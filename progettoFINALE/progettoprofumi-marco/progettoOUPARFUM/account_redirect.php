<?php
session_start(); // Avvia la sessione

// Controlla se la sessione dell'utente è attiva
if (isset($_SESSION['utente_id'])) {
    // Controlla se l'utente è un amministratore
    if (isset($_SESSION['utente_stato']) && $_SESSION['utente_stato'] === 'a') {
        header("Location: admin.php");
    } else {
        header("Location: account.php");
    }
    exit();
} else {
    // Se l'utente non è loggato, reindirizza alla pagina di accesso
    header("Location: accesso.html");
    exit();
}
?>