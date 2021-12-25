
var tHeight = 400; //altezza tabella
var tWidth = 700; //larghezza tabella
var fontDim; //dimensione testo celle tabella

$(function(){
    var tabella = $('#tabella');
    tabella.css({
        height : tHeight+'px',
        width : tWidth+'px'
    });
    fontDim = 20;
    $('td').css({
        'font-size' : fontDim+'px',
        'text-transform' : 'uppercase'
    });
    $('tr').on('mouseover',function(){
        $(this).css('background-color','yellow');
    });
    $('tr').on('mouseout',function(){
        $(this).css('background-color','transparent');
    });
    $('input').on('focus',function(){
        $(this).css('background-color','orange');
    });
    $('input').on('blur',function(){
        $(this).css('background-color','transparent');
    });
    $('#fRegistra').on('submit',function(ev){
        ev.preventDefault();
        var dati = {};
        dati['ajax'] = '1';
        dati['email'] = $('#iEmail').val();
        dati['username'] = $('#iUser').val();
        dati['password'] = $('#iPass').val();
        dati['confPassword'] = $('#iConfPass').val();
        $.ajax({
            url : 'funzioni/creaAccount.php',
            method : 'post',
            data : dati,
            success : function(risposta, stato, xhr){
                //console.log(risposta);
                var ris = JSON.parse(risposta);
                message('dialog','Registrazione','auto','auto',ris.msg,'close');
                if(ris.hasOwnProperty('done')){
                    audio = new Audio('audio/notify.wav');
                    audio.play();
                }
                if(ris.hasOwnProperty('warning')){
                    audio = new Audio('audio/exclamation.wav');
                    audio.play();
                }
                if(ris.hasOwnProperty('error')){
                    audio = new Audio('audio/error.wav');
                    audio.play();
                }
                $('#dialog').on('dialogclose',function(){
                    $('#dialog').remove();
                });
                //cancello i campi che contengono la password per sicurezza
                $('#iPass').val('');
                $('#iConfPass').val('');
            },
            error : function(xhr, stato, errore){
                console.error(errore);
            },
            complete : function(xhr, stato){
                
            }
        });

    });//$('#fRegistra').on('submit',function(ev)
});