<?php
session_start();
unset($_SESSION['sequenza']);
unset($_SESSION['gioco']);
unset($_SESSION['errore']);
unset($_SESSION['spostamenti']);
unset($_SESSION['day']);
unset($_SESSION['h']);
unset($_SESSION['m']);
unset($_SESSION['s']);
header('location: ../menuGioco.php');
?>