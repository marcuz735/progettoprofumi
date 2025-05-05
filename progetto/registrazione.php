<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mail = $_POST["email"];
    $pw = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $conn = mysqli_connect("localhost", "root", "vc-mob2-22", "ouparfum");

    if (!$conn) {
        die("Connessione fallita: " . mysqli_connect_error());
    }

    $stmt = mysqli_prepare($conn, "INSERT INTO utenti (mail, pw) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ss", $mail, $pw);

    if (mysqli_stmt_execute($stmt)) {
        $id = mysqli_insert_id($conn);

        // Login automatico
        $_SESSION["utente_id"] = $id;
        $_SESSION["utente_mail"] = $mail;

        // Redirect automatico alla homepage
        header("Location: homepage.html");
        exit();
    } else {
        echo "Errore durante la registrazione: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>
