<?php
session_start();
require_once('config.php');
require_once('../oggetti/giocatore.php');

$risposta = array();
//$risposta['msg'] = '';
$ajax = (isset($_POST['ajax']) && $_POST['ajax'] == '1');

//se l'utente è connesso
if(isset($_SESSION['giocatore'],$_SESSION['logged']) && $_SESSION['giocatore'] != '' && $_SESSION['logged']){
    //se il record esiste ed è un numero
    if(isset($_POST['record']) && is_numeric($_POST['record'])){
        $giocatore = unserialize($_SESSION['giocatore']);
        $record = $_POST['record'];
        $datiG = array();
        $datiG['campo'] = 'username';
        $datiG['username'] = $giocatore->getUsername();
        $datiG['registrato'] = '1';
        //$datiG['record'] = $record;
        try{
            $player = new Giocatore($datiG);
            $errno = $giocatore->getErrno();
            //ottengo i dati anche se l'utente 'non si è loggato'
            if($errno == 0 || $errno == GIOCATOREERR_INCORRECTLOGINDATA){
                $agg = $player->setRecord($record);
                if($agg){
                    $risposta['ok'] = '1';
                    $_SESSION['giocatore'] = serialize($player);
                }
                else{
                    $risposta['error'] = '1';
                    $errno = $player->getErrno();
                    switch($errno){
                        case GIOCATOREERR_DATANOTUPDATED:
                            break;
                        case GIOCATOREERR_QUERYERROR:
                        case GIOCATOREERR_INVALIDDATAFORMAT:
                            $risposta['msg'] = ERROR." {$errno}";
                            break;
                        default:
                            $risposta['msg'] = UNKNOWN_ERROR;
                            break;
                    }
                }
            }//if($errno == 0 || $errno == GIOCATOREERR_INCORRECTLOGINDATA){
            else{
                $risposta['error'] = '1';
                $errno = $player->getErrno();
                switch($errno){
                    case GIOCATOREERR_DATANOTSET:
                    case GIOCATOREERR_INVALIDFIELD:
                        $risposta['msg'] = ERROR." {$errno}";
                        break;
                    default:
                        $risposta['msg'] = UNKNOWN_ERROR;
                        break;
                }
            }
        }
        catch(Exception $e){
            $risposta['error'] ='1';
            $risposta['msg'] = $player->getError();
        }

    }//if(isset($_POST['record']) && is_numeric($_POST['record']))
    else{
        $risposta['error'] ='1';
        $risposta['msg'] = 'Il valore richiesto non esiste o è in un formato non valido';
    }
}

if($ajax)echo json_encode($risposta,JSON_UNESCAPED_UNICODE);
?>