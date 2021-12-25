var gioco; //true se il gioco è in esecuzione

//restituisce la stringa contenente il tempo trascorso
function timerStr(d,h,m,s){
    if(s < 10) s = '0'+s;
    if(m < 10) m = '0'+m;
    if(h < 10) h = '0'+h;
    return d+'d '+h+'h '+m+'m '+s+'s';
}

//aggiorna il record personale
function setRecord(d,h,m,s){
    //equivalente in secondi dei giorni trascorsi
    var sD = d * 86400;
    //equivalente in secondi delle ore trascorse
    var sH = h * 3600;
    //equivalente in secondi dei minuti trascorsi
    var sM = m * 60;
    //secondi 
    var sS = parseInt(s);
    var dati = {};
    dati['ajax'] = '1';
    dati['record'] = sD + sH + sM + sS;
    $.ajax({
        url : 'funzioni/setRecord.php',
        method : 'post',
        data : dati,
        success : function(risposta, stato, xhr){
            //console.log(risposta);
            var risp = JSON.parse(risposta);
            //console.log(risp);
            if(risp.hasOwnProperty('msg')){
                message('dialog','Aggiornamento record','auto','400px',risp.msg,'close');
                //distruggo la finestra dopo averla chiusa
                $('#dialog').on('dialogclose',function(){
                    $('#dialog').remove();
                });
            }
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
        },
        error : function(xhr, stato, errore){
            console.error(errore);
        },
        complete : function(xhr, stato){
        }
    }); 
}

//true se l'array è in ordine numerico crescente
function isOrdinato(array){
    var ordinato = true;
    for(var i = 0; i < array.length - 2; i++){
        var n1 = parseInt(array[i]);
        var n2 = parseInt(array[i+1]);
        if(isNaN(n1) || isNaN(n2)){
            ordinato = false;
            break;
        }
        if(n1 > n2){
            ordinato = false;
            break;
        } 
    }
    return ordinato;
}

$(function(){
    var titolo = $('#titolo');
    //var area = $('#area');
    titolo.css({
        'margin-top' : '30px',
        'margin-bottom' : '100px'
    });
    $('#esci').on('click',function(){
        gioco = false;
        $('<div id="backMenu">').dialog({
            resizable : false,
            draggable : false,
            position : {
                my : 'center center',
                at : 'center center',
                of : window
            },
            height : 'auto', 
            width :  'auto', 
            modal : true,
            title : 'Torna al menu',
            open : function(){
                $(this).html('Sei sicuro di voler tornare al menu?');
            },
            close : function(){
                gioco = true;
                $(this).dialog('destroy');
            },
            buttons : [{
                text : 'SI',
                click : function(){
                    window.location.href = 'funzioni/backMenu.php';
                }
            },
            {
                text : 'NO',
                click : function(){
                    $(this).dialog('destroy');
                }
            }]
        }); //$('<div id="logout">').dialog
    });
});