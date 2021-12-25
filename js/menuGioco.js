
var voceH = 100; //altezza voce del menu
var voceW = 450; //larghezza voce del menu
//altezza e larghezza dello schermo
var wHeight = $(window).height();
var wWidth = $(window).width();
var fontDim = (voceH/4); //dimensione testo voci del menu
var m0,m1,m2,m3,m4;
var html = '';
var audio;

//evidenzia una voce del menu
function evidenzia(voce){
    if(voce == 0){
        m0.css('background-color','yellow');
        m1.css('background-color','transparent');    
        m2.css('background-color','transparent');    
        m3.css('background-color','transparent');    
        m4.css('background-color','transparent');    
    }
    else if(voce == 1){
        m0.css('background-color','transparent'); 
        m1.css('background-color','yellow');
        m2.css('background-color','transparent');
        m3.css('background-color','transparent');
        m4.css('background-color','transparent');  
    }
    else if(voce == 2){
        m0.css('background-color','transparent');    
        m1.css('background-color','transparent'); 
        m2.css('background-color','yellow');  
        m3.css('background-color','transparent');
        m4.css('background-color','transparent');  
    }
    else if(voce == 3){
        m0.css('background-color','transparent');    
        m1.css('background-color','transparent'); 
        m2.css('background-color','transparent'); 
        m3.css('background-color','yellow');
        m4.css('background-color','transparent');  
    }
    else if(voce == 4){
        m0.css('background-color','transparent');    
        m1.css('background-color','transparent'); 
        m2.css('background-color','transparent'); 
        m3.css('background-color','transparent'); 
        m4.css('background-color','yellow');
    }
}

$(function(){
    var voce = 0; //voce del menu evidenziata
    var titolo = $('#titolo');
    var menu = $('#menu');
    //voci del menu
    m0 = $('#m0');
    m1 = $('#m1');
    m2 = $('#m2');
    m3 = $('#m3');
    m4 = $('#m4');
    //form nuova partita e carica partita
    var fNuova = $('#fNuova');
    var fCarica = $('#fCarica');
    titolo.css({
        'margin-top' : '30px',
        'margin-bottom' : '100px'
    });
    $('.voci').css({
        height : voceH+'px',
        widht: voceW+'px',
        'font-size' : fontDim+'px'
    });
    m0.css('background-color','yellow');
    $('#m0').on('mouseover',function(){
        voce = 0;
        evidenzia(voce);
    });
    $('#m1').on('mouseover',function(){
        voce = 1;
        evidenzia(voce);
    });
    $('#m2').on('mouseover',function(){
        voce = 2;
        evidenzia(voce);
    });
    $('#m3').on('mouseover',function(){
        voce = 3;
        evidenzia(voce);
    });
    $('#m4').on('mouseover',function(){
        voce = 4;
        evidenzia(voce);
    });
    $('.voci').on('click',function(){
        //Nuova partita
        if(voce == 0){
            fNuova.submit();
        }
        //Carica partita
        else if(voce == 1){
            var ajax = {};
            ajax['ajax'] = '1';
            ajax['carica'] = '1';
            $.ajax({
                url : 'funzioni/getSaves.php',
                method : 'post',
                data : ajax,
                success : function(risposta, stato, xhr){
                     //console.log(risposta);
                     var risp = JSON.parse(risposta);
                     //console.log(risp);
                     if(risp.hasOwnProperty('msg')){
                         message('dialog','Carica partita','auto','auto',risp.msg,'close');
                         $('#dialog').on('dialogclose',function(){
                            $('#dialog').remove();
                        });
                     }
                     if(risp.hasOwnProperty('empty')){
                         //salvataggi trovati
                         if(risp.empty == '0'){
                            var slot;
                            var ids;
                            var html = '';
                            var container = $('<div>');
                            for(i in risp['saves']){
                                /*aggiungo un div contenente il pulsante per caricare la partita 
                                e le informazioni su quella partita */
                                slot = risp['saves'][i]['slot'];
                                ids = risp['saves'][i]['id'];
                                //div che contiene il pulsante CARICA e le informazioni sula partita salvata
                                var divCarica = $('<div>');
                                divCarica.attr({
                                    id : 'slot'+slot,
                                    class : 'divSalva'
                                });
                                divCarica.css({
                                    margin : '5px',
                                    display : 'flex',
                                    'justify-content' : 'center',
                                    'align-items' : 'center'
                                });
                                //al submit di questo form la partita viene caricata
                                var formC = $('<form>');
                                formC.attr({
                                    id : 'f'+slot,
                                    class : 'fCarica',
                                    method : 'post',
                                    action : 'funzioni/load.php'
                                });
                                var inputB = $('<input>');
                                inputB.attr({
                                    type : 'submit',
                                    id : 'i'+slot,
                                    class : 'bSlot',
                                    value : 'CARICA'
                                });
                                var inputH = $('<input>');
                                inputH.attr({
                                    type : 'hidden',
                                    name : 'carica',
                                    value : '1'
                                });
                                //id del salvataggio
                                var idS = $('<input>');
                                idS.attr({
                                    type : 'hidden',
                                    name : 'ids',
                                    value : ids
                                });
                                inputB.css('margin-right', '10px');
                                formC.append(inputB,inputH,idS);
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
                                //dati del salvataggio da mostrare
                                var dataBis = risp['saves'][slot]['data'];
                                var sequenzaBis = risp['saves'][slot]['sequenza'];
                                var tempoBis =  risp['saves'][slot]['tempo'];
                                var spostamentiBis = risp['saves'][slot]['spostamenti'];
                                html = `DATA: `+dataBis+`<br>
ORDINE: `+sequenzaBis+`<br>
TEMPO: `+tempoBis+`<br>
SPOSTAMENTI: `+spostamentiBis+``;
                                divText.html(html);
                                divCarica.append(formC,divText);
                                container.append(divCarica);
                            }//for(i in risp['saves'])
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
                                title : 'Carica partita',
                                open : function(){
                                    $(this).html(container);
                                    //il giocatore ha selezionato una partita da caricare
                                    /*$('.fCarica').on('submit',function(e){
                                        e.preventDefault();
                                        console.log($(this).attr('id'));


                                        //console.log("InputId = "+inputId);
                                    }); //$('.bSlot').on('click',function(e)  */  
                                },
                                close : function(){
                                    $(this).dialog('destroy');
                                }
                            });//$('<div id="saves">').dialog
                         }//if(risp.empty == '0')

                     }//if(risp.hasOwnProperty('empty'))
                     if(risp.hasOwnProperty('ding')){
                         audio = new Audio('audio/ding.wav');
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

                },
                complete : function(xhr, stato){

                }
            });//$.ajax
        }
        //Profilo
        else if(voce == 2){
            window.location.href = 'profilo.php';
        }
        //record
        else if(voce == 3){
            window.location.href="record.php";
        }
        //Esci
        else if(voce == 4){
            $('<div id="logout">').dialog({
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
                title : 'Esci dal gioco',
                open : function(){
                    $(this).html('Sei sicuro di voler uscire dal gioco?');
                },
                close : function(){
                    $(this).dialog('destroy');
                },
                buttons : [{
                    text : 'SI',
                    click : function(){
                        window.location.href = 'funzioni/logout.php';
                    }
                },
                {
                    text : 'NO',
                    click : function(){
                        $(this).dialog('destroy');
                    }
                }]
            }); //$('<div id="logout">').dialog
        }//else if(voce == 4)
    });
    $('body').on('keydown',function(e){
        //tasto giu
        if(e.keyCode == 40){
            if(voce < 4){
                voce++;
            }
        }
        //tasto su
        else if(e.keyCode == 38){
            if(voce > 0){
                voce--;    
            }
        }
        evidenzia(voce);
        
    });//$('body').on('keydown'
    $('body').on('keypress',function(e){
        //console.log(e);
        //tasto INVIO
        if(e.keyCode == 13){
            $('.voci').click();
        }//if(e.keyCode == 13)
    });//$('body').on('keypress'

});