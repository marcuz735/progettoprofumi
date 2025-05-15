<?php
session_start();

// Verifica se l'utente è loggato
if (!isset($_SESSION['utente_id'])) {
    header("Location: accesso.html");
    exit();
}

$utente_id = $_SESSION['utente_id'];

// Connessione al database
$conn = new mysqli("localhost", "root", "vc-mob2-22", "ouparfum");
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Recupera il carrello attivo (stato 'c')
$query_carrello = "SELECT * FROM ordini WHERE id_utente = ? AND stato = 'c' LIMIT 1";
$stmt = $conn->prepare($query_carrello);
$stmt->bind_param("i", $utente_id);
$stmt->execute();
$result = $stmt->get_result();
$carrello = $result->fetch_assoc();

if (!$carrello) {
    echo "<p>Nessun carrello attivo trovato.</p>";
    exit();
}

$ordine_id = $carrello['id'];

// Recupera i dettagli del carrello
$query_dettagli = "
    SELECT d.quantita, p.nome, p.prezzo 
    FROM dettagli d 
    JOIN prodotti p ON d.id_prodotto = p.id 
    WHERE d.id_ordine = ?";
$stmt = $conn->prepare($query_dettagli);
$stmt->bind_param("i", $ordine_id);
$stmt->execute();
$dettagli_result = $stmt->get_result();

// Totale
$totale = 0;
$prodotti = [];

while ($row = $dettagli_result->fetch_assoc()) {
    $row['subtotale'] = $row['prezzo'] * $row['quantita'];
    $totale += $row['subtotale'];
    $prodotti[] = $row;
}

// Se è stato inviato il form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $citta = $_POST['citta'];
    $cap = $_POST['cap'];
    $provincia = $_POST['provincia'];
    $via = $_POST['via'];
    $civico = $_POST['civico'];

    // Aggiorna l’ordine attuale
    $update_query = "
        UPDATE ordini 
        SET stato = 'o', citta = ?, cap = ?, provincia = ?, via = ?, civico = ? 
        WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sisssi", $citta, $cap, $provincia, $via, $civico, $ordine_id);
    $stmt->execute();

    // Crea un nuovo carrello (stato 'c')
    $insert_query = "INSERT INTO ordini (id_utente, stato) VALUES (?, 'c')";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("i", $utente_id);
    $stmt->execute();

    // Reindirizza alla pagina di conferma
    header("Location: confermato.html");
    exit();
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
    <header class="site-header">
        <h1>Conferma Ordine</h1>
    </header>

    <main class="order-summary">
        <h2>Riepilogo Carrello</h2>
        <table>
            <tr>
                <th>Prodotto</th>
                <th>Quantità</th>
                <th>Prezzo</th>
                <th>Subtotale</th>
            </tr>
            <?php foreach ($prodotti as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['nome']) ?></td>
                    <td><?= $p['quantita'] ?></td>
                    <td>€<?= number_format($p['prezzo'], 2) ?></td>
                    <td>€<?= number_format($p['subtotale'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3"><strong>Totale</strong></td>
                <td><strong>€<?= number_format($totale, 2) ?></strong></td>
            </tr>
        </table>

        <h2>Inserisci Indirizzo di Spedizione</h2>
        <form method="POST" action="">
            <label for="citta">Città:</label>
            <input type="text" name="citta" required>

            <label for="cap">CAP:</label>
            <input type="number" name="cap" required>

            <label for="provincia">Provincia:</label>
            <input type="text" name="provincia" required>

            <label for="via">Via:</label>
            <input type="text" name="via" required>

            <label for="civico">Civico:</label>
            <input type="text" name="civico" required>

            <button type="submit">Conferma Ordine</button>
        </form>
    </main>

    <footer class="footer">
        <p>&copy; 2025 OuParfume - Tutti i diritti riservati</p>
    </footer>
</body>
</html>
