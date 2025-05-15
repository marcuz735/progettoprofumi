<?php
// Connessione al database con MySQLi
$host = 'localhost'; // nome host
$db = 'ouparfum'; // nome del database
$user = 'root'; // nome utente
$pass = 'vc-mob2-22'; // password

// Crea connessione
$conn = new mysqli($host, $user, $pass, $db);

// Verifica la connessione
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Seleziona tutti i prodotti dalla tabella
$query = "SELECT * FROM prodotti";
$result = $conn->query($query);

// Verifica se ci sono prodotti
if ($result->num_rows > 0) {
    $prodotti = $result->fetch_all(MYSQLI_ASSOC); // Ottieni tutti i prodotti
} else {
    $prodotti = []; // Se non ci sono prodotti
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Prodotti - AuParfum</title>
    <link rel="stylesheet" href="stilehom.css" />
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
            <a href="carrello.php" class="cart-icon">🛒</a>
            <a href="accesso.html" class="user-icon">👤</a>
        </div>
    </header>

    <!-- Sezione prodotti -->
    <main>
        <section class="product-list" id="product-list">
            <?php
            // Cicliamo su ogni prodotto estratto dal database
            foreach ($prodotti as $prodotto) {
                // Recupera i dati
                $percorso = htmlspecialchars($prodotto['percorso']);
                $nome = htmlspecialchars($prodotto['nome']);
                $prezzo = number_format($prodotto['prezzo'], 2);
            
                // Stampa un blocco per ogni prodotto
                echo '<a href="prod_info.php?product=' . $prodotto['id'] . '" class="product">';
                echo '<img src="' . $percorso . '" alt="Profumo ' . $nome . '" />';
                echo '<div class="product-info">';
                echo '<h3>' . $nome . '</h3>';
                echo '<p class="price">€' . $prezzo . '</p>';
                echo '</div>';
                echo '</a>';
            }
            
            ?>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer" id="bottom">
        <div class="footer-container">
            <!-- Sezione informazioni -->
            <div class="footer-info">
                <h4>AuParfume</h4>
                <p class="footer-description">
                    Scopri la tua essenza<br>Vivi il tuo profumo<br>Esprimi la tua personalità
                </p>
            </div>

            <!-- Sezione contatti -->
            <div class="footer-contact">
                <h4>Contatti</h4>
                <p><strong>Email:</strong> support@auparfume.com</p>
                <p><strong>Telefono:</strong> +39 0123 456789</p>
                <p><strong>Indirizzo:</strong> Via Roma, 123, Milano, Italia</p>
            </div>
        </div>

        <!-- Sezione "Torna su" sotto OuParfume -->
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

<?php
// Chiudiamo la connessione
$conn->close();
?>
