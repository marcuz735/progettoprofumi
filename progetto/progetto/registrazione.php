<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm-password"];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6 || $password !== $confirmPassword) {
        header("Location: accesso.html?errore_reg=1");
        exit();
    }

    $conn = mysqli_connect("localhost", "root", "vc-mob2-22", "ouparfum");
    if (!$conn) {
        die("Connessione fallita: " . mysqli_connect_error());
    }

    // Controllo se l'email è già registrata
    $stmt = mysqli_prepare($conn, "SELECT id FROM utenti WHERE mail = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        header("Location: accesso.html?errore_reg=1");
        exit();
    }

    mysqli_stmt_close($stmt);

    // Creazione della password hash
    $hash_pw = password_hash($password, PASSWORD_DEFAULT);

    // Inserimento dell'utente nella tabella utenti
    $stmt = mysqli_prepare($conn, "INSERT INTO utenti (mail, pw, stato) VALUES (?, ?, 'u')");
    mysqli_stmt_bind_param($stmt, "ss", $email, $hash_pw);

    if (mysqli_stmt_execute($stmt)) {
        $userId = mysqli_insert_id($conn); // Otteniamo l'id dell'utente appena registrato
        mysqli_stmt_close($stmt);

        // Aggiunta della riga nella tabella ordini con lo stato 'c' (nel carrello) e l'id_utente
        $stmt = mysqli_prepare($conn, "INSERT INTO ordini (citta, cap, via, civico, stato, id_utente) VALUES (NULL, NULL, NULL, NULL, 'c', ?)");
        mysqli_stmt_bind_param($stmt, "i", $userId);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            header("Location: accesso.html?registrato=1");
            exit();
        } else {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            header("Location: accesso.html?errore_reg=1");
            exit();
        }
    } else {
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        header("Location: accesso.html?errore_reg=1");
        exit();
    }
}
?>
