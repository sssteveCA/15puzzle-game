<?php
ob_start();
require_once('funzioni/config.php');
require_once('oggetti/giocatore.php');

//se l'utente è connesso
if(isset($_SESSION['giocatore'],$_SESSION['logged']) && $_SESSION['giocatore'] != '' && $_SESSION['logged']){
    header('location: menuGioco.php');
}
else{
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Recupera password</title>
        <link rel="stylesheet" href="css/global.css" type="text/css">
        <link rel="stylesheet" href="css/reset.css" type="text/css">
        <link rel="stylesheet" href="jqueryUI/jquery-ui.min.css" type="text/css">
        <link rel="stylesheet" href="jqueryUI/jquery-ui.theme.min.css" type="text/css">
        <script src="js/jquery-3.6.0.min.js"></script>
        <script src="jqueryUI/jquery-ui.min.js"></script>
        <script src="js/dialog.js"></script>
        <script src="js/reset.js"></script>
    <head>
    <body>
    <div id="indietro">
        <a href="recupera.php"><img src="immagini/back.jpg" alt="indietro" title="indietro"></a>
        <a href="recupera.php">Indietro</a>
    </div>
    <div id="container">
<?php
    if(isset($_REQUEST['codReset']) && preg_match(Giocatore::$regex['cambioPwd'],$_REQUEST['codReset'])){
        $codReset = $_REQUEST['codReset'];
        $time = time()-$attesa;
        $dati = array();
        $giocatore = new Giocatore($dati);
        $esiste = $giocatore->esiste("`cambioPwd` = '$codReset' AND `dataCambioPwd` >= '$time'");
        //se il codice di cambio password esiste mostro il form
        if($esiste == 1){
?>
<fieldset id="f1">
    <legend>Recupero password</legend>
    <h2>Inserisci la nuova password</h2>
    <form action="funzioni/recovery.php" method="post" id="fRecupera">
        <div>
            <label for="nuova">Nuova password</label>
            <input type="password" id="nuova" name="nuova">
        </div>
        <div>
            <label for="confNuova">Conferma nuova password</label>
            <input type="password" id="confNuova" name="confNuova">
        </div>
        <div>
            <input type="hidden" id="chiave" name="chiave" value="<?php echo $_REQUEST['codReset']; ?>">
            <input type="submit" id="conferma" value="CONFERMA">
        </div>
    </form>
</fieldset>
</div>
<?php
        }//if($esiste == 1)
        else echo 'Codice non valido';
    }//if(isset($_REQUEST['codReset']) && preg_match(Giocatore::$regex['codAut'],$_REQUEST['codReset']))
    else echo 'Formato codice non corretto';
?>
<?php
?>
    </body>
</html
<?php
}//else di if(isset($_SESSION['giocatore'],$_SESSION['logged']) && $_SESSION['giocatore'] != '' && $_SESSION['logged'])
?>
