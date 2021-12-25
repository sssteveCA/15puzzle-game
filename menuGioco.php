<?php
session_start();
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <title>Menu</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="css/global.css" type="text/css">
        <link rel="stylesheet" href="jqueryUI/jquery-ui.min.css" type="text/css">
        <link rel="stylesheet" href="jqueryUI/jquery-ui.theme.min.css" type="text/css">
        <script src="js/jquery-3.6.0.min.js"></script>
        <script src="jqueryUI/jquery-ui.min.js"></script>
        <script src="js/dialog.js"></script>
        <script src="js/menuGioco.js"></script>
    </head>
    <body>
<?php
//se il giocatore è connesso
if(isset($_SESSION['giocatore'],$_SESSION['logged']) && $_SESSION['giocatore'] != '' && $_SESSION['logged']){
?>
        <h1 id="titolo" title="gioco del quindici">Gioco del quindici</h1>
        <div id="menu">
            <form id="fNuova" method="post" action="funzioni/load.php">
                <div id="m0" class="voci">Nuova partita</div>
                <input type="hidden" name="nuova" value="1">
            </form>
            <form id="fCarica" method="post" action="funzioni/load.php">
                <div id="m1" class="voci">Carica partita</div>
                <input type="hidden" name="carica" value="1">
            </form>
            <div id="m2" class="voci">Profilo</div>
            <div id="m3" class="voci">Record</div>
            <div id="m4" class="voci">Esci</div>
        </div>
<?php
}//if(isset($_SESSION['giocatore'],$_SESSION['logged']) && $_SESSION['giocatore'] != '' && $_SESSION['logged'])
else{
    echo '<div><a href="index.php">Effettua l\' accesso</a> per giocare</div>';
}
?>
    </body>
</html>