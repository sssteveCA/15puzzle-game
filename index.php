<?php
session_start();
//se l'utente è connesso
if(isset($_SESSION['giocatore'],$_SESSION['logged']) && $_SESSION['giocatore'] != '' && $_SESSION['logged']){
    header('location: menuGioco.php');
}
else{
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <title>Accedi</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="css/global.css" type="text/css">
        <link rel="stylesheet" href="jqueryUI/jquery-ui.min.css" type="text/css">
        <link rel="stylesheet" href="jqueryUI/jquery-ui.theme.min.css" type="text/css">
        <script src="js/jquery-3.6.0.min.js"></script>
        <script src="jqueryUI/jquery-ui.min.js"></script>
        <script src="js/index.js"></script>
    </head>
    <body>
        <h1 id="titolo" title="gioco del quindici">Gioco del quindici</h1>
        <div id="menu">
            <div id="m0" class="voci">Accedi</div>
            <div id="m1" class="voci">Registrati</div>
            <div id="m2" class="voci">Recupera profilo</div>
        </div>
    </body>
</html>
<?php
}// else(utente non connesso)
?>