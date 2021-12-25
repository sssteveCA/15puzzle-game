<?php
session_start();
require_once('funzioni/config.php');
require_once('funzioni/funzioni.php');

?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <title>Record</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="css/global.css" type="text/css">
        <link rel="stylesheet" href="css/record.css" type="text/css">
        <script src="js/jquery-3.6.0.min.js"></script>
        <script src="js/gioco.js"></script>
    </head>
    <body>
<?php
//se il giocatore è connesso
if(isset($_SESSION['giocatore'],$_SESSION['logged']) && $_SESSION['giocatore'] != '' && $_SESSION['logged']){
    $records = getBestRecords($mysqlHost,$mysqlUser,$mysqlPass,$mysqlDb,$tabGiocatori);
?>
    <div id="indietro">
        <a href="menuGioco.php"><img src="immagini/back.jpg" alt="indietro" title="indietro"></a>
        <a href="menuGioco.php">Indietro</a>
    </div>
    <h1 id="titolo" title="gioco del quindici">Gioco del quindici</h1>
<?php
    //echo '<script>console.log("'.json_encode($records).'");</script>';
    if($records !== null){

?>
    <div id="tabella">
<script>
    var records = <?php echo json_encode($records); ?>;
    //console.log(records);
    var tHeight = 400; //altezza tabella
    var tWidth = 700; //larghezza tabella
    var fontDim; //dimensione testo celle tabella
    var table = $('<table>');
    var caption; //titolo tabella
    var tr; //riga
    var th1,th2,th3; //intestazioni
    var td1,td2,td3; //dati
    /*i secondi complessivi sono mostrati come giorni,ore,minuti,secondi impiegati*/
    var day,h,m,s; 
    var secondi; //secondi totali impiegati
    var strTempo; //tempo in formato giorni,ore,minuti,secondi
    table.attr({
        border : '1'
    });
    //inserisco il titolo della tabella
    caption = $('<caption>');
    caption.html('Giocatori con i migliori record');
    table.append(caption);
    //inserisco le intestazioni nella tabella
    tr = $('<tr>');
    th1 = $('<th>');
    th1.attr({
        class : 'pos'
    });
    th1.html('POSIZIONE');
    th2 = $('<th>');
    th2.attr({
        class : 'player'
    });
    th2.html('GIOCATORE');
    th3 = $('<th>');
    th3.attr({
        class : 'record'
    });
    th3.html('RECORD PERSONALE');
    tr.append(th1,th2,th3);
    table.append(tr);
    //inserisco i dati nella tabella
    for(i in records){
        time = '';
        tr = $('<tr>');
        /*console.log(records[i]['username']);
        console.log(records[i]['record']);*/
        td1 = $('<td>');
        td1.attr({
            class : 'pos'
        });
        td1.html((records[i]['posizione'])+'°');
        td2 = $('<td>');
        td2.attr({
            class : 'player'
        });
        td2.html(records[i]['username']);
        td3 = $('<td>');
        td3.attr({
            class : 'record'
        });
        //ricavo dai secondi totali i giorni,le ore,i minuti e i secondi rimanenti impiegati
        secondi = parseInt(records[i]['record']);
        day = parseInt(secondi/86400);
        secondi -= (day*86400);
        h = parseInt(secondi/3600);
        secondi -= (h*3600);
        m = parseInt(secondi/60);
        secondi -= (m*60);
        s = secondi;
        time = timerStr(day,h,m,s);
        td3.html(time);
        tr.append(td1,td2,td3);
        table.append(tr);
    }//for(i in records)
    $('#tabella').append(table);
</script>
    </div>
<?php
    }//if($records !== null)
    else{
        echo '<h1>Nessun record trovato</h1>';
    }
}//if(isset($_SESSION['giocatore'],$_SESSION['logged']) && $_SESSION['giocatore'] != '' && $_SESSION['logged'])
else{
    echo '<div><a href="index.php">Effettua l\' accesso</a> per giocare</div>';
}
?>
    </body>
</html>