<?php
session_start();
require_once('config.php');
require_once('../oggetti/giocatore.php');
$risposta = array();
$risposta['msg'] = '';
$ajax = (isset($_POST['ajax']) && $_POST['ajax'] == '1');

//se l'utente è connesso
if(isset($_SESSION['giocatore'],$_SESSION['logged']) && $_SESSION['giocatore'] != '' && $_SESSION['logged']){
    if(isset($_POST['edit'])){
        $edit = $_POST['edit'];
        //cambio username
        if($edit == '1'){
            if(isset($_POST['username']) && preg_match(Giocatore::$regex['username'],$_POST['username'])){
                $giocatore = unserialize($_SESSION['giocatore']);
                $dati = array();
                $dati['campo'] = 'username';
                $dati['registrato'] = '1';
                $dati['username'] = $giocatore->getUsername();
                $dati['password'] = $_POST['password'];
                try{
                    $player = new Giocatore($dati);
                    $errore = $player->getErrno();
                    if($errore == 0 && $player->isLogged() === true){
                        $modifica = array();
                        $modifica['username'] = $_POST['username'];
                        $where = array();
                        $where['username'] = $player->getUsername();
                        $aggiorna = $player->update($modifica,$where);
                        if($aggiorna){
                            $risposta['done'] = '1';
                            $risposta['msg'] = 'Username aggiornato con successo';
                            $_SESSION['giocatore'] = serialize($player);
                        }
                        else{
                            $risposta['error'] = '1';
                            $risposta['msg'] = $player->getError();
                        }
                    }//if($errore == 0 || $player->isLogged() === true)
                    else{
                        $risposta['error'] = '1';
                        if($player->getErrno() == GIOCATOREERR_INCORRECTLOGINDATA){
                            $risposta['msg'] = 'Password non corretta';
                        }
                        else $risposta['msg'] = $player->getError();
                    }
                }
                catch(Exception $e){
                    $risposta['error'] = '1';
                    $risposta['msg'] = $e->getMessage();
                }
            }//if(isset($_POST['username']) && preg_match(Giocatore::$regex['username'],$_POST['username']))
        }//if($edit == '1')
        //cambio password
        if($edit == '2'){
            if(isset($_POST['nuova'],$_POST['confNuova']) && $_POST['nuova'] != '' && $_POST['confNuova'] != ''){
                if($_POST['nuova'] == $_POST['confNuova']){
                    $giocatore = unserialize($_SESSION['giocatore']);
                    $dati = array();
                    $dati['campo'] = 'username';
                    $dati['registrato'] = '1';
                    $dati['username'] = $giocatore->getUsername();
                    $dati['password'] = $_POST['nuova'];
                    try{
                        $player = new Giocatore($dati);
                        $errore = $player->getErrno();
                        if($errore == 0 && $player->isLogged() === true){
                            $modifica = array();
                            $modifica['password'] = $_POST['nuova'];
                            $where = array();
                            $where['username'] = $player->getUsername();
                            $aggiorna = $player->update($modifica,$where);
                            if($aggiorna){
                                $risposta['done'] = '1';
                                $risposta['msg'] = 'Password aggiornata con successo';
                                $_SESSION['giocatore'] = serialize($player);
                            }
                            else{
                                $risposta['error'] = '1';
                                $risposta['msg'] = $player->getError();
                            }
                        }//if($errore == 0 && $player->isLogged() === true)
                        else{
                            $risposta['error'] = '1';
                            if($player->getErrno() == GIOCATOREERR_INCORRECTLOGINDATA){
                                $risposta['msg'] = 'Password non corretta';
                            }
                            else $risposta['msg'] = $player->getError();       
                        }
                    }
                    catch(Exception $e){
                        $risposta['error'] = '1';
                        $risposta['msg'] = $e->getMessage();               
                    }
                }//if($_POST['nuova'] == $_POST['confNuova'])
                else{
                    $risposta['warning'] = '1';
                    $risposta['msg'] = 'Le due password non coincidono';
                }
            }//if(isset($_POST['nuova'],$_POST['confNuova']) && $_POST['nuova'] != '' && $_POST['confNuova'] != '')
            else{
                $risposta['warning'] = '1';
                $risposta['msg'] = 'Inserisci una nuova password e la conferma per continuare';
            }
        }//if($edit == '2')
    }//if(isset($_POST['edit']))
    else{
        $risposta['error'] = '1';
        $risposta['msg'] = 'Nessuna operazione selezionata';
    }
}//if(isset($_SESSION['giocatore'],$_SESSION['logged']) && $_SESSION['giocatore'] != '' && $_SESSION['logged'])
else{
    if($ajax){
        $risposta['warning'] = '1';
        $risposta['msg'] = 'Il tuo account è stato disconnesso';
    }
    else $risposta['msg'] = '<div><a href="../index.php">Effettua l\' accesso</a> per giocare</div>';
}
if($ajax)echo json_encode($risposta);
else{
    $html = <<<HTML
<!DOCTYPE html>
<html lang="it">
    <head>
        <title>Modifica profilo</title>
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