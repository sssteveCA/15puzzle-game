<?php
ob_start();
session_start();
require_once('config.php');
require_once('../oggetti/giocatore.php');

$ajax = (isset($_POST['ajax']) && $_POST['ajax'] == '1');
$risposta = array();
$risposta['msg'] = '';
$risposta['done'] = '0';

//se l'utente è collegato non eseguo la pagina
if(isset($_SESSION['giocatore'],$_SESSION['logged']) && $_SESSION['giocatore'] != '' && $_SESSION['logged']){
    header('location: menuGioco.php');
}
else{
    if(isset($_POST['email'],$_POST['username'],$_POST['password'],$_POST['confPassword'])){
        if($_POST['password'] == $_POST['confPassword']){
            try{
                $dati = array();
                $dati['email'] = $_POST['email'];
                $dati['username'] = $_POST['username'];
                $dati['password'] = $_POST['password'];
                $giocatore = new Giocatore($dati);
                $errno = $giocatore->getErrno();
                if($errno == 0){
                    $codAut = $giocatore->getCodAut();
                    $indAtt = dirname($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'],2).'/attiva.php';
                    $codIndAtt = $indAtt.'?codAut='.$codAut;
                    $headers = <<<HEADER
From: Admin <noreply@localhost.lan>
Reply-to: <noreply@localhost.lan>
Content-type: text/html
MIME-Version: 1.0
HEADER;
                    $html = <<<HTML
<!DOCTYPE html>
<html lang="it">
    <head>
        <title>Attivazione profilo</title>
        <meta charset="utf-8">
        <style>
            body{
                display: flex;
                justify-content: center;
            }
            div#linkAtt{
                padding: 10px;
                background-color: orange;
                font-size: 20px;
            }
        </style>
    <head>
    <body>
        <div id="linkAtt">
Completa la registrazione facendo click sul link sottostante:
<p><a href="{$codIndAtt}">{$codIndAtt}</a></p>
oppure vai all'indirizzo <p><a href="{$indAtt}">{$indAtt}</a></p> e incolla il seguente codice: 
<p>{$codAut}</p>
        </div>
    </body>
</html>
HTML;
                    $sent = $giocatore->sendEmail($giocatore->getEmail(),'Attivazione profilo',$html,$headers);
                    if($sent){
                        $risposta['done'] = '1';
                        $risposta['msg'] = 'Accedi alla tua casella di posta per completare la registrazione';
                        if(!$ajax)header('refresh:10;url=../index.php');
                    }
                    else{
                        $risposta['error'] = '1';
                        $risposta['msg'] = $giocatore->getError();
                        //if(!$ajax)header('refresh:10;url=../registrati.php');
                    }
                }//if($errno == 0)
                else{
                    $risposta['error'] = '1';
                    switch($errno){
                        case GIOCATOREERR_USERNAMEMAILEXIST:
                            $risposta['msg'] = $giocatore->getError();
                            break;
                        default:
                            $risposta['msg'] = UNKNOWN_ERROR;
                            break;
                    }
                    //$risposta['msg'] .= ' '.$giocatore->getQuery();
                }
            }
            catch(Exception $e){
                $risposta['error'] = '1';
                $risposta['msg'] = $e->getMessage();
            }
        }//if($_POST['password'] == $_POST['confPassword'])
        else{
            $risposta['warning'] = '1';
            $risposta['msg'] = 'Le due password non coincidono';
        }
    }//if(isset($_POST['email'],$_POST['username'],$_POST['password'],$_POST['confPassword']))
    else{
        if($ajax){}
        else{
            $risposta['msg'] = 'Compila il <a href="registrati.php">form</a> per eseguire questa pagina';
        }
    }

}//else (utente non collegato)
if($ajax)echo json_encode($risposta,JSON_UNESCAPED_UNICODE);
else{
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <title>Registrazione</title>
        <meta charset="utf-8">
    <head>
    <body>
    <?php echo $risposta['msg']; ?>
    </body>
</html>
<?php
}//else (esecuz)
?>