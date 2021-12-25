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
        <title>Recupera password</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="css/global.css" type="text/css">
        <link rel="stylesheet" href="css/recupera.css" type="text/css">
        <link rel="stylesheet" href="jqueryUI/jquery-ui.min.css" type="text/css">
        <link rel="stylesheet" href="jqueryUI/jquery-ui.theme.min.css" type="text/css">
        <script src="js/jquery-3.6.0.min.js"></script>
        <script src="jqueryUI/jquery-ui.min.js"></script>
        <script src="js/dialog.js"></script>
        <script src="js/recupera.js"></script>
    </head>
    <body>
        <div id="indietro">
            <a href="index.php"><img src="immagini/back.jpg" alt="indietro" title="indietro"></a>
            <a href="index.php">Indietro</a>
        </div>
        <h1 id="titolo" title="gioco del quindici">Gioco del quindici</h1>
        <fieldset id="dRecupera">
            <legend>Recupera il tuo profilo</legend>
            <form id="fRecupera" method="post" action="funzioni/recProfilo.php">
                <div>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email">
                </div>
                <div>
                    <input type="submit" id="bOk" value="OK">
                </div>
            </form>
        </fieldset>
    </body>
</html>
<?php
} //else di if(isset($_SESSION['giocatore'],$_SESSION['logged']) && $_SESSION['giocatore'] != '' && $_SESSION['logged'])
?>