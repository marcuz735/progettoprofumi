<?php
session_start();

// Verifica login
if (!isset($_SESSION['utente_id'])) {
    header("Location: accesso.html");
    exit;
}

$utente_id = $_SESSION['utente_id'];

// Connessione DB
$conn = new mysqli('localhost', 'root', 'root', 'ouparfum');
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
    <div class="carrello-container">
      <form method="post" class="carrello-actions">
        <button type="submit" name="svuota_carrello" class="btn-danger">🗑️ Svuota Carrello</button>
      </form>

      <?php $totale = 0; ?>
      <?php foreach ($prodotti as $prodotto): ?>
        <?php
          $sub = $prodotto['prezzo'] * $prodotto['quantita'];
          $totale += $sub;
        ?>
        <div class="carrello-item">
          <img src="<?= htmlspecialchars($prodotto['percorso']) ?>" alt="" class="img-mini">

          <div class="carrello-details">
            <h4><?= htmlspecialchars($prodotto['nome']) ?></h4>
            <div class="prezzo-singolo">€<?= number_format($prodotto['prezzo'], 2) ?></div>

            <form method="post" class="update-form">
              <input type="hidden" name="id_prodotto" value="<?= $prodotto['id'] ?>">
              <div class="quantita-controls">
                <input type="number" name="quantita" value="<?= $prodotto['quantita'] ?>" min="1">
                <button type="submit" name="update_qty" class="btn-refresh">⟳</button>
              </div>
            </form>

            <div class="subtotale">
              Totale: <strong>€<?= number_format($sub, 2) ?></strong>
            </div>
          </div>

          <form method="post" class="remove-form">
            <input type="hidden" name="id_prodotto" value="<?= $prodotto['id'] ?>">
            <button type="submit" name="remove" class="btn-danger">❌</button>
          </form>
        </div>
      <?php endforeach; ?>

      <div class="carrello-footer">
        <form action="conferma_ordine.php" method="post">
          <button type="submit" class="btn-conferma">Conferma Ordine</button>
        </form>
        <p class="totale-final">
          <strong>Totale:</strong> €<?= number_format($totale, 2) ?>
        </p>
      </div>
    </div>
  <?php endif; ?>
</main>
</body>
</html>

<?php $conn->close(); ?>
