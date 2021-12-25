<?php

session_start();
require_once('config.php');
require_once('../oggetti/giocatore.php');
require_once('../oggetti/salvataggio.php');
$risposta = array();
$ajax = (isset($_POST['ajax']) && $_POST['ajax'] == '1');

//se il giocatore è connesso
if(isset($_SESSION['giocatore'],$_SESSION['logged']) && $_SESSION['giocatore'] != '' && $_SESSION['logged']){
    $giocatore = unserialize($_SESSION['giocatore']);
    //per permettere che la finestra di salvataggio venga mostrata anche se non ci sono ancora partite salvate
    $idS = Salvataggio::getPlayerSaves($giocatore->getUsername());
    //se sono stati trovati salvataggi
    if(!empty($idS)){
        $risposta['empty'] = '0';
        foreach($idS as $id){
            try{
                $aSave = array();
                $aSave['id'] = $id;
                $save = new Salvataggio($aSave);
                $errno = $save->getErrno();
                if($errno == 0){
                    $slot = $save->getSlot();
                    $risposta['done'] = '1';
                    $risposta['saves'][$slot]['id'] = $save->getId();
                    $risposta['saves'][$slot]['data'] = $save->getData();
                    $risposta['saves'][$slot]['slot'] = $slot;
                    $risposta['saves'][$slot]['sequenza'] = $save->getSequenza();
                    $risposta['saves'][$slot]['tempo'] = $save->getTempo();
                    $risposta['saves'][$slot]['spostamenti'] = $save->getSpostamenti();
                }
                else{
                    $risposta['error'] = '1';
                    switch($errno){
                        case SALVATAGGIOERR_INFONOTGETTED:
                        case SALVATAGGIOERR_QUERYERROR:
                        case SALVATAGGIOERR_IDNOTEXISTS:
                            $risposta['msg'] = ERROR." {$errno}";
                            break;
                        default:
                            $risposta['msg'] = UNKNOWN_ERROR;
                            break;
                    }
                }
            }
            catch(Exception $e){
                $risposta['error'] = '1';
                $risposta['msg'] = $e->getMessage();
            }
        }//foreach($idS as $id)

    }//if(!empty($idS))
    else{
        $risposta['done'] = '1';
        $risposta['ding'] = '1';
        $risposta['empty'] = '1';
        if(isset($_POST['carica'])&& $_POST['carica'] == '1')$risposta['msg'] = 'Nessun salvataggio trovato';
    }

}//if(isset($_SESSION['giocatore'],$_SESSION['logged']) && $_SESSION['giocatore'] != '' && $_SESSION['logged'])
else{
    $risposta['msg'] = 'Il tuo profilo è stato disconnesso';
}

if($ajax)echo json_encode($risposta);
?>