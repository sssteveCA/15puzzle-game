<?php

//fino ai 10 tempi più brevi in cui il gioco è stato completato
function getBestRecords($mysqlHost,$mysqlUser,$mysqlPass,$mysqlDb,$tabGiocatori){
    $arrayRecords = null;
    $mysqli = new mysqli($mysqlHost,$mysqlUser,$mysqlPass,$mysqlDb);
    if($mysqli->connect_errno !== 0){
        return $arrayRecords;
    }
    $query = <<<SQL
SELECT `username`,`record` FROM `{$tabGiocatori}` WHERE `record` IS NOT NULL ORDER BY `record` LIMIT 10;
SQL;
    $result = $mysqli->query($query);
    if($result){
        if($result->num_rows > 0){
            $i = 0;
            $arrayRecords = array();
            while($riga = $result->fetch_array(MYSQLI_ASSOC)){
                $arrayRecords[$i]["posizione"] = $i + 1;
                $arrayRecords[$i]["username"] = $riga["username"];
                $arrayRecords[$i]["record"] = $riga["record"];
                $i++;
            }
        }//if($result->num_rows > 0)
        $result->free();
    }//if($result)
    $mysqli->close();
    return $arrayRecords;
}

//scambia la posizione delle tessere un numero pari di volte
function mischia(){
    $min=4;
    $max=8;
    $sequenza = array('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15');
    //finché non ottengo un numero di scambi da effettuare pari continuo il ciclo
    /*do{
        $n = mt_rand(8,16);
    }while($n & 1);*/
    $n = 2 * mt_rand($min, $max);
    //scambio le tessere un numero n di volte
    for($i = 0; $i < $n; $i++){
        //cerco 2 indici dell'array in maniera casuale(finchè non sono diversi)
        do{
            $indexA = mt_rand(0,14);
            $indexB = mt_rand(0,14);
        }while($indexA == $indexB);
        $tmpA = $sequenza[$indexA];
        $tmpB = $sequenza[$indexB];
        //scambio i valori
        $sequenza[$indexB] = $tmpA;
        $sequenza[$indexA] = $tmpB;
    }
    return $sequenza;
}

//controlla che la sequenza generata sia risolvibile
function isRisolvibile($sequenza){
    $risolvibile = null;
    $l = count($sequenza);
    /*indice elemento dell'array che precede gli altri con cui deve essere confrontato*/
    $pos = 1; 
    $inv = 0; //somma delle inversioni
    //controlla che l'array sia composto da 15 elementi
    if($l == 15){
        for($i = 0; $i < $l; $i++){
            //controllo quanti sono i numeri inferiori dopo $sequenza[$i]
            for($j = $pos; $j < $l; $j++){
                if($sequenza[$i] > $sequenza[$j])$inv++;
            }
            $pos++;
        }
        //se il numero è pari la sequenza è risolvibile
        if($inv & 1)$risolvibile = false;
        else $risolvibile = true;
    }
    return $risolvibile;
}
?>