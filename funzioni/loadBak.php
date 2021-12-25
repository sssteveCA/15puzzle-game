<?php

session_start();
require_once('config.php');
require_once('funzioni.php');
require_once('../oggetti/giocatore.php');
$errore = '';
$gioco = false; //true se è possibile iniziare il gioco
$sequenza = array('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15');
$tempo = null;
$spostamenti = null;
$risolvibile = false;

//se l'utente è connesso
if(isset($_SESSION['giocatore'],$_SESSION['logged']) && $_SESSION['giocatore'] != '' && $_SESSION['logged']){
     //Nuova partita
     if(isset($_POST['nuova']) && $_POST['nuova'] == '1'){
        //ordina in modo casuale gli elementi dell'array finché la sequenza non è risolvibile
        do{
            $sequenza = mischia();
            $risolvibile = isRisolvibile($sequenza);
        }while($risolvibile === false);

        if($risolvibile !== null){
            //variabili di sessione da passare alla pagina del gioco(gioco.php)
            $_SESSION['spostamenti'] = '0';
            $_SESSION['day'] = '0';
            $_SESSION['h'] = '0';
            $_SESSION['m'] = '0';
            $_SESSION['s'] = '0';
            $gioco = true;
        }
        //se l'array non è in un formato valido
        else{
            echo $errore = 'Formato della sequenza non valido';
            $gioco = false;       
        }
    }
    //Carica partita
    else if(isset($_POST['carica']) && $_POST['carica'] == '1'){
        $giocatore = unserialize($_SESSION['giocatore']);
        $dati = array();
        $dati['campo'] = 'username';
        $dati['registrato'] = '1';
        $dati['username'] = $giocatore->getUsername();
        //unset($_SESSION['giocatore']);
        try{
            $giocatore = new Giocatore($dati);
            $errN = $giocatore->getErrno();
            if($errN == 0 || $errN == GIOCATOREERR_INCORRECTLOGINDATA){
                //dati di salvataggio
                $sequenza = $giocatore->getSequenza();
                $spostamenti = $giocatore->getSpostamenti();
                $tempo = $giocatore->getTempo();
                if($spostamenti != null && $tempo != null){
                    //variabili di sessione da passare alla pagina del gioco(gioco.php)
                    $_SESSION['spostamenti'] = $giocatore->getSpostamenti();
                    $_SESSION['day'] = $tempo[0];
                    $_SESSION['h'] = $tempo[1];
                    $_SESSION['m'] = $tempo[2];
                    $_SESSION['s'] = $tempo[3];
                }
                $gioco = true;
            }//if($errN == 0 || $errN == GIOCATOREERR_INCORRECTLOGINDATA)
            else{
                $errore = $giocatore->getError();
            }
        }
        catch(Exception $e){
            $errore = $e->getMessage();
        }

    }//else if(isset($_POST['carica']) && $_POST['carica'] == '1')
    if($gioco){
        $_SESSION['gioco'] = '1';
        if(isset($sequenza,$_SESSION['spostamenti'],$_SESSION['day'],$_SESSION['h'],$_SESSION['m'],$_SESSION['s'])){
            $l = count($sequenza);
            //aggiungo il div senza numero alla fine dell'array(se è stata scelta una nuova partita)
            if($l < 16){
                array_push($sequenza,'vuoto');
                //nuova posizione per il div vuoto(da 0 a 15)
                $newPos = mt_rand(0,$l);
                $num = $sequenza[$newPos];
                //il numero va nell'ultima posizione
                $sequenza[$l] = $num;
                //la cella vuota occuperà ora la posizione $newPos
                $sequenza[$newPos] = 'vuoto';
            }
            $_SESSION['sequenza'] = $sequenza;
            /*echo '<pre>';
            var_dump($_SESSION['sequenza']);
            echo '</pre>';*/
        }
        //é possibile avviare ma non è stato trovato nessun salvataggio
        else $_SESSION['errore'] = 'Nessun salvataggio trovato';
        header('location: ../gioco.php');
    }
    else{
        $_SESSION['gioco'] = '0';
        $_SESSION['errore'] = 'Impossibile avviare il gioco: '.$errore;
        echo $_SESSION['errore'];
        unset($_SESSION['gioco'],$_SESSION['errore']);
    }
}
?>