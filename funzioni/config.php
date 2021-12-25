<?php

$attesa = 3600;
$tabGiocatori = 'giocatori';

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