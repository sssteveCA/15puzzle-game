<?php
session_start();
require_once('config.php');
require_once('../oggetti/giocatore.php');
$risultato = array();
$ajax = (isset($_POST['ajax']) && $_POST['ajax'] == '1');
$risultato['msg'] = 'Non è stata scelta alcuna operazione valida';

//se l'utente è connesso
if(isset($_SESSION['giocatore'],$_SESSION['logged']) && $_SESSION['giocatore'] != '' && $_SESSION['logged']){
    header('location: ../menuGioco.php');
}
else{
    //password dimenticata
    if(isset($_POST['email']) && $_POST['email'] != ''){
        $email = $_POST['email'];
        //codice per il recupero dell'account
        $dati = array();
        $dati['campo'] = 'email';
        $dati['registrato'] = '1';
        $dati['dimenticata'] = '1';
        $dati['email'] = $email;
        try{
            $giocatore = new Giocatore($dati);
            $valori = array();
            $valori['cambioPwd'] = $giocatore->getCambioPwd();
            $valori['dataCambioPwd'] = $giocatore->getDataCambioPwd();
            $where = array();
            $where['email'] = $giocatore->getEmail();
            $mod = $giocatore->update($valori,$where);
            if($mod){
                /*indirizzo assoluto della pagina reset.php
                REQUEST_SCHEME = protocollo utilizzato
                SERVER_NAME = nome del sito da cui lo script è eseguito
                SCRIPT_NAME = percorso dello script in esecuzione  */
                $indReset = dirname($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'],2).'/reset.php';
                //URL con il codice per reimpostare la password
                $indResetCod = $indReset.'?codReset='.$giocatore->getCambioPwd();
                //inserisce $codReset in 'cambioPwd nel campo 'email' che ha $email
                $headers = <<<HEADER
From: Admin <noreply@localhost.lan>
Reply-to: noreply@localhost.lan
Content-type: text/html
MIME-Version: 1.0
HEADER;
//il messaggio viene inviato come pagina HTML
        $body = <<<HTML
<!DOCTYPE html>
<html lang="it">
    <head>
        <title>Recupera password</title>
        <meta charset="utf-8">
        <style>
            body{
                display: flex;
                justify-content: center;
            }
            div#pagina{
                background-color: cyan;
                padding: 40px;
            }
            div#account{
                background-color: lime;
                padding: 20px;
            }
            p{
                margin: 10px;
            }
            p#messaggio{
                font-size: 20px;
                font-weight: bold;
                color: blue;
            }
        </style>
    </head>
    <body>
        <div id="pagina">
            <p id="messaggio">Per recuperare la password fai click sul link sottostante</p>
            <div id="account">
                    <p id="link"><a href="{$indResetCod}">{$indResetCod}</a></p>                   
            </div>
        </div>
    </body>
</html>
HTML;
                $send = $giocatore->sendEmail($giocatore->getEmail(),'Recupero password',$body,$headers);
                if($send){
                    $risultato['done'] = '1';
                    $risultato['msg'] = 'Una mail per il recupero della password è stata inviata alla tua casella di posta';
                }
                else{
                    $risultato['msg'] = "C'è stato un errore durante l' invio della mail";
                }
            }//if($mod)
            else{
                $risultato['msg'] = $giocatore->getError();
            } 
        }
        catch(Exception $e){
            $risultato['msg'] = $e->getMessage();
        }
    }//if(isset($_POST['email']) && $_POST['email'] != '')
    else{
            $risultato['msg'] = 'Inserisci un indirizzo mail';
    }

}//else di if(isset($_POST['email']) && $_POST['email'] != '')
if($ajax)echo json_encode($risultato);
else{
    $html = <<<HTML
<!DOCTYPE html>
<html lang="it">
    <head>
        <title>Mail</title>
        <meta charset="utf-8">
    </head>
    <body>
{$risultato['msg']}
    </body>
</html>
HTML;
echo $html;
}
?>