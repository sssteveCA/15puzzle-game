
<?php

//pagina che effettua la richiesta POST: reset.php
ob_start();
require_once('config.php');
require_once('../oggetti/giocatore.php');
$messaggio = array();
$messaggio['msg'] = '';
$ajax = (isset($_POST['ajax']) && $_POST['ajax'] == '1');


//se l'utente è connesso
if(isset($_SESSION['giocatore'],$_SESSION['logged']) && $_SESSION['giocatore'] != '' && $_SESSION['logged']){
    header('location: ../menuGioco.php');
}
else{
    if(isset($_REQUEST['chiave']) && preg_match(Giocatore::$regex['cambioPwd'],$_REQUEST['chiave'])){
        if(isset($_POST['nuova'],$_POST['confNuova']) && $_POST['nuova'] != '' && $_POST['confNuova'] != ''){
            $nuova = $_POST['nuova']; //nuova password
            $conf = $_POST['confNuova']; //conferma nuova password
            //se le due password coincidono
            if($nuova == $conf){
                $dati = array();
                $dati['campo'] = 'cambioPwd';
                $dati['registrato'] = '1';
                $dati['dimenticata'] = '1';
                $dati['nuovaP'] = $nuova;
                $dati['cambioPwd'] = $_REQUEST['chiave'];
                $dati['dataCambioPwd'] = time()-$attesa;
                try{
                    $giocatore = new Giocatore($dati);
                    if($giocatore->getErrno() == 0 || $giocatore->getErrno() == GIOCATOREERR_INCORRECTLOGINDATA){
                        $messaggio['done'] = '1';
                        $messaggio['msg'] = 'Password modificata';
                    }
                    else{
                        $messaggio['error'] = '1';
                        $messaggio['msg'] = $giocatore->getError();
                    }
                }
                catch(Exception $e){
                    $messaggio['error'] = '1';
                    $messaggio['msg'] = $e->getMessage();
                }
            }//if($nuova == $conf)
            else{
                $messaggio['warning'] = '1';
                $messaggio['msg'] = 'Le due password non coincidono';
            }
        }//if(isset($_POST['nuova'],$_POST['confNuova']) && $_POST['nuova'] != '' && $_POST['confNuova'] != '')
        else{
            $messaggio['warning'] = '1';
            $messaggio['msg'] = 'Nessuna password impostata';
        }
    }//if(isset($_REQUEST['chiave']) && preg_match(Giocatore::$regex['cambioPwd'],$_REQUEST['chiave']))
    else{
        $messaggio['error'] = '1';
        $messaggio['msg'] = 'Codice non valido';
    }
    if($ajax){
        echo json_encode($messaggio);
    }
    //se non è stata fatta una chiamata con AJAX mostra la pagina HTML
    else{
        $html = <<<HTML
<!DOCTYPE html>
<html lang="it">
    <head>
        <title>Recupera password</title>
        <meta charset="utf-8">
    </head>
    <body>
    {$messaggio['msg']}
    </body>
</html>
HTML;
        echo $html;
    }
}//else di if(isset($_SESSION['giocatore'],$_SESSION['logged']) && $_SESSION['giocatore'] != '' && $_SESSION['logged'])
?>