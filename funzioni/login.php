<?php
ob_start();
session_start();
require_once('config.php');
require_once('../oggetti/giocatore.php');
$errore = '';

if(isset($_SESSION['giocatore'],$_SESSION['logged']) && $_SESSION['giocatore'] != '' && $_SESSION['logged']){
    header('location: menuGioco.php');
}
else{
    $_SESSION['logged'] = false;
    if(isset($_POST['username'],$_POST['password']) && $_POST['username'] != '' && $_POST['password'] != ''){
        $dati = array();
        $dati['campo'] = 'username';
        $dati['username'] = $_POST['username']; 
        $dati['password'] = $_POST['password'];
        $dati['registrato'] = '1';
        try{
            $giocatore = new Giocatore($dati);
            $errore = $giocatore->getError();
            $login = $giocatore->isLogged();
            //accesso autorizzato
            if($errore == 0 && $login){
                $_SESSION['giocatore'] = serialize($giocatore);
                $_SESSION['logged'] = true;
                header('location: ../menuGioco.php');            
            }
            else{
                $errore = $giocatore->getError();
                header('refresh:10;url=../index.php');
            }
        }
        catch(Exception $e){
            $errore = $e->getMessage();
            header('refresh:10;url=../index.php');
        }
    }//if(isset($_POST['username'],$_POST['password'])){
    else{
        $errore = '<a href="index.php">Effettua l\' accesso</a> per giocare';
    }
}
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
    </head>
    <body>
    <?php echo $errore; ?>
    </body>
</html>
