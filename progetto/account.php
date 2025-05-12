<?php
session_start();

// Controllo se la sessione è attiva e se l'utente è loggato
if (!isset($_SESSION['utente_mail'])) {
    // Se la sessione non è attiva, reindirizza all'accesso
    header("Location: accesso.html");
    exit();
}

// Inizializzo variabili per gli eventuali messaggi di errore e successo
$errore_pw = "";
$messaggio_mail = "";
$messaggio_pw = "";

// Connessione al database
$conn = new mysqli("localhost", "root", "vc-mob2-22", "ouparfum");

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Recupera l'email dell'utente dalla sessione
$utente_mail = $_SESSION['utente_mail'];

// Prepara la query per ottenere i dettagli dell'utente
$stmt = $conn->prepare("SELECT id, mail, pw FROM utenti WHERE mail = ?");
$stmt->bind_param("s", $utente_mail);
$stmt->execute();
$stmt->bind_result($id_utente, $email_db, $password_db);
$stmt->fetch();
$stmt->close();

// Se l'utente non è trovato nel DB, forza il logout
if (!$id_utente) {
    session_destroy();
    header("Location: accesso.html");
    exit();
}

// Modifica email
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nuova_mail'])) {
    $nuova_mail = $conn->real_escape_string($_POST['nuova_mail']);

    // Aggiorna l'email nel database
    $stmt = $conn->prepare("UPDATE utenti SET mail = ? WHERE id = ?");
    $stmt->bind_param("si", $nuova_mail, $id_utente);
    if ($stmt->execute()) {
        // Aggiorna la sessione con la nuova email
        $_SESSION['utente_mail'] = $nuova_mail;
        $messaggio_mail = "Email aggiornata con successo!";
    } else {
        $messaggio_mail = "Errore durante l'aggiornamento dell'email.";
    }
    $stmt->close();
}

// Modifica password
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['vecchia_pw']) && isset($_POST['nuova_pw'])) {
    $vecchia_pw = $_POST['vecchia_pw'];
    $nuova_pw = password_hash($_POST['nuova_pw'], PASSWORD_DEFAULT);

    // Verifica se la vecchia password è corretta
    if (!password_verify($vecchia_pw, $password_db)) {
        $errore_pw = "La vecchia password è errata.";
    } else {
        // Aggiorna la password nel database
        $stmt = $conn->prepare("UPDATE utenti SET pw = ? WHERE id = ?");
        $stmt->bind_param("si", $nuova_pw, $id_utente);
        if ($stmt->execute()) {
            $messaggio_pw = "Password aggiornata con successo!";
        } else {
            $errore_pw = "Errore durante l'aggiornamento della password.";
        }
        $stmt->close();
    }
}

// Recupero storico ordini dell'utente
$storico = $conn->query("SELECT * FROM ordini WHERE id_utente = $id_utente ORDER BY id DESC");

$conn->close();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Il tuo account</title>
    <link rel="stylesheet" href="stilehom.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="homepage.html">Home</a></li>
                <li><a href="prodotti.php">I Nostri Prodotti</a></li>
                <li><a href="contatti.html">Contatti</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Ciao, <?php echo htmlspecialchars($utente_mail); ?></h2>

        <!-- Modifica email -->
        <form method="POST">
            <label for="nuova_mail">Cambia email:</label>
            <input type="email" name="nuova_mail" required>
            <button type="submit">Aggiorna Email</button>
            <?php if ($messaggio_mail): ?>
                <div><?php echo $messaggio_mail; ?></div>
            <?php endif; ?>
        </form>

        <!-- Modifica password -->
        <form method="POST">
            <label for="vecchia_pw">Vecchia password:</label>
            <input type="password" name="vecchia_pw" required>
            <label for="nuova_pw">Nuova password:</label>
            <input type="password" name="nuova_pw" required>
            <button type="submit">Aggiorna Password</button>
            <?php if ($errore_pw): ?>
                <div><?php echo $errore_pw; ?></div>
            <?php elseif ($messaggio_pw): ?>
                <div><?php echo $messaggio_pw; ?></div>
            <?php endif; ?>
        </form>

        <!-- Storico ordini -->
        <h2>Storico Ordini</h2>
        <?php if ($storico->num_rows > 0): ?>
            <table>
                <tr>
                    <th>ID Ordine</th>
                    <th>Indirizzo</th>
                    <th>CAP</th>
                    <th>Provincia</th>
                    <th>Stato</th>
                </tr>
                <?php while ($ordine = $storico->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $ordine['id']; ?></td>
                        <td><?php echo $ordine['via'] . ' ' . $ordine['civico'] . ', ' . $ordine['citta']; ?></td>
                        <td><?php echo $ordine['cap']; ?></td>
                        <td><?php echo $ordine['provincia']; ?></td>
                        <td><?php echo ($ordine['stato'] === 'o') ? 'Ordinato' : 'In Carrello'; ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>Nessun ordine effettuato.</p>
        <?php endif; ?>

        <!-- Logout -->
        <a href="logout.php">Logout</a>
    </main>
</body>
</html>
