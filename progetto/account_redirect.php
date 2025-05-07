<?php
session_start(); // Avvia la sessione

// Controlla se la sessione dell'utente è attiva
if (isset($_SESSION['utente'])) {
    // Se l'utente è loggato, reindirizza alla pagina account.php
    header("Location: account.php");
    exit();
} else {
    // Se l'utente non è loggato, reindirizza alla pagina di accesso
    header("Location: accesso.html");
    exit();
}
?>
