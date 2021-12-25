<?php
session_start();
require_once('funzioni/config.php');
require_once('oggetti/giocatore.php');
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <title>Modifica profilo</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="css/global.css" type="text/css">
        <link rel="stylesheet" href="css/profilo.css" type="text/css">
        <link rel="stylesheet" href="jqueryUI/jquery-ui.min.css" type="text/css">
        <link rel="stylesheet" href="jqueryUI/jquery-ui.theme.min.css" type="text/css">
        <script src="js/jquery-3.6.0.min.js"></script>
        <script src="jqueryUI/jquery-ui.min.js"></script>
        <script src="js/dialog.js"></script>
        <script src="js/profilo.js"></script>
    </head>
    <body>
        <div id="indietro">
            <a href="menuGioco.php"><img src="immagini/back.jpg" alt="indietro" title="indietro"></a>
            <a href="menuGioco.php">Indietro</a>
        </div>
        <h1 id="titolo" title="gioco del quindici">Gioco del quindici</h1>
<?php
//se l'utente è connesso
if(isset($_SESSION['giocatore'],$_SESSION['logged']) && $_SESSION['giocatore'] != '' && $_SESSION['logged']){
    $giocatore = unserialize($_SESSION['giocatore']);
?>
        <table id="tProfilo" border="1">
            <caption>Modifica dati profilo</caption>
            <tr>
                <td class="def">Indirizzo email</td>
                <td class="val"><?php echo $giocatore->getEmail(); ?></td>
            </tr>
            <tr>
                <td class="def">Nome utente</td>
                <td class="val">
                    <form id="fUsername" method="post" action="funzioni/modProfilo.php">
                        <input type="hidden" name="edit" value="1">
                        <input type="text" id="username" class="field" name="username" value="<?php echo $giocatore->getUsername(); ?>">
                        <input type="button" id="bModifica" class="button" value="MODIFICA">
                    </form>
                </td>
            </tr>
            <tr>
                <td class="def">Password</td>
                <td class="val">
                    <form id="fPassword" method="post" action="funzioni/modProfilo.php">
                        <input type="hidden" name="edit" value="2">
                        <input type="password" id="nuova" class="field" name="nuova" placeholder="Inserisci la nuova password"> 
                        <input type="password" id="confNuova" class="field" name="confNuova" placeholder="Conferma la nuova password"> 
                        <input type="button" id="bModifica2" class="button" value="MODIFICA">
                    </form>
                </td>
            </tr>
        </table>
<?php
}//if(isset($_SESSION['giocatore'],$_SESSION['logged']) && $_SESSION['giocatore'] != '' && $_SESSION['logged'])
else{
    echo '<div><a href="index.php">Effettua l\' accesso</a> per giocare</div>';
}
?>
    </body>
</html>
