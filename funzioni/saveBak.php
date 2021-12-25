<?php
session_start();
require_once('config.php');
require_once('../oggetti/giocatore.php');
$risposta = array();
$risposta['msg'] = '';
$ajax = (isset($_POST['ajax']) && $_POST['ajax'] == '1');


//se l'utente è connesso
if(isset($_SESSION['giocatore'],$_SESSION['logged']) && $_SESSION['giocatore'] != '' && $_SESSION['logged']){
    if(isset($_POST['sequenza'],$_POST['tempo'],$_POST['spostamenti'])){
        $tempo = $_POST['tempo'];
        $spostamenti = $_POST['spostamenti'];
        if(preg_match(Giocatore::$regex['tempo'],$tempo) && is_numeric($spostamenti)){
            $sequenza = $_POST['sequenza'];
            $player = unserialize($_SESSION['giocatore']);
            $datiP = array();
            $datiP['campo'] = 'username';
            $datiP['username'] = $player->getUsername();
            $datiP['registrato'] = '1';
            try{
                //ottengo i dati dal database
                $giocatore = new Giocatore($datiP);
                $errN = $giocatore->getErrno();
                //ottengo i dati anche se l'utente 'non si è loggato'
                if($errN == 0 || $errN == GIOCATOREERR_INCORRECTLOGINDATA){
                    $aSalva = array();
                    //salvo l'ordine delle tessere in formato stringa
                    $aSalva['sequenza'] = implode(" ",$sequenza);
                    $aSalva['tempo'] = $tempo;
                    $aSalva['spostamenti'] = $spostamenti;
                    $where = array();
                    $where['username'] = $giocatore->getUsername();
                    //aggiorno la tabella
                    $aggiorna = $giocatore->update($aSalva,$where);
                    if($aggiorna){
                        $risposta['done'] = '1';
                        $risposta['msg'] = 'Il gioco è stato salvato';
                        $_SESSION['giocatore'] = serialize($giocatore);
                    }
                    else{
                        $risposta['error'] = '1';
                        $risposta['msg'] = $giocatore->getError();
                    }
                }//if($errN == 0 || $errN == GIOCATOREERR_INCORRECTLOGINDATA)
                else{
                    $risposta['error'] = '1';
                    $risposta['msg'] = $giocatore->getError();
                }
            }
            catch(Exception $e){
                $risposta['error'] = '1';
                $risposta['msg'] = $e->getMessage();
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
if($ajax)echo json_encode($risposta);
?>