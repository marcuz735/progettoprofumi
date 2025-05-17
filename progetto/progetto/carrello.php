<?php
session_start();

// Verifica login
if (!isset($_SESSION['utente_id'])) {
    header("Location: accesso.html");
    exit;
}

$utente_id = $_SESSION['utente_id'];

// Connessione DB
$conn = new mysqli('localhost', 'root', 'vc-mob2-22', 'ouparfum');
if ($conn->connect_error) die("Errore connessione: " . $conn->connect_error);

// Recupera ordine aperto
$stmt = $conn->prepare("SELECT * FROM ordini WHERE id_utente = ? AND stato = 'c' LIMIT 1");
$stmt->bind_param("i", $utente_id);
$stmt->execute();
$result = $stmt->get_result();

$id_ordine = null;
if ($row = $result->fetch_assoc()) {
    $id_ordine = $row['id'];
} else {
    // Nessun carrello aperto
    $id_ordine = null;
}

### GESTIONE POST ###
if ($_SERVER["REQUEST_METHOD"] === "POST" && $id_ordine !== null) {
    // Modifica quantità
    if (isset($_POST['update_qty']) && is_numeric($_POST['id_prodotto']) && is_numeric($_POST['quantita'])) {
        $id_prodotto = $_POST['id_prodotto'];
        $quantita = max(1, intval($_POST['quantita'])); // minimo 1
        $updateStmt = $conn->prepare("UPDATE dettagli SET quantita = ? WHERE id_ordine = ? AND id_prodotto = ?");
        $updateStmt->bind_param("iii", $quantita, $id_ordine, $id_prodotto);
        $updateStmt->execute();
    }

    // Rimuovi singolo prodotto
    if (isset($_POST['remove']) && is_numeric($_POST['id_prodotto'])) {
        $id_prodotto = $_POST['id_prodotto'];
        $removeStmt = $conn->prepare("DELETE FROM dettagli WHERE id_ordine = ? AND id_prodotto = ?");
        $removeStmt->bind_param("ii", $id_ordine, $id_prodotto);
        $removeStmt->execute();
    }

    // Svuota carrello
    if (isset($_POST['svuota_carrello'])) {
        $deleteStmt = $conn->prepare("DELETE FROM dettagli WHERE id_ordine = ?");
        $deleteStmt->bind_param("i", $id_ordine);
        $deleteStmt->execute();
    }

    // Dopo ogni POST, ricarica per aggiornare
    header("Location: carrello.php");
    exit;
}

// Recupera prodotti del carrello
$prodotti = [];
if ($id_ordine !== null) {
    $sql = "SELECT p.id, p.nome, p.prezzo, p.percorso, d.quantita
            FROM dettagli d
            JOIN prodotti p ON d.id_prodotto = p.id
            WHERE d.id_ordine = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_ordine);
    $stmt->execute();
    $result = $stmt->get_result();
    $prodotti = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Carrello - OuParfum</title>
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
        <a href="account_redirect.php" class="user-icon">👤</a>
    </div>
</header>

<main class="carrello-main">
    <h2>Il Tuo Carrello</h2>

    <?php if (empty($prodotti)): ?>
        <p>Il tuo carrello è vuoto.</p>
    <?php else: ?>
        <form method="post">
            <button type="submit" name="svuota_carrello" class="btn-danger">Svuota Carrello</button>
        </form>
        <table class="carrello-tabella">
            <thead>
                <tr>
                    <th>Prodotto</th>
                    <th>Prezzo</th>
                    <th>Quantità</th>
                    <th>Totale</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php $totale = 0; ?>
                <?php foreach ($prodotti as $prodotto): ?>
                    <?php $sub = $prodotto['prezzo'] * $prodotto['quantita']; $totale += $sub; ?>
                    <tr>
                        <td>
                            <img src="<?= htmlspecialchars($prodotto['percorso']) ?>" alt="" class="img-mini">
                            <?= htmlspecialchars($prodotto['nome']) ?>
                        </td>
                        <td>€<?= number_format($prodotto['prezzo'], 2) ?></td>
                        <td>
                            <form method="post" class="update-form">
                                <input type="hidden" name="id_prodotto" value="<?= $prodotto['id'] ?>">
                                <input type="number" name="quantita" value="<?= $prodotto['quantita'] ?>" min="1" style="width: 50px;">
                                <button type="submit" name="update_qty">Aggiorna</button>
                            </form>
                        </td>
                        <td>€<?= number_format($sub, 2) ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="id_prodotto" value="<?= $prodotto['id'] ?>">
                                <button type="submit" name="remove" class="btn-danger">Rimuovi</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="carrello-totale">
            <p><strong>Totale:</strong> €<?= number_format($totale, 2) ?></p>
            <form action="conferma_ordine.php" method="post">
                <button type="submit">Conferma Ordine</button>
            </form>
        </div>
    <?php endif; ?>
</main>

<footer class="footer" id="bottom">
    <div class="footer-container">
        <div class="footer-info">
            <h4>OuParfum</h4>
            <p class="footer-description">Scopri la tua essenza<br>Vivi il tuo profumo<br>Esprimi la tua personalità</p>
        </div>
        <div class="footer-contact">
            <h4>Contatti</h4>
            <p><strong>Email:</strong> support@ouparfum.com</p>
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

<?php $conn->close(); ?>
