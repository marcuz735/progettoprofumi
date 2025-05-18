<?php
$conn = new mysqli("localhost", "root", "vc-mob2-22", "ouparfum");
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$immagini = [];
$res = $conn->query("SELECT percorso FROM prodotti ORDER BY RAND() LIMIT 3");
while ($riga = $res->fetch_assoc()) {
    $immagini[] = $riga['percorso'];
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profumi di Classe - Home</title>
  <link rel="stylesheet" href="stilehom.css">
</head>
<body id="top">
  <header class="site-header">
    <div class="header-left">
      <img src="immagini/logo.png" alt="Logo OuParfume" class="logo"> 
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
  
  <main>
    <div class="new-arrivals">
      <div class="carousel-container">
        <?php foreach ($immagini as $img): ?>
          <img src="<?= htmlspecialchars($img) ?>" alt="Prodotto casuale">
        <?php endforeach; ?>
      </div>
      <div class="carousel-link">
        <a href="prodotti.php">
          <em>Scopri<br>i nostri prodotti</em>
        </a>
      </div>
      <span class="arrow arrow-left">&#10094;</span>
      <span class="arrow arrow-right">&#10095;</span>
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
    const images = document.querySelectorAll('.new-arrivals img');
    const leftArrow = document.querySelector('.arrow-left');
    const rightArrow = document.querySelector('.arrow-right');
    
    let currentIndex = 0;

    function scrollLeft() {
      currentIndex = (currentIndex > 0) ? currentIndex - 1 : images.length - 1;
      updateCarousel();
    }

    function scrollRight() {
      currentIndex = (currentIndex < images.length - 1) ? currentIndex + 1 : 0;
      updateCarousel();
    }

    function updateCarousel() {
      const offset = -currentIndex * 100;
      images.forEach(image => {
        image.style.transform = `translateX(${offset}%)`;
      });
    }

    leftArrow.addEventListener('click', scrollLeft);
    rightArrow.addEventListener('click', scrollRight);
    updateCarousel();
  </script>
</body>
</html>
