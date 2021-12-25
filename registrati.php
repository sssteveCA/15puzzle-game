<?php
session_start();
if(isset($_SESSION['giocatore'],$_SESSION['logged']) && $_SESSION['giocatore'] != '' && $_SESSION['logged']){
}
else{
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <title>Registrati</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="css/global.css" type="text/css">
        <link rel="stylesheet" href="css/registrati.css" type="text/css">
        <link rel="stylesheet" href="jqueryUI/jquery-ui.min.css" type="text/css">
        <link rel="stylesheet" href="jqueryUI/jquery-ui.theme.min.css" type="text/css">
        <script src="js/jquery-3.6.0.min.js"></script>
        <script src="jqueryUI/jquery-ui.min.js"></script>
        <script src="js/dialog.js"></script>
        <script src="js/registrati.js"></script>
    </head>
    <body>
        <div id="indietro">
            <a href="index.php"><img src="immagini/back.jpg" alt="indietro" title="indietro"></a>
            <a href="index.php">Indietro</a>
        </div>
        <h1 id="titolo" title="gioco del quindici">Gioco del quindici</h1>
        <form id="fRegistra" method="post" action="funzioni/creaAccount.php">
            <table id="tabella" border="1">
                <tr>
                    <td>Email</td>
                    <td><input type="email" id="iEmail" class="iCampo" name="email"></td>
                </tr>
                <tr>
                    <td>Username</td>
                    <td><input type="text" id="iUser" class="iCampo" name="username"></td>
                </tr>
                <tr>
                    <td>Password</td>
                    <td><input type="password" id="iPass" class="iCampo" name="password"></td>
                </tr>
                <tr>
                    <td>Conferma password</td>
                    <td><input type="password" id="iConfPass" class="iCampo" name="confPassword"></td>
                </tr>
                <tr>
                    <td><input type="submit" id="bOk" class="pulsante" value="REGISTRATI"></td>
                    <td><input type="reset" id="bReset" class="pulsante" value="ANNULLA"></td>
                </tr>
            </table>
        </form>
    </body>
<?php
}//else (utente non connesso)
?>