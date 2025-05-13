<?php
session_start();

// Distruggi tutte le variabili di sessione e la sessione
session_unset();
session_destroy();

// Reindirizza alla pagina di login
header("Location: accesso.html");
exit();
?>
