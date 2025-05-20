<?php
session_start();
if (!isset($_SESSION['utente_id'])) {
    header("Location: accesso.html");
    exit();
}

$utente_id = $_SESSION['utente_id'];
$conn = new mysqli("localhost", "root", "vc-mob2-22", "ouparfum");
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$messaggio_email = "";
$messaggio_password = "";

// Cambio email
if (isset($_POST['cambia_email'])) {
    $nuova_email = $_POST['nuova_email'];
    if (!filter_var($nuova_email, FILTER_VALIDATE_EMAIL)) {
        $messaggio_email = "<p ='color: red;style'>Formato email non valido.</p>";
    } else {
        $conn->query("UPDATE utenti SET mail = '$nuova_email' WHERE id = $utente_id");
        $messaggio_email = "<p style='color: green;'>Email aggiornata con successo.</p>";
    }
}

// Cambio password con conferma e controlli
if (isset($_POST['cambia_password'])) {
    $vecchia_password = $_POST['vecchia_password'];
    $nuova_password = $_POST['nuova_password'];
    $conferma_password = $_POST['conferma_password'];

    if ($nuova_password !== $conferma_password) {
        $messaggio_password = "<p style='color: red;'>Le nuove password non coincidono.</p>";
    } elseif (strlen($nuova_password) < 6) {
        $messaggio_password = "<p style='color: red;'>La nuova password deve contenere almeno 6 caratteri.</p>";
    } else {
        $result_pw = $conn->query("SELECT pw FROM utenti WHERE id = $utente_id");
        if ($result_pw && $row_pw = $result_pw->fetch_assoc()) {
            if (password_verify($vecchia_password, $row_pw['pw'])) {
                $nuova_password_hash = password_hash($nuova_password, PASSWORD_DEFAULT);
                $conn->query("UPDATE utenti SET pw = '$nuova_password_hash' WHERE id = $utente_id");
                $messaggio_password = "<p style='color: green;'>Password aggiornata con successo.</p>";
            } else {
                $messaggio_password = "<p style='color: red;'>La vecchia password non è corretta.</p>";
            }
        }
    }
}


// Storico ordini completati
$query = "
    SELECT 
        o.id AS ordine_id, o.via, o.civico, o.citta, o.cap, o.provincia, o.data_ordine,
        p.nome AS prodotto_nome, p.percorso, p.prezzo, d.quantita
    FROM ordini o
    JOIN dettagli d ON o.id = d.id_ordine
    JOIN prodotti p ON d.id_prodotto = p.id
    WHERE o.id_utente = $utente_id AND o.stato = 'o'
    ORDER BY o.data_ordine DESC
";

$result = $conn->query($query);

// Raggruppamento ordini per ID
$ordini = [];
while ($row = $result->fetch_assoc()) {
    $id = $row['ordine_id'];
    if (!isset($ordini[$id])) {
        $ordini[$id] = [
            'indirizzo' => "{$row['via']}, {$row['civico']}, {$row['cap']} {$row['citta']} ({$row['provincia']})",
            'data' => $row['data_ordine'],
            'prodotti' => [],
            'totale' => 0
        ];
    }
    $ordini[$id]['prodotti'][] = [
        'nome' => $row['prodotto_nome'],
        'percorso' => $row['percorso'],
        'prezzo' => $row['prezzo'],
        'quantita' => $row['quantita']
    ];
    $ordini[$id]['totale'] += $row['prezzo'] * $row['quantita'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>OuParfum - Account</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" href="stilehom.css">
</head>
<body>
    <header class="site-header">
        <div class="header-left">
            <img src="immagini/logo.png" alt="Logo OuParfum" class="logo"> 
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="homepage.php">Home</a></li>
                <li><a href="prodotti.php">I Nostri Prodotti</a></li>
                <li><a href="#bottom">Contatti</a></li>
            </ul>
        </nav>
        <div class="header-icons">
            <a href="carrello.php" class="cart-icon">🛒</a>
            <a href="account.php" class="user-icon">👤</a>
        </div>
    </header>

    <main class="form-container">

    <h2>Modifica Account</h2>

    <h3>Cambia Email</h3>
    <form method="post">
        <label>Nuova Email:</label>
        <input type="email" name="nuova_email" required>
        <?php if (!empty($messaggio_email)) echo $messaggio_email; ?>
        <button type="submit" name="cambia_email">Aggiorna Email</button>
    </form>

    <h3>Cambia Password</h3>
    <form method="post">
        <label>Vecchia Password:</label>
        <input type="password" name="vecchia_password" required>

        <label>Nuova Password:</label>
        <input type="password" name="nuova_password" required>

        <label>Conferma Nuova Password:</label>
        <input type="password" name="conferma_password" required>

        <?php if (!empty($messaggio_password)) echo $messaggio_password; ?>
        <button type="submit" name="cambia_password">Cambia Password</button>
    </form>

    <h3 class="login-subtitle">Storico Ordini</h3>

    <div class="orders-box">
        <?php if (empty($ordini)): ?>
            <p class="form-message">Non hai ancora effettuato ordini.</p>
        <?php else: ?>
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>ID Ordine</th>
                        <th>Data</th>
                        <th>Indirizzo</th>
                        <th>Prodotti</th>
                        <th>Totale</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ordini as $ordine_id => $ordine): ?>
                        <tr>
                            <td>#<?= $ordine_id ?></td>
                            <td><?= date("d/m/Y", strtotime($ordine['data'])) ?></td>
                            <td><?= $ordine['indirizzo'] ?></td>
                            <td>
                                <?php foreach ($ordine['prodotti'] as $prodotto): ?>
                                    <div>
                                        <?= $prodotto['nome'] ?> x<?= $prodotto['quantita'] ?> (€<?= number_format($prodotto['prezzo'], 2) ?>)
                                    </div>
                                <?php endforeach; ?>
                            </td>
                            <td>€<?= number_format($ordine['totale'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="logout-container">
        <form action="logout.php" method="post">
            <button type="submit" class="logout-button">Logout</button>
        </form>
    </div>
</main>


    <footer class="footer" id="bottom">
        <div class="footer-container">
            <div class="footer-info">
                <h4>OuParfum</h4>
                <p class="footer-description">
                    Scopri la tua essenza<br>Vivi il tuo profumo<br>Esprimi la tua personalità
                </p>
            </div>
            <div class="footer-contact">
                <h4>Contatti</h4>
                <p><strong>Email:</strong> support@auparfume.com</p>
                <p><strong>Telefono:</strong> +39 0123 456789</p>
                <p><strong>Indirizzo:</strong> Via Roma, 123, Milano, Italia</p>
            </div>
        </div>
        <div class="footer-back-to-top">
            <a href="#top" class="back-to-top-link">Torna su</a>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 OuParfum - Tutti i diritti riservati</p>
        </div>
    </footer>
</body>
</html>