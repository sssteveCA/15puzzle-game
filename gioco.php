<?php
session_start();
require_once('oggetti/giocatore.php');
$errore = '';
$gioco = false; //true se è possibile iniziare il gioco
$sequenza = array('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15');
$risposta = array();



//se l'utente è connesso
if(isset($_SESSION['giocatore'],$_SESSION['logged']) && $_SESSION['giocatore'] != '' && $_SESSION['logged']){
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <title>Gioco del quindici</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="css/global.css" type="text/css">
        <link rel="stylesheet" href="css/gioco.css" type="text/css">
        <link rel="stylesheet" href="jqueryUI/jquery-ui.min.css" type="text/css">
        <link rel="stylesheet" href="jqueryUI/jquery-ui.theme.min.css" type="text/css">
        <script src="js/jquery-3.6.0.min.js"></script>
        <script src="jqueryUI/jquery-ui.min.js"></script>
        <script src="js/dialog.js"></script>
        <script src="js/gioco.js"></script>
        <style></style>
    </head>
    <body>
        <h1 id="titolo" title="gioco del quindici">Gioco del quindici</h1>
        
<?php
            if(isset($_SESSION['gioco'])){
                if($_SESSION['gioco'] == '1'){
                    if(isset($_SESSION['errore'])){
                        echo '<h1>'.$_SESSION['errore'].'</h1>';
?>
        <div id="indietro">
            <a href="funzioni/backMenu.php"><img src="immagini/back.jpg" alt="indietro" title="indietro"></a>
            <a href="funzioni/backMenu.php">Indietro</a>
        </div>
<?php
                    }//if(isset($_SESSION['errore']))
                    else{
?>   
        <div id="area">
        </div>
        <div id="punteggi">
            <div id="tempo" class="pt"></div>
            <div id="spostamenti" class="pt"></div>
        </div>
        <div id="side">
            <input type="button" id="salva" value="SALVA PARTITA">
            <input type="button" id="esci" value="TORNA AL MENU">
        </div>
        <script>
    var tesseraDim = 70; //dimensione di ciascuna tessera
    var margin = 5;
    var spostamento = tesseraDim+margin;
    var click = false; //true se è stato fatto click su una tessera
    var tesseraC; //tessera su cui è stato fatto il click
    //posizione x e y di ciascuna tessera
    var xT = margin;
    var yT = margin;
    var area = $('#area');
    var sequenza = <?php echo json_encode($_SESSION['sequenza']); ?>;
    var areaDim = (tesseraDim*4)+(margin*3);
    var vuotoX; //posizione X del div senza numero
    var vuotoY; //posizione Y del div senza numero
    var tXpre; //posizione X iniziale della tessera selezionata
    var tYpre; //posizione Y iniziale della tessera selezionata
    var tX; //posizione X della tessera selezionata
    var tY; //posizione Y della tessera selezionata
    var animation; //codice CSS contenente l'animazione da eseguire
    var ordineT; //posizione tessera che verrà spostata
    var ordineV; //posizione div vuoto che verrà spostato
    var tTemp; //numero tessera spostata, usato per aggiornare l'array sequenza
    var vTemp; //div vuoto spostato, usato per aggiornare l'array sequenza
    var sorted = false; //controlla che le tessere siano ordinate in senso crescente e se lo sono il gioco termina
    var audio; //file wav da riprodurre
    var tId; //id del setInterval
    var time; //stringa che contiene il tempo di gioco
    //tempo trascorso
    /*inserisco le variabili di sessione in variabili javascript e le converto in numeri*/
    var day = <?php echo $_SESSION['day']; ?>;
    day = parseInt(day);
    var h = <?php echo $_SESSION['h']; ?>;
    h = parseInt(h);
    var m = <?php echo $_SESSION['m']; ?>;
    m = parseInt(m);
    var s = <?php echo $_SESSION['s']; ?>;
    s = parseInt(s);
    var spostamenti = <?php echo $_SESSION['spostamenti']; ?>;
    spostamenti = parseInt(spostamenti);
    area.css({
        height : areaDim+'px',
        width : areaDim+'px',
        padding : margin+'px',
        border : '1px solid black'
    });
    /*creazione delle tessere nell'ordine casuale generato o secondo il salvataggio*/
    for(var i = 0; i < 16; i++){
        //console.log(sequenza);
        var tessera = $('<div>');
        var idT = '';
        var html = '';
        if(sequenza[i] != 'vuoto'){
            //inserisce il numero come testo nell'elemento tessera
            idT = 'n'+sequenza[i];
            html = sequenza[i];
        }
        else{
            //Spazio vuoto
            idT = sequenza[i];
            html = '';
        }
        tessera.attr({
            id : idT,
            class : 'tessera'
        });
        tessera.html(html);
        tessera.css({
            top : yT+'px',
            left : xT+'px'
        });
        area.append(tessera);
        var d = i+1; //numero tessera
        var q = parseInt(d/4); //
        var r = d % 4;
        /*dopo 4 tessere passo alla riga successiva,
        riporto la posizione X all'estremo di sinistra e aumento la posizione Y 
        per la tessera successiva*/
        if(r == 0 && q > 0){
            //console.log("d = "+d+" q = "+q+" r = "+r);
            xT = margin;
            yT += spostamento;
        }
        else{
            //altrimenti mi sposto solo in X in avanti
            xT += spostamento;
        }
        
    }//for(var i = 1; i<= 16; i++)
    //dimensione di ciascuna tessera
    $('.tessera').css({
        height : tesseraDim+'px',
        width : tesseraDim+'px'
    });
    //la tessera invisibile non ha nessun bordo
    $('.tessera').not('#vuoto').css({
        border : '1px solid black'
    });
    gioco = true;
    //aggiorna il punteggio e il tempo impiegato
    tId = setInterval(function(){
        if(gioco === true){
            time = timerStr(day,h,m,s);
            s++;
            if(s > 59){
                s = 0;
                m++;
            }
            if(m > 59){
                m = 0;
                h++;
            }
            if(h > 23){
                h = 0;
                day++;
            }
            $('#tempo').html('TEMPO: '+time);
            $('#spostamenti').html('SPOSTAMENTI: '+spostamenti);
        }
    },1000); //setInterval(function()
    //seleziono una tessera da muovere
    $('.tessera').not('#vuoto').on('click',function(e){
        e.stopPropagation();
         //se il gioco non è finito
         if(gioco === true){
             //console.log('click tessera');
            $('.tessera').css('background-color','transparent');
            $(this).css('background-color','orange');
            tesseraC = $(this);
            vuotoX = parseInt($('#vuoto').css('left')); //posizione X del div dello spazio vuoto
            vuotoY = parseInt($('#vuoto').css('top')); //posizione Y del div dello spazio vuot
            tX = parseInt(tesseraC.css('left')); //posizione X della tessera selezionata
            tY = parseInt(tesseraC.css('top')); ////posizione X della tessera selezionata
            tXpre = tX; //posizione X della tessera selezionata, nel frame iniziale dell'animazione
            tYpre = tY; //posizione Y della tessera selezionata, nel frame iniziale dell'animazione
            ordineT = 0;
            //posizione della tessera selezionata da 0 a 15
            ordineT += parseInt(tY/spostamento)*4 + parseInt(tX/spostamento);
            ordineV = 0;
            //posizione deldiv dello spazio vuoto da 0 a 15
            ordineV += parseInt(vuotoY/spostamento)*4 + parseInt(vuotoX/spostamento);
            //console.log(tesseraC);  
            /*console.log("ordineT = "+ordineT);
            console.log("ordineV = "+ordineV);*/
            /*variabili temporanee per fare lo scambio tra il div vuoto e la tessera selezionata */
            tTemp = sequenza[ordineT];
            vTemp = sequenza[ordineV];
            /*console.log("tTemp "+tTemp);
            console.log("vTemp "+vTemp);*/ 
            //se il div vuoto si trova adiacente a sinistra
            if((tX == (vuotoX + spostamento)) && tY == vuotoY){
                tX -= spostamento;
                vuotoX += spostamento;
                sequenza[ordineT - 1] = tTemp;
                sequenza[ordineV + 1] = vTemp;
                spostamenti++;
            }  
            //se il div vuoto si trova adiacente a destra
            else if((tX == (vuotoX - spostamento)) && tY == vuotoY){
                tX += spostamento;
                vuotoX -= spostamento; 
                sequenza[ordineT + 1] = tTemp;
                sequenza[ordineV - 1] = vTemp;
                spostamenti++;
            }
            //se il div vuoto si trova adiacente sotto
            else if((tY == (vuotoY - spostamento)) && tX == vuotoX){
                tY += spostamento;
                vuotoY -= spostamento;
                sequenza[ordineT + 4] = tTemp;
                sequenza[ordineV - 4] = vTemp;
                spostamenti++;
            }
            //se il div vuoto si trova adiacente sopra
            else if((tY == (vuotoY + spostamento)) && tX == vuotoX){
                tY -= spostamento;
                vuotoY += spostamento;
                sequenza[ordineT - 4] = tTemp;
                sequenza[ordineV + 4] = vTemp;
                spostamenti++;
            }
            //definisco i keyframes dell'animazione nel tag style
            animation = `@keyframes sposta{
    0%{
        top: `+tYpre+`px;
        left: `+tXpre+`px;
    }
    100%{
        top: `+tY+`px;
        left: `+tX+`px;
    }
}`;
            $('style').html(animation);
            //la tessera selezionata si sposta in modo animato
            tesseraC.css({
                /*top : tY+'px',
                left : tX+'px'*/
                'animation-name' : 'sposta',
                'animation-duration' : '0.5s',
                'animation-delay' : '0.5s',
                'animation-iteration-count' : '1',
                'animation-direction' : 'normal',
                'animation-timing-function' : 'linear',
                'animation-fill-mode' : 'forwards'
            });
            //al termine dell'animazione la cancello dalla tessera che l'ha eseguita
            tesseraC.on('animationend',function(){
                $(this).css({
                    'animation-name' : 'none',
                    top : tY+'px',
                    left : tX+'px'
                });
                //$('style').html('');
            });
            //contemporaneamente si sposta il div con lo spazio vuoto
            $('#vuoto').css({
                top : vuotoY+'px',
                left : vuotoX+'px'
            });
            /*console.log("DOPO");
            console.log("vuotoX = "+vuotoX);
            console.log("vuotoY = "+vuotoY);
            console.log("tXpre = "+tXpre);
            console.log("tYpre = "+tYpre);
            console.log("tX = "+tX);
            console.log("tY = "+tY);
            console.log("sequenza");
            console.log(sequenza);*/
            //se le tessere sono ordinate in senso crescente
            sorted = isOrdinato(sequenza);
            //if(spostamenti == 4)sorted = true;
            if(sorted === true){
                //il conteggio del tempo e degli spostamenti vengono bloccati
                clearInterval(tId);
                //aggiornamento dopo aver bloccato il loop
                $('#tempo').html('TEMPO: '+time);
                $('#spostamenti').html('SPOSTAMENTI: '+spostamenti);
                //aggiorno se il tempo impiegato è il più basso
                setRecord(day,h,m,s);
                $('.tessera').css('background-color','transparent');
                //div che conterrà la scritta 'GIOCO COMPLETATO'
                var divWin = $('<div>');
                divWin.attr({
                    id : 'divWin'
                });
                divWin.css({
                    display : 'inline',
                    margin : '0 auto',
                    width : '80%',
                    height : 'auto',
                    border : '4px solid black',
                    'background-color' : 'white',
                    'text-align' : 'center',
                    'font-weight' : 'bold',
                    'margin-top' : '10px',
                    'z-index' : '10'
                });
                divWin.html("GIOCO COMPLETATO");
                $('body').append(divWin);
                //dimensione del font in base alla grandezza del div
                var divW = divWin.width();
                divWin.css('font-size',(divW/15)+'px');
                //audio gioco completato
                audio = new Audio('audio/complete.wav');
                audio.play();
                gioco = false;
            }//if(sorted == true)
         }//if(gioco === true)
    });//$('.tessera').not('#vuoto').on('click',function(e)
    //deseleziono la tessera su cui è stato fatto click precedentemente
    $(document).on('click',function(){
            //console.log('click body');
            if(gioco){
                $('.tessera').css('background-color','transparent');
            }   
    });

    $('#salva').on('click',function(){
        console.log("salva");
        //se il gioco non è finito è possibile salvare
        if(gioco === true){
            var ajax = {};
            ajax['ajax'] = '1';
            $.ajax({
                url : 'funzioni/getSaves.php',
                method : 'post',
                data : ajax,
                success : function(risposta, stato, xhr){
                    console.log(risposta);
                    var risp = JSON.parse(risposta);
                    if(risp.hasOwnProperty('msg')){

                    }
                    if(risp.hasOwnProperty('done')){
                        //salvataggi trovati
                        if(risp.done == '1'){
                            var slot;
                            var html = '';
                            var container = $('<div>');
                            container.attr('id','container');
                            for(slot = 1; slot <= 5; slot++){
                                //creazione della dialogbox con gli slot di salvataggio
                                var divSalva = $('<div>');
                                divSalva.attr({
                                    id : 'slot'+slot,
                                    class : 'divSalva'
                                });
                                    divSalva.css({
                                    margin : '5px',
                                    display : 'flex',
                                    'justify-content' : 'center',
                                    'align-items' : 'center'
                                });
                                var input = $('<input>');
                                input.attr({
                                    type : 'button',
                                    id : 'i'+slot,
                                    class : 'bSlot',
                                    value : 'SALVA'
                                });
                                input.css('margin-right', '10px');
                                var divText = $('<div>');
                                divText.attr('class','divText');
                                divText.css({
                                    border : '1px solid black',
                                    width : '350px',
                                    height : '60px',
                                    'font-size' : '12px',
                                    display : 'flex',
                                    'justify-content' : 'center',
                                    'align-items' : 'center'
                                });
                                if(risp.hasOwnProperty('saves')){
                                    //if(typeof(risp.saves.slot != "undefined")){
                                    if(risp['saves'][slot] != undefined){
                                        var dataBis = risp['saves'][slot]['data'];
                                        var sequenzaBis = risp['saves'][slot]['sequenza'];
                                        var tempoBis =  risp['saves'][slot]['tempo'];
                                        var spostamentiBis = risp['saves'][slot]['spostamenti'];
                                        html = `DATA: `+dataBis+`<br>
ORDINE: `+sequenzaBis+`<br>
TEMPO: `+tempoBis+`<br>
SPOSTAMENTI: `+spostamentiBis+``;
                                        divText.html(html);
                                    }//if(typeof(risp.saves.slot != "undefined"))
                                    else divText.html('Vuoto');
                                }//if(risp.hasOwnProperty('saves'))
                                else divText.html('Vuoto');
                                divSalva.append(input,divText);
                                container.append(divSalva);
                            }//for(slot = 1; slot <= 5; slot++)
                            //finestra di dialogo dei salvataggi
                            $('<div id="saves">').dialog({
                                resizable : false,
                                draggable : false,
                                position : {
                                    my : 'center center',
                                    at : 'center center',
                                    of : window
                                },
                                height : 'auto',
                                width : 'auto',
                                modal : true,
                                title : 'Salva partita',
                                open : function(){
                                    gioco = false;
                                    $(this).html(container);
                                    $('.bSlot').on('click',function(e){
                                        $('#saves').dialog('close');
                                        var inputId = $(this).attr('id');
                                        console.log("inputId"+inputId);
                                        var slot;
                                        //Verifico quale slot è stato scelto per il salvataggio
                                        //slot 1
                                        if(inputId == 'i1')slot = 1;
                                        //slot 2
                                        else if(inputId == 'i2')slot = 2;
                                        //slot 3
                                        else if(inputId == 'i3')slot = 3;
                                        //slot 4
                                        else if(inputId == 'i4')slot = 4;
                                        //slot 5
                                        else if(inputId == 'i5')slot = 5;
                                        var dati = {};
                                        dati['ajax'] = '1';
                                        dati['slot'] = slot;
                                        dati['sequenza'] = sequenza;
                                        dati['tempo'] = day+' '+h+' '+m+' '+s;
                                        dati['spostamenti'] = spostamenti;
                                        $.ajax({
                                            url : 'funzioni/save.php',
                                            method : 'post',
                                            data : dati,
                                            success : function(risposta, stato, xhr){
                                                console.log(risposta);
                                                var risp = JSON.parse(risposta);
                                                if(risp.hasOwnProperty('msg')){
                                                    gioco = false;
                                                    message('dialog','Salva partita','auto','auto',risp.msg,'close');
                                                    if(risp.hasOwnProperty('done')){
                                                        audio = new Audio('audio/notify.wav');
                                                        audio.play();
                                                    }
                                                    if(risp.hasOwnProperty('warning')){
                                                        audio = new Audio('audio/exclamation.wav');
                                                        audio.play();
                                                    }
                                                    if(risp.hasOwnProperty('error')){
                                                        audio = new Audio('audio/error.wav');
                                                        audio.play();
                                                    }
                                                    $('#dialog').on('dialogclose',function(){
                                                        gioco = true;
                                                        $('#dialog').remove();
                                                    });
                                                }//if(risp.hasOwnProperty('msg'))
                                            },
                                            error : function(xhr, stato, errore){
                                            },
                                            complete : function(xhr, stato){
                                            }
                                        });//$.ajax
                                    });//$('.bSlot').on('click',function(e)
                                },
                                close : function(){
                                    gioco = true;
                                    $(this).dialog('destroy');
                                }
                            });//$('<div id="saves">').dialog
                        }//if(risp.done == '1')
                        else{

                        }
                    }//if(risp.hasOwnProperty('done'))

                },
                error : function(xhr, stato, errore){

                },
                complete : function(xhr, stato){

                }

            }); //$.ajax
        }//if(gioco === true)
        //il gioco non può essere salvato perché terminato
        else{
            message('dialog','Salva','auto','auto','Impossibile salvare perché il gioco è stato completato','close');
            $('#dialog').on('dialogclose',function(){
                        $('#dialog').remove();
            });
            audio = new Audio('audio/exclamation.wav');
            audio.play();

        }
    });//$('#salva').on('click',function()
</script> 
<?php
                    }//else di if(isset($_SESSION['errore']))
                }//if($_SESSION['gioco'] == '1')
                else{
                    if(isset($_SESSION['errore']))echo '<h1>'.$_SESSION['errore'].'</h1>';
                }
            }//if(isset($_SESSION['gioco']))
            else{
                echo '<h1>Errore sconosciuto</h1>';
            }
?>
    </body>
</html>

<?php
}
//if(isset($_SESSION['giocatore'],$_SESSION['logged']) && $_SESSION['giocatore'] != '' && $_SESSION['logged'])
else{
    $errore = '<div><a href="index.php">Effettua l\' accesso</a> per giocare</div>';
}
if(isset($errore)) echo $errore;
?>