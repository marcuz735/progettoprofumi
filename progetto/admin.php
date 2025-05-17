<?php
session_start();

// Controllo accesso admin
if (!isset($_SESSION["utente_id"]) || $_SESSION["utente_stato"] !== "a") {
    header("Location: accesso.html");
    exit();
}

$conn = new mysqli("localhost", "root", "vc-mob2-22", "ouparfum");
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// --- GESTIONE CATEGORIE ---

// Inserimento nuova categoria
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["azione_cat"]) && $_POST["azione_cat"] === "inserisci_categoria") {
    $aroma = $_POST["aroma"];
    $genere = $_POST["genere"];
    $stmt = $conn->prepare("INSERT INTO categorie (aroma, genere) VALUES (?, ?)");
    $stmt->bind_param("ss", $aroma, $genere);
    $stmt->execute();
    $stmt->close();
    header("Location: admin.php"); // Ricarica la pagina
    exit();
}

// Eliminazione categoria
if (isset($_GET["elimina_categoria"])) {
    $id_categoria = $_GET["elimina_categoria"];
    $stmt = $conn->prepare("DELETE FROM categorie WHERE id = ?");
    $stmt->bind_param("i", $id_categoria);
    $stmt->execute();
    $stmt->close();
    header("Location: admin.php");
    exit();
}

// Caricamento categorie per il menu a tendina e per la gestione categorie
$categorie = [];
$cat_result = $conn->query("SELECT id, aroma, genere FROM categorie");
while ($riga = $cat_result->fetch_assoc()) {
    $categorie[$riga['id']] = $riga['aroma'] . " - " . ucfirst($riga['genere']);
}

// --- GESTIONE PRODOTTI ---

// INSERIMENTO PRODOTTO
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["azione"]) && $_POST["azione"] === "inserisci") {
    $nome = $_POST["nome"];
    $descrizione = $_POST["descrizione"];
    $apertura = $_POST["apertura"];
    $centrale = $_POST["centrale"];
    $base = $_POST["base"];
    $prezzo = $_POST["prezzo"];
    $categoria = $_POST["categoria"];
    
    // Upload immagine
    $immagine_nome = basename($_FILES["immagine"]["name"]);
    $destinazione = "immagini/" . $immagine_nome;
    move_uploaded_file($_FILES["immagine"]["tmp_name"], $destinazione);

    $stmt = $conn->prepare("INSERT INTO prodotti (nome, descrizione, apertura, centrale, base, prezzo, id_categoria, percorso) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssdss", $nome, $descrizione, $apertura, $centrale, $base, $prezzo, $categoria, $destinazione);
    $stmt->execute();
    $stmt->close();
}

// CANCELLAZIONE PRODOTTO
if (isset($_GET["elimina"])) {
    $id = $_GET["elimina"];
    $stmt = $conn->prepare("DELETE FROM prodotti WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// MODIFICA PRODOTTO
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["azione"]) && $_POST["azione"] === "modifica") {
    $id = $_POST["id"];
    $nome = $_POST["nome"];
    $descrizione = $_POST["descrizione"];
    $apertura = $_POST["apertura"];
    $centrale = $_POST["centrale"];
    $base = $_POST["base"];
    $prezzo = $_POST["prezzo"];
    $categoria = $_POST["categoria"];
    $percorso_vecchio = $_POST["percorso_vecchio"];

    // Controllo se è stata caricata una nuova immagine
    if (isset($_FILES["immagine"]) && $_FILES["immagine"]["error"] === UPLOAD_ERR_OK) {
        $immagine_nome = basename($_FILES["immagine"]["name"]);
        $destinazione = "immagini/" . $immagine_nome;

        // Sposta il nuovo file nella cartella immagini
        move_uploaded_file($_FILES["immagine"]["tmp_name"], $destinazione);

        // (facoltativo) puoi eliminare la vecchia immagine dal server
        if (file_exists($percorso_vecchio)) {
            unlink($percorso_vecchio);
        }

        $percorso = $destinazione;
    } else {
        // Nessuna nuova immagine caricata, mantieni la vecchia
        $percorso = $percorso_vecchio;
    }

    $stmt = $conn->prepare("UPDATE prodotti SET nome=?, descrizione=?, apertura=?, centrale=?, base=?, prezzo=?, id_categoria=?, percorso=? WHERE id=?");
    $stmt->bind_param("sssssdssi", $nome, $descrizione, $apertura, $centrale, $base, $prezzo, $categoria, $percorso, $id);
    $stmt->execute();
    $stmt->close();
}

// RECUPERO TUTTI I PRODOTTI
$prodotti = $conn->query("SELECT * FROM prodotti");
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Prodotti - Admin</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; vertical-align: top; }
        h2 { margin-top: 40px; }
        form { margin-bottom: 30px; }
        input, textarea, select { width: 100%; padding: 6px; margin: 5px 0; }
        .btn { padding: 8px 16px; margin-top: 10px; cursor: pointer; }
        img { border-radius: 4px; }
        a { text-decoration: none; color: red; font-weight: bold; }
    </style>
</head>
<body>

<h1>Gestione Prodotti - Amministratore</h1>

<h2>Inserisci nuovo prodotto</h2>
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="azione" value="inserisci">
    Nome: <input type="text" name="nome" required>
    Descrizione: <textarea name="descrizione" required></textarea>
    Note di apertura: <input type="text" name="apertura" required>
    Note centrali: <input type="text" name="centrale" required>
    Note di base: <input type="text" name="base" required>
    Prezzo: <input type="number" name="prezzo" step="0.01" required>
    Categoria:
    <select name="categoria" required>
        <option value="" disabled selected>Seleziona categoria</option>
        <?php foreach ($categorie as $id => $desc): ?>
            <option value="<?= $id ?>"><?= htmlspecialchars($desc) ?></option>
        <?php endforeach; ?>
    </select>
    Immagine: <input type="file" name="immagine" accept="image/*" required>
    <button type="submit" class="btn">Inserisci</button>
</form>

<!-- SEZIONE GESTIONE CATEGORIE -->
<h2>Gestione categorie</h2>

<h3>Nuova categoria</h3>
<form method="POST">
    <input type="hidden" name="azione_cat" value="inserisci_categoria">
    Aroma: <input type="text" name="aroma" required>
    Genere:
    <select name="genere" required>
        <option value="" disabled selected>Seleziona genere</option>
        <option value="Femminile">Femminile</option>
        <option value="Maschile">Maschile</option>
    </select>
    <button type="submit" class="btn">Aggiungi Categoria</button>
</form>

<h3>Categorie esistenti</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Aroma</th>
        <th>Genere</th>
        <th>Azioni</th>
    </tr>
    <?php
    $cat_result = $conn->query("SELECT id, aroma, genere FROM categorie");
    while ($cat = $cat_result->fetch_assoc()):
    ?>
        <tr>
            <td><?= $cat['id'] ?></td>
            <td><?= htmlspecialchars($cat['aroma']) ?></td>
            <td><?= htmlspecialchars($cat['genere']) ?></td>
            <td>
                <a href="?elimina_categoria=<?= $cat['id'] ?>" onclick="return confirm('Eliminare questa categoria?')">🗑️</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<h2>Prodotti esistenti</h2>
<table>
    <tr>
        <th>Immagine</th>
        <th>Nome</th>
        <th>Descrizione</th>
        <th>Apertura</th>
        <th>Centrale</th>
        <th>Base</th>
        <th>Prezzo</th>
        <th>Categoria</th>
        <th>Azioni</th>
    </tr>
    <?php while ($row = $prodotti->fetch_assoc()): ?>
        <tr>
            <form method="POST" enctype="multipart/form-data">
                <td><img src="<?= htmlspecialchars($row['percorso']) ?>" width="50" alt="img"></td>
                <td><input type="text" name="nome" value="<?= htmlspecialchars($row['nome']) ?>"></td>
                <td><textarea name="descrizione"><?= htmlspecialchars($row['descrizione']) ?></textarea></td>
                <td><input type="text" name="apertura" value="<?= htmlspecialchars($row['apertura']) ?>"></td>
                <td><input type="text" name="centrale" value="<?= htmlspecialchars($row['centrale']) ?>"></td>
                <td><input type="text" name="base" value="<?= htmlspecialchars($row['base']) ?>"></td>
                <td><input type="number" name="prezzo" step="0.01" value="<?= $row['prezzo'] ?>"></td>
                <td>
                    <select name="categoria" required>
                        <?php foreach ($categorie as $id => $desc): ?>
                            <option value="<?= $id ?>" <?= $id == $row['id_categoria'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($desc) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <input type="hidden" name="azione" value="modifica">
                    <input type="hidden" name="percorso_vecchio" value="<?= htmlspecialchars($row['percorso']) ?>">
                    <input type="file" name="immagine" accept="image/*">
                    <button type="submit" class="btn">Salva</button>
                    <a href="?elimina=<?= $row['id'] ?>" onclick="return confirm('Sei sicuro?')">🗑️</a>
                </td>
            </form>
        </tr>
    <?php endwhile; ?>
</table>

<form action="logout.php" method="post" style="text-align: right;">
    <button type="submit" style="background-color: red; color: white; border: none; padding: 8px 16px; cursor: pointer;">Logout</button>
</form>

</body>
</html>

<?php $conn->close(); ?>
