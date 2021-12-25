<?php
session_start();
require_once('config.php');
require_once('../oggetti/giocatore.php');
require_once('../oggetti/salvataggio.php');
$risposta = array();
$risposta['msg'] = '';
$ajax = (isset($_POST['ajax']) && $_POST['ajax'] == '1');

//var_dump($_POST);


//se l'utente è connesso
if(isset($_SESSION['giocatore'],$_SESSION['logged']) && $_SESSION['giocatore'] != '' && $_SESSION['logged']){
    if(isset($_POST['slot'],$_POST['sequenza'],$_POST['tempo'],$_POST['spostamenti'])){
        $tempo = $_POST['tempo'];
        $spostamenti = $_POST['spostamenti'];
        $slot = $_POST['slot'];
        if(preg_match(Salvataggio::$regex['tempo'],$tempo) && is_numeric($spostamenti) && is_numeric($slot)){
            $sequenza = $_POST['sequenza'];
            $player = unserialize($_SESSION['giocatore']);
            $datiP = array();
            $datiP['idg'] = $player->getId();
            $datiP['data'] = date('Y-m-d H:i:s');
            $datiP['slot'] = $slot;
            $datiP['sequenza'] = $sequenza;
            $datiP['tempo'] = $tempo;
            $datiP['spostamenti'] = $spostamenti;
            try{
                //ottengo i dati dal database
                $salvataggio = new Salvataggio($datiP);
                $errN = $salvataggio->getErrno();
                //ottengo i dati anche se l'utente 'non si è loggato'
                if($errN == 0){
                    $risposta['done'] = '1';
                    $risposta['msg'] = 'La partita è stata salvata';      
                }//if($errN == 0 || $errN == GIOCATOREERR_INCORRECTLOGINDATA)
                else{
                    $risposta['error'] = '1';
                    $risposta['msg'] = $salvataggio->getError();
                    //$risposta['query'] = $salvataggio->getQuery();
                }
            }
            catch(Exception $e){
                $risposta['error'] = '1';
                $risposta['msg'] = $e->getMessage();
                file_put_contents(LOGFILE,$risposta['msg'],FILE_APPEND);
                $risposta['msg'] = UNKNOWN_ERROR;
            }
        }//if(preg_match(Giocatore::$regex['tempo'],$tempo) && is_numeric($spostamenti))  
        else{
            $risposta['error'] = '1';
            $risposta['msg'] = 'Dati da salvare non validi';
        }
    }//if(isset($_POST['sequenza'],$_POST['tempo'],$_POST['spostamenti'])))
    else{
        $risposta['error'] = '1';
        $risposta['msg'] = 'Errore durante il rilevamento dell\' ordine delle tessere';
    }
}//if(isset($_SESSION['giocatore'],$_SESSION['logged']) && $_SESSION['giocatore'] != '' && $_SESSION['logged'])
else{
    $risposta['warning'] = '1';
    $risposta['msg'] = 'Il tuo account è stato disconnesso';
}
if($ajax)echo json_encode($risposta,JSON_UNESCAPED_UNICODE);
?>