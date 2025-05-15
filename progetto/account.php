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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profumi di Classe - Home</title>
  <link rel="stylesheet" href="stilehom.css"> <!-- Collega il file CSS -->

</head>
<body id="top">
  <header class="site-header">
    <div class="header-left">
      <img src="immagini/logo.png" alt="Logo OuParfume" class="logo"> 
    </div>
    <nav class="main-nav">
      <ul>
        <li><a href="homepage.html">Home</a></li>
        <li><a href="prodotti.php">I Nostri Prodotti</a></li>
        <li><a href="#bottom">Contatti</a></li>
      </ul>
    </nav>
    <!-- Icone come link -->
    <div class="header-icons">
    <a href="carrello.php" class="cart-icon">
      🛒
    </a>
    <a href="accesso.html" class="user-icon">
      👤
    </a></div>
  </header>

  <main class="login-main">
  <div class="login-form-container">
    <h2 class="login-title">Benvenuto, <?php echo htmlspecialchars($utente_mail); ?></h2>

    <!-- Modifica email -->
    <form method="POST" class="login-form">
      <label for="nuova_mail">Nuova email</label>
      <input type="email" name="nuova_mail" required>
      <button type="submit">Aggiorna Email</button>
      <?php if ($messaggio_mail): ?>
        <p class="form-message"><?php echo $messaggio_mail; ?></p>
      <?php endif; ?>
    </form>

    <!-- Modifica password -->
    <form method="POST" class="login-form">
      <label for="vecchia_pw">Vecchia password</label>
      <input type="password" name="vecchia_pw" required>
      <label for="nuova_pw">Nuova password</label>
      <input type="password" name="nuova_pw" required>
      <button type="submit">Aggiorna Password</button>
      <?php if ($errore_pw): ?>
        <p class="form-error"><?php echo $errore_pw; ?></p>
      <?php elseif ($messaggio_pw): ?>
        <p class="form-message"><?php echo $messaggio_pw; ?></p>
      <?php endif; ?>
    </form>

    <!-- Storico ordini -->
    <h3 class="login-subtitle">Storico Ordini</h3>
    <?php if ($storico->num_rows > 0): ?>
      <div class="orders-box">
        <table class="orders-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Indirizzo</th>
              <th>CAP</th>
              <th>Provincia</th>
              <th>Stato</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($ordine = $storico->fetch_assoc()): ?>
              <tr>
                <td><?php echo $ordine['id']; ?></td>
                <td><?php echo $ordine['via'] . ' ' . $ordine['civico'] . ', ' . $ordine['citta']; ?></td>
                <td><?php echo $ordine['cap']; ?></td>
                <td><?php echo $ordine['provincia']; ?></td>
                <td><?php echo ($ordine['stato'] === 'o') ? 'Ordinato' : 'In Carrello'; ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p class="form-message">Nessun ordine effettuato.</p>
    <?php endif; ?>

    <!-- Logout -->
    <div class="logout-container">
      <a href="logout.php" class="logout-button">Logout</a>
    </div>
  </div>
</main>

    <footer class="footer" id="bottom">
      <div class="footer-container">
        <!-- Sezione informazioni -->
        <div class="footer-info">
          <h4>OuParfume</h4>
          <p class="footer-description">
            Scopri la tua essenza<br>Vivi il tuo profumo<br>Esprimi la tua personalità</p>
        </div>
    
        <!-- Sezione contatti -->
        <div class="footer-contact">
          <h4>Contatti</h4>
          <p><strong>Email:</strong> support@auparfume.com</p>
          <p><strong>Telefono:</strong> +39 0123 456789</p>
          <p><strong>Indirizzo:</strong> Via Roma, 123, Milano, Italia</p>
        </div>
      </div>
    
      <!-- Sezione "Torna su" sotto AuParfume -->
      <div class="footer-back-to-top">
        <a href="#top" class="back-to-top-link">Torna su</a>
      </div>
    
      <!-- Copyright -->
      <div class="footer-bottom">
        <p>&copy; 2025 OuParfume - Tutti i diritti riservati</p>
      </div>
    </footer>
</body>
</html>