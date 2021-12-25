
var voceH = 100; //altezza voce del menu
var voceW = 450; //larghezza voce del menu
//altezza e larghezza dello schermo
var wHeight = $(window).height();
var wWidth = $(window).width();
var fontDim = (voceH/4); //dimensione testo voci del menu
var m0,m1,m2,m3,m4;

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
            fCarica.submit();
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