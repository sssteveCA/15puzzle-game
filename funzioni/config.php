<?php

$attesa = 3600;
$tabGiocatori = 'giocatori';

//messaggio errore sconosciuto
const ERROR = 'Si è verificato un errore. Codice ';
const ERROR_LOG = 'Si è verificato un errore. Controllare il file di log';
const UNKNOWN_ERROR = 'Errore sconosciuto';

//file di log
const LOGFILE = "log.txt";

if($_SERVER['SERVER_NAME'] == 'localhost'){
    $mysqlHost = 'localhost';
    $mysqlUser = 'root';
    $mysqlPass = '';
    $mysqlDb = 'stefano';
}
else{
    $mysqlHost = '';
    $mysqlUser = '';
    $mysqlPass = '';
    $mysqlDb = '';
}

if (! function_exists("array_key_last")) {
    function array_key_last($array) {
        if (!is_array($array) || empty($array)) {
            return NULL;
        }
       
        return array_keys($array)[count($array)-1];
    }
}


?>