<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Acquisizione dei dati dal modulo
    $mail = $_POST["email"];
    $pw = $_POST["password"];

    // Connessione al database
    $conn = new mysqli("localhost", "root", "vc-mob2-22", "ouparfum");

    if ($conn->connect_error) {
        die("Connessione fallita: " . $conn->connect_error);
    }

    // Prepara la query per selezionare l'utente in base all'email
    $stmt = $conn->prepare("SELECT id, pw FROM utenti WHERE mail = ?");
    $stmt->bind_param("s", $mail);
    $stmt->execute();
    $stmt->bind_result($id, $hash_pw);

    // Verifica se la password è corretta
    if ($stmt->fetch() && password_verify($pw, $hash_pw)) {
        // Imposta le variabili di sessione
        $_SESSION["utente_id"] = $id;
        $_SESSION["utente_mail"] = $mail;

        // Reindirizza alla pagina dell'account
        header("Location: account.php");
        exit();
    } else {
        // Reindirizza indietro se le credenziali non sono corrette
        header("Location: accesso.html?errore=1");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
