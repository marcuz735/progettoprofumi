<?php
session_start();

// Controllo se l'utente è loggato
if (!isset($_SESSION['utente_id'])) {
    header("Location: accesso.html");
    exit();
}

$host = "localhost";
$user = "root";
$pass = "vc-mob2-22";
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

$errore = "";  // variabile per messaggi d'errore

// Se l'utente ha confermato il form
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["citta"])) {
    $citta = trim($_POST["citta"]);
    $cap = trim($_POST["cap"]);
    $provincia = trim($_POST["provincia"]);
    $via = trim($_POST["via"]);
    $civico = trim($_POST["civico"]);
    $data = date('Y-m-d');

    // Controllo lunghezza provincia (deve essere 2)
    if (strlen($provincia) !== 2) {
        $errore = "La provincia deve essere lunga esattamente 2 caratteri.";
    }
    // Controllo lunghezza CAP (deve essere 5)
    else if (strlen($cap) !== 5) {
        $errore = "Il CAP deve essere lungo esattamente 5 caratteri.";
    }

    if ($errore === "") {
        // Aggiorna ordine esistente
        $update_query = "UPDATE ordini SET citta = ?, cap = ?, provincia = ?, via = ?, civico = ?, stato = 'o', data_ordine = ? WHERE id = ?";
        $stmt_update = $conn->prepare($update_query);
        $stmt_update->bind_param("ssssssi", $citta, $cap, $provincia, $via, $civico, $data, $ordine_id);
        $stmt_update->execute();

        // Crea nuovo ordine (nuovo carrello)
        $insert_query = "INSERT INTO ordini (citta, cap, provincia, via, civico, stato, id_utente) VALUES (NULL, NULL, NULL, NULL, NULL, 'c', ?)";
        $stmt_insert = $conn->prepare($insert_query);
        $stmt_insert->bind_param("i", $utente_id);
        $stmt_insert->execute();

        // Redirect alla pagina conferma
        header("Location: confermato.html");
        exit();
    }
}

// Preleva i dettagli dell’ordine per riepilogo
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
    <h1>Conferma Ordine</h1>

    <h2>Riepilogo Carrello</h2>
    <ul>
        <?php foreach ($prodotti as $p): ?>
            <li><?= htmlspecialchars($p['nome']) ?> - €<?= number_format($p['prezzo'], 2) ?> x <?= $p['quantita'] ?></li>
        <?php endforeach; ?>
    </ul>
    <p><strong>Totale:</strong> €<?= number_format($totale, 2) ?></p>

    <?php if ($errore): ?>
        <p style="color: red; font-weight: bold;"><?= htmlspecialchars($errore) ?></p>
    <?php endif; ?>

    <h2>Inserisci Indirizzo di Spedizione</h2>
    <form method="post" action="">
        <label>Città: <input type="text" name="citta" required></label><br>
        <label>CAP: <input type="text" name="cap" required maxlength="5"></label><br>
        <label>Provincia: <input type="text" name="provincia" required maxlength="2"></label><br>
        <label>Via: <input type="text" name="via" required></label><br>
        <label>Civico: <input type="text" name="civico" required></label><br>
        <button type="submit">Conferma Ordine</button>
    </form>
</body>
</html>
