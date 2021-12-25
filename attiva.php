<?php
session_start();
require_once('funzioni/config.php');
require_once('oggetti/giocatore.php');

//se l'utente è collegato non eseguo la pagina
if(isset($_SESSION['giocatore'],$_SESSION['logged']) && $_SESSION['giocatore'] != '' && $_SESSION['logged']){
}
else{
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Attivazione account</title>
        <style>
            body{
                display: flex;
                justify-content: center;
            }
            fieldset div{
                margin: 10px;
            }
            fieldset div:last-child{
                display: flex;
                justify-content: center;
            }
            fieldset div > *{
                margin-right: 5px;
            }
            input#codAut{
                width: 300px;
            }
            input#attiva{
                padding: 5px;
            }
        </style>
    </head>
    <body>
<?php
    $regex = '/^[a-z0-9]{64}$/i';
    if(isset($_REQUEST['codAut']) && preg_match($regex,$_REQUEST['codAut'])){
        $dati = array();
        $dati['campo'] = 'codAut';
        $dati['codAut'] = $_REQUEST['codAut'];
        $dati['registrato'] = '1';
        $giocatore = new Giocatore($dati);
        $codAut = $giocatore->getCodAut();
        $error = $giocatore->getErrno();
        //account attivato
        if(!isset($codAut) && $error === 0){
            echo 'L\' account è stato attivato';
        }
        //account non attivato
        else{
            //echo 'L\' account non è stato attivato';
            echo $giocatore->getError();
        }
    }//if(isset($_REQUEST['codAut']) && preg_match($regex,$_REQUEST['codAut']))  
    else{
?>
        <fieldset id="f1">
            <legend>Attivazione account</legend>
            <h2>Inserisci il codice di attivazione</h2>
            <form action="attiva.php" method="post" id="fAttiva">
                <div>
                    <label for="codAut">Codice</label>
                    <input type="text" id="codAut" name="codAut">
                </div>
                <div>
                    <input type="submit" id="attiva" value="ATTIVA">
                </div>
            </form>
        </fieldset>
<?php
    }//else di if(isset($_REQUEST['codAut']) && preg_match($regex,$_REQUEST['codAut']))
?>
    </body>
</html>
<?php
}//else (utente non collegato)
?>