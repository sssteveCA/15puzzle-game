<?php
session_start();
unset($_SESSION['logged']);
unset($_SESSION['giocatore']);
session_destroy();
header('location: ../index.php');
?>