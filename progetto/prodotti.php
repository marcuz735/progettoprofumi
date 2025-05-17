<?php
// Connessione al database
$host = 'localhost';
$db = 'ouparfum';
$user = 'root';
$pass = 'vc-mob2-22';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Recupera aromi distinti per filtro
$resAromi = $conn->query("SELECT DISTINCT aroma FROM categorie ORDER BY aroma");
$aromi = [];
while ($row = $resAromi->fetch_assoc()) {
    $aromi[] = $row['aroma'];
}

// Valori dei filtri
$aroma_selezionato = $_GET['aroma'] ?? "";
$genere_selezionato = $_GET['genere'] ?? "";

// Ricaviamo gli id_categoria validi per i filtri combinati
$id_categorie_filtrate = [];
if ($aroma_selezionato || $genere_selezionato) {
    $queryCat = "SELECT id FROM categorie WHERE 1=1";
    if ($aroma_selezionato) {
        $queryCat .= " AND aroma = '" . $conn->real_escape_string($aroma_selezionato) . "'";
    }
    if ($genere_selezionato) {
        $queryCat .= " AND genere = '" . $conn->real_escape_string($genere_selezionato) . "'";
    }
    $resCat = $conn->query($queryCat);
    while ($row = $resCat->fetch_assoc()) {
        $id_categorie_filtrate[] = $row['id'];
    }
}

// Costruisci query prodotti
if (!empty($id_categorie_filtrate)) {
    $id_str = implode(',', array_map('intval', $id_categorie_filtrate));
    $queryProdotti = "SELECT * FROM prodotti WHERE id_categoria IN ($id_str)";
} else {
    if ($aroma_selezionato || $genere_selezionato) {
        // Nessuna categoria corrisponde ai filtri impostati: nessun prodotto da mostrare
        $queryProdotti = "SELECT * FROM prodotti WHERE 1=0";
    } else {
        // Nessun filtro impostato: mostra tutti i prodotti
        $queryProdotti = "SELECT * FROM prodotti";
    }
}

$result = $conn->query($queryProdotti);
$prodotti = ($result && $result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
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
        <section class="filters" style="text-align: center; margin: 20px;">
            <form method="GET" action="prodotti.php">
                <label for="aroma">Aroma:</label>
                <select name="aroma" id="aroma">
                    <option value="">Tutti</option>
                    <?php foreach ($aromi as $aroma): ?>
                        <option value="<?= htmlspecialchars($aroma) ?>" <?= $aroma == $aroma_selezionato ? 'selected' : '' ?>>
                            <?= htmlspecialchars($aroma) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="genere">Genere:</label>
                <select name="genere" id="genere">
                    <option value="">Tutti</option>
                    <option value="maschile" <?= $genere_selezionato == "maschile" ? 'selected' : '' ?>>Maschile</option>
                    <option value="femminile" <?= $genere_selezionato == "femminile" ? 'selected' : '' ?>>Femminile</option>
                </select>

                <button type="submit">Filtra</button>
            </form>
        </section>

        <section class="product-list" id="product-list">
            <?php if (empty($prodotti)): ?>
                <p style="text-align: center;">Nessun prodotto trovato per la categoria selezionata.</p>
            <?php else: ?>
                <?php foreach ($prodotti as $prodotto): ?>
                    <a href="prod_info.php?product=<?= $prodotto['id'] ?>" class="product">
                        <img src="<?= htmlspecialchars($prodotto['percorso']) ?>" alt="Profumo <?= htmlspecialchars($prodotto['nome']) ?>" />
                        <div class="product-info">
                            <h3><?= htmlspecialchars($prodotto['nome']) ?></h3>
                            <p class="price">€<?= number_format($prodotto['prezzo'], 2) ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>

    <footer class="footer" id="bottom">
        <div class="footer-container">
            <div class="footer-info">
                <h4>AuParfume</h4>
                <p class="footer-description">
                    Scopri la tua essenza<br>Vivi il tuo profumo<br>Esprimi la tua personalità
                </p>
            </div>
            <div class="footer-contact">
                <h4>Contatti</h4>
                <p><strong>Email:</strong> support@auparfume.com</p>
                <p><strong>Telefono:</strong> +39 0123 456789</p>
                <p><strong>Indirizzo:</strong> Via Roma, 123, Milano, Italia</p>
            </div>
        </div>
        <div class="footer-back-to-top">
            <a href="#top" class="back-to-top-link">Torna su</a>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 OuParfume - Tutti i diritti riservati</p>
        </div>
    </footer>
</body>
</html>
<?php $conn->close(); ?>
