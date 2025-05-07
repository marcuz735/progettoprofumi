<?php
session_start();
if (!isset($_SESSION['utente'])) {
  header("Location: accesso.html");
  exit();
}

$errore_pw = "";
$messaggio_mail = "";
$messaggio_pw = "";

$conn = new mysqli("localhost", "root", "vc-mob2-22", "tpsi 5bi");
if ($conn->connect_error) {
  die("Connessione fallita: " . $conn->connect_error);
}

$mail = $_SESSION['utente'];
$result = $conn->query("SELECT * FROM utenti WHERE mail = '$mail'");
$utente = $result->fetch_assoc();
$id_utente = $utente['id'];

// CAMBIO MAIL
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nuova_mail'])) {
  $nuova_mail = $conn->real_escape_string($_POST['nuova_mail']);
  $conn->query("UPDATE utenti SET mail = '$nuova_mail' WHERE id = $id_utente");
  $_SESSION['utente'] = $nuova_mail;
  $messaggio_mail = "Email aggiornata con successo!";
}

// CAMBIO PASSWORD
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['vecchia_pw']) && isset($_POST['nuova_pw'])) {
  $vecchia = $_POST['vecchia_pw'];
  $nuova = password_hash($_POST['nuova_pw'], PASSWORD_DEFAULT);

  $result = $conn->query("SELECT pw FROM utenti WHERE id = $id_utente");
  $dati = $result->fetch_assoc();
  if (!password_verify($vecchia, $dati['pw'])) {
    $errore_pw = "La vecchia password è errata.";
  } else {
    $conn->query("UPDATE utenti SET pw = '$nuova' WHERE id = $id_utente");
    $messaggio_pw = "Password aggiornata con successo!";
  }
}

// STORICO ORDINI
$storico = $conn->query("SELECT * FROM ordini WHERE id_utente = $id_utente AND stato = 'o' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Il tuo account</title>
  <link rel="stylesheet" href="stilehom.css">
  <style>
    .form-container {
      max-width: 500px;
      margin: 30px auto;
      padding: 20px;
      background-color: #f9f9f9;
      border-radius: 12px;
      box-shadow: 0 0 10px #ccc;
    }
    .form-container h2 {
      text-align: center;
    }
    .form-container form {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }
    .form-container input[type="email"],
    .form-container input[type="password"] {
      padding: 8px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    .form-container button {
      padding: 10px;
      background-color: #c62828;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    .form-container .errore {
      color: red;
      text-align: center;
    }
    .form-container .successo {
      color: green;
      text-align: center;
    }
    .storico-container {
      max-width: 700px;
      margin: 40px auto;
    }
    .storico-container table {
      width: 100%;
      border-collapse: collapse;
    }
    .storico-container th, .storico-container td {
      border: 1px solid #ccc;
      padding: 10px;
      text-align: center;
    }
    .logout-link {
      text-align: center;
      margin-top: 30px;
    }
  </style>
</head>
<body>
  <header class="site-header">
    <div class="header-left">
      <img src="immagini/logo.png" alt="Logo OuParfume" class="logo"> 
    </div>
    <nav class="main-nav">
      <ul>
        <li><a href="homepage.php">Home</a></li>
        <li><a href="prodotti.html">I Nostri Prodotti</a></li>
        <li><a href="contatti.html">Contatti</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <div class="form-container">
      <h2>Ciao, <?php echo htmlspecialchars($mail); ?></h2>

      <!-- Modifica email -->
      <form method="POST">
        <label for="nuova_mail">Cambia email:</label>
        <input type="email" name="nuova_mail" required>
        <button type="submit">Aggiorna Email</button>
        <?php if ($messaggio_mail): ?>
          <div class="successo"><?php echo $messaggio_mail; ?></div>
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
          <div class="errore"><?php echo $errore_pw; ?></div>
        <?php elseif ($messaggio_pw): ?>
          <div class="successo"><?php echo $messaggio_pw; ?></div>
        <?php endif; ?>
      </form>
    </div>

    <!-- Storico Ordini -->
    <div class="storico-container">
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
        <p style="text-align:center;">Nessun ordine effettuato.</p>
      <?php endif; ?>
    </div>

    <!-- Logout -->
    <div class="logout-link">
      <a href="logout.php">Logout</a>
    </div>
  </main>
</body>
</html>
