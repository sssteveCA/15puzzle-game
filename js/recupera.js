$(function(){
    $('#fRecupera').on('submit',function(ev){
        ev.preventDefault();
        var dati = {};
        dati['email'] = $('#email').val();
        dati['ajax'] = '1';
        $.ajax({
            url : 'funzioni/recProfilo.php',
            method : 'post',
            data : dati,
            success : function(risposta, stato, xhr){
                var ris = JSON.parse(risposta);
                //alert(ris.msg);
                message('dialog','Recupera account','auto','auto',ris.msg,'close');
                if(ris.hasOwnProperty('done')){
                    audio = new Audio('audio/notify.wav');
                    audio.play();
                }
                else{
                    audio = new Audio('audio/error.wav');
                    audio.play();
                }
                $('#dialog').on('dialogclose',function(){
                    $('#dialog').remove();
                });
            },
            error : function(xhr, stato, errore){
                console.error(errore);
            },
            complete : function(xhr, stato){
                
            }
        });
    });
});