var audio;

$(function(){
    var dati = {};
    $('#fRecupera').on('submit',function(e){
        e.preventDefault();
        dati['ajax'] = '1';
        dati['chiave'] = $('#chiave').val();
        dati['nuova'] = $('#nuova').val();
        dati['confNuova'] = $('#confNuova').val();
        $.ajax({
            url : 'funzioni/recovery.php',
            method : 'post',
            data : dati,
            success : function(risposta, stato, xhr){
                //console.log(risposta);
                var risp = JSON.parse(risposta);
                message('dialog','Recupero password','auto','400px',risp.msg,'close');
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
                //distruggo la finestra dopo averla chiusa
                $('#dialog').on('dialogclose',function(){
                    $('#dialog').remove();
                });
                //cancello i campi che contengono la password per sicurezza
                $('#nuova').val('');
                $('#confNuova').val('');
            },
            error : function(xhr, stato, errore){
                console.error(errore);
            },
            complete : function(xhr, stato){
            }
        });//$.ajax
    });
});