<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mail = $_POST["email"];
    $pw = $_POST["password"];

    $conn = mysqli_connect("localhost", "root", "vc-mob2-22", "ouparfum");

    if (!$conn) {
        die("Connessione fallita: " . mysqli_connect_error());
    }

    $stmt = mysqli_prepare($conn, "SELECT id, pw FROM utenti WHERE mail = ?");
    mysqli_stmt_bind_param($stmt, "s", $mail);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id, $hash_pw);

    if (mysqli_stmt_fetch($stmt) && password_verify($pw, $hash_pw)) {
        $_SESSION["utente_id"] = $id;
        $_SESSION["utente_mail"] = $mail;
        header("Location: homepage.html");
        exit();
    } else {
        header("Location: accesso.html?errore=1");
        exit();
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>
