<?php
session_start();

// Connessione al DB
$host = 'localhost';
$db = 'ouparfum';
$user = 'root';
$pass = 'root';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Recupera ID prodotto dall'URL
$id_prodotto = isset($_GET['product']) ? intval($_GET['product']) : 0;

// Recupera informazioni sul prodotto
$sql = "SELECT * FROM prodotti WHERE id = $id_prodotto";
$result = $conn->query($sql);
$prodotto = $result->fetch_assoc();

// Gestione aggiunta al carrello
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION["utente_id"])) {
    $utente_id = $_SESSION["utente_id"];

    // Cerca se esiste un ordine aperto (carrello) per l'utente
    $sql_check = "SELECT id FROM ordini WHERE id_utente = ? AND stato = 'c' LIMIT 1";
    $stmt = $conn->prepare($sql_check);
    $stmt->bind_param("i", $utente_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id_ordine);
        $stmt->fetch();
    } else {
        // Nessun carrello aperto, ne creiamo uno nuovo
        $sql_insert = "INSERT INTO ordini (citta, cap, provincia, via, civico, stato, id_utente) VALUES ('', '', '', '', '', 'c', ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("i", $utente_id);
        $stmt_insert->execute();
        $id_ordine = $stmt_insert->insert_id;
    }

    // Controlla se il prodotto è già nel carrello
    $sql_check_dettaglio = "SELECT id, quantita FROM dettagli WHERE id_ordine = ? AND id_prodotto = ?";
    $stmt_check_dettaglio = $conn->prepare($sql_check_dettaglio);
    $stmt_check_dettaglio->bind_param("ii", $id_ordine, $id_prodotto);
    $stmt_check_dettaglio->execute();
    $result_dettaglio = $stmt_check_dettaglio->get_result();

    if ($row = $result_dettaglio->fetch_assoc()) {
        // Già presente, aggiorna quantità
        $new_qty = $row["quantita"] + 1;
        $update_stmt = $conn->prepare("UPDATE dettagli SET quantita = ? WHERE id = ?");
        $update_stmt->bind_param("ii", $new_qty, $row["id"]);
        $update_stmt->execute();
    } else {
        // Nuova riga
        $insert_stmt = $conn->prepare("INSERT INTO dettagli (id_ordine, id_prodotto, quantita) VALUES (?, ?, 1)");
        $insert_stmt->bind_param("ii", $id_ordine, $id_prodotto);
        $insert_stmt->execute();
    }

}

$conn->close();
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8" />
  <title><?php echo $prodotto['nome']; ?> - Info Prodotto</title>
  <link rel="stylesheet" href="stilehom.css" />
</head>
<body id="top">
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
      <a href="accesso.html" class="user-icon">👤</a>
    </div>
  </header>

  <main class="product-main">
    <div class="product-container">
      <img src="<?php echo $prodotto['percorso']; ?>" alt="<?php echo $prodotto['nome']; ?>" class="product-image">
      <div class="product-info">
        <h2><?php echo $prodotto['nome']; ?></h2>
        <p class="description"><?php echo $prodotto['descrizione']; ?></p>
        <p class="price">€<?php echo number_format($prodotto['prezzo'], 2); ?></p>

        <h3>Note di apertura</h3>
        <p><?php echo $prodotto['apertura']; ?></p>

        <h3>Note centrali</h3>
        <p><?php echo $prodotto['centrale']; ?></p>

        <h3>Note di base</h3>
        <p><?php echo $prodotto['base']; ?></p>

        <?php if (isset($_SESSION["utente_id"])): ?>
        <form method="POST">
          <button type="submit" class="add-to-cart">Aggiungi al Carrello</button>
        </form>
        <?php else: ?>
        <p><a href="accesso.html">Accedi</a> per aggiungere al carrello.</p>
        <?php endif; ?>
      </div>
    </div>
  </main>

  <footer class="footer" id="bottom">
    <div class="footer-container">
      <div class="footer-info">
        <h4>OuParfum</h4>
        <p class="footer-description">
          Scopri la tua essenza<br>Vivi il tuo profumo<br>Esprimi la tua personalità</p>
      </div>
      <div class="footer-contact">
        <h4>Contatti</h4>
        <p><strong>Email:</strong> support@ouparfum.com</p>
        <p><strong>Telefono:</strong> +39 02 4953 7612</p>
        <p><strong>Indirizzo:</strong> Via Della Vittoria, 15, Milano, Italia</p>
    </div>
    </div>
    <div class="footer-back-to-top">
      <a href="#top" class="back-to-top-link">Torna su</a>
    </div>
    <div class="footer-bottom">
      <p>&copy; 2025 OuParfum - Tutti i diritti riservati</p>
    </div>
  </footer>
  
<script>
  document.addEventListener('DOMContentLoaded', () => {
  const cartIcon = document.querySelector('.cart-icon');
  const addToCartButtons = document.querySelectorAll('.add-to-cart');

  addToCartButtons.forEach(button => {
    button.addEventListener('click', (e) => {
      e.preventDefault(); // ferma il submit per far vedere animazione

      cartIcon.classList.add('animate');

      setTimeout(() => {
        cartIcon.classList.remove('animate');
        // submitta il form dopo animazione
        button.closest('form').submit();
      }, 600);
    });
  });
});
</script>
</body>
</html>
