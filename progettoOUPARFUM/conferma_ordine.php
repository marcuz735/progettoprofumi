<?php
session_start();

// Controllo se l'utente è loggato
if (!isset($_SESSION['utente_id'])) {
    header("Location: accesso.html");
    exit();
}

$host = "localhost";
$user = "root";
$pass = "root";
$db = "ouparfum";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$utente_id = $_SESSION['utente_id'];

// Trova l'ordine con stato 'c' (carrello attivo)
$query_ordine = "SELECT * FROM ordini WHERE id_utente = ? AND stato = 'c' LIMIT 1";
$stmt_ordine = $conn->prepare($query_ordine);
$stmt_ordine->bind_param("i", $utente_id);
$stmt_ordine->execute();
$result_ordine = $stmt_ordine->get_result();
$ordine = $result_ordine->fetch_assoc();

if (!$ordine) {
    echo "Nessun carrello attivo trovato.";
    exit();
}

$ordine_id = $ordine['id'];
$errori = [];

// Se il form è stato inviato
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $citta = trim($_POST["citta"]);
    $cap = trim($_POST["cap"]);
    $provincia = trim($_POST["provincia"]);
    $via = trim($_POST["via"]);
    $civico = trim($_POST["civico"]);
    $data = date('Y-m-d');

    // Validazioni
    if (strlen($provincia) !== 2) {
        $errori['provincia'] = "La provincia deve essere composta da 2 lettere.";
    }

    if (!preg_match('/^\d{5}$/', $cap)) {
        $errori['cap'] = "Il CAP deve contenere esattamente 5 cifre numeriche.";
    }

    if (!preg_match('/\d/', $civico)) {
        $errori['civico'] = "Numero civico non valido";
    }

    if (empty($errori)) {
        // Aggiorna ordine esistente
        $update_query = "UPDATE ordini SET citta = ?, cap = ?, provincia = ?, via = ?, civico = ?, stato = 'o', data_ordine = ? WHERE id = ?";
        $stmt_update = $conn->prepare($update_query);
        $stmt_update->bind_param("ssssssi", $citta, $cap, $provincia, $via, $civico, $data, $ordine_id);
        $stmt_update->execute();

        // Crea nuovo carrello
        $insert_query = "INSERT INTO ordini (citta, cap, provincia, via, civico, stato, id_utente) VALUES (NULL, NULL, NULL, NULL, NULL, 'c', ?)";
        $stmt_insert = $conn->prepare($insert_query);
        $stmt_insert->bind_param("i", $utente_id);
        $stmt_insert->execute();

        header("Location: confermato.html");
        exit();
    }
}

// Preleva i dettagli dell’ordine per il riepilogo
$query_dettagli = "
    SELECT p.nome, p.prezzo, d.quantita
    FROM dettagli d
    JOIN prodotti p ON d.id_prodotto = p.id
    WHERE d.id_ordine = ?
";
$stmt_dettagli = $conn->prepare($query_dettagli);
$stmt_dettagli->bind_param("i", $ordine_id);
$stmt_dettagli->execute();
$result_dettagli = $stmt_dettagli->get_result();

$prodotti = [];
$totale = 0;
while ($row = $result_dettagli->fetch_assoc()) {
    $prodotti[] = $row;
    $totale += $row['prezzo'] * $row['quantita'];
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Conferma Ordine</title>
    <link rel="stylesheet" href="stilehom.css">
</head>
<body>
  
  <style>
    .errore-campo {
      color: red ;
      font-size: 0.9em;
      margin-top: 4px;
    }

    label {
      display: block;
      margin-bottom: 10px;
    }

    input {
      display: block;
      margin-top: 4px;
    }
  </style>

<div class="conferma-ordine-container">
    <h1>Conferma Ordine</h1>

    <h2>Riepilogo Carrello</h2>
    <ul>
        <?php foreach ($prodotti as $p): ?>
            <li>
                <?= htmlspecialchars($p['nome']) ?>
                <span>€<?= number_format($p['prezzo'], 2) ?> x <?= $p['quantita'] ?></span>
            </li>
        <?php endforeach; ?>
    </ul>

    <p>
        <span>Totale:</span>
        <span>€<?= number_format($totale, 2) ?></span>
    </p>

    <h2>Inserisci Indirizzo di Spedizione</h2>
    <form method="post" action="">
        <label>Città:
            <input type="text" name="citta" required value="<?= htmlspecialchars($_POST['citta'] ?? '') ?>">
        </label>

        <label>CAP:
            <input type="text" maxlength="5" name="cap" required value="<?= htmlspecialchars($_POST['cap'] ?? '') ?>">
            <?php if (isset($errori['cap'])): ?>
                <div class="errore-campo"><?= $errori['cap'] ?></div>
            <?php endif; ?>
        </label>

        <label>Provincia:
            <input type="text" maxlength="2" name="provincia" required value="<?= htmlspecialchars($_POST['provincia'] ?? '') ?>">
            <?php if (isset($errori['provincia'])): ?>
                <div class="errore-campo"><?= $errori['provincia'] ?></div>
            <?php endif; ?>
        </label>

        <label>Via:
            <input type="text" name="via" required value="<?= htmlspecialchars($_POST['via'] ?? '') ?>">
        </label>

        <label>Civico:
            <input type="text" name="civico" required value="<?= htmlspecialchars($_POST['civico'] ?? '') ?>">
            <?php if (isset($errori['civico'])): ?>
                <div class="errore-campo"><?= $errori['civico'] ?></div>
            <?php endif; ?>
        </label>

        <button type="submit" class="btn-conferma">Conferma Ordine</button>
    </form>
</div>
</body>
</html>