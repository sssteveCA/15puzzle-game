var voceH = 100; //altezza voce del menu
var voceW = 450; //larghezza voce del menu
//altezza e larghezza dello schermo
var wHeight = $(window).height();
var wWidth = $(window).width();
var fontDim = (voceH/4); //dimensione testo voci del menu
var html;
var m0,m1,m2;

//evidenzia una voce del menu
function evidenzia(voce){
    if(voce == 0){
        m0.css('background-color','yellow');
        m1.css('background-color','transparent');    
        m2.css('background-color','transparent');    
    }
    else if(voce == 1){
        m0.css('background-color','transparent'); 
        m1.css('background-color','yellow');
        m2.css('background-color','transparent'); 
    }
    else if(voce == 2){
        m0.css('background-color','transparent');    
        m1.css('background-color','transparent'); 
        m2.css('background-color','yellow');  
    }
}

$(function(){
    var voce = 0; //voce del menu evidenziata
    var titolo = $('#titolo');
    var menu = $('#menu');
    m0 = $('#m0');
    m1 = $('#m1');
    m2 = $('#m2');
    titolo.css({
        'margin-top' : '30px',
        'margin-bottom' : '100px'
    });
    /*menu.css({
        height : menuH+'px',
        width : menuW+'px'
    });*/
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
    $('.voci').on('click',function(){
        console.log(voce);
        //se l'elemento non esiste
        if(voce == 0){
            if(!$('#fAccedi').length){
                var form = $('<form>');
                form.attr({
                    id : 'fAccedi',
                    method : 'post',
                    action : 'funzioni/login.php'
                });
                var div1 = $('<div>');
                var iUser = $('<input>');
                iUser.attr({
                    type : 'text',
                    name : 'username',
                    placeholder : 'Il tuo nome utente',
                    size : '35'
                });
                iUser.css('margin-bottom','20px');
                div1.append(iUser);
                var div2 = $('<div>');
                var iPassword = $('<input>');
                iPassword.attr({
                    type : 'password',
                    name : 'password',
                    placeholder : 'La tua password',
                    size : '35'
                });
                div2.append(iPassword);
                form.append(div1);
                form.append(div2);
                $('<div id="login">').dialog({
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
                    title : 'Accedi',
                    open : function(){
                        $(this).html(form);
                    },
                    close : function(){
                        $(this).dialog('destroy');
                    },
                    buttons : [{
                        text : 'OK',
                        click : function(){
                            form.submit();
                        }
                    },
                    {
                        text : 'ANNULLA',
                        click : function(){
                            $(this).dialog('destroy');
                        }
                    }]
                }); //$('<div id="login">').dialog
            }//if($('#fAccedi').length)
        }//if(voce == 0)
        else if(voce == 1){
            window.location.href = 'registrati.php';
        }
        else if(voce == 2){
            window.location.href = 'recupera.php';
        }
    });
    $('body').on('keydown',function(e){
        //tasto giu
        if(e.keyCode == 40){
            if(voce < 2){
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
    });//$('body').on('keydown')
    $('body').on('keypress',function(e){
        //console.log(e);
        //tasto INVIO
        if(e.keyCode == 13){
            $('.voci').click();
        }
    });
});