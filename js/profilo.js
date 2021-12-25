
var iPassword;

function passwordDialog(id,idForm){
    var iPassword = $('<input>');
        iPassword.attr({
            type : 'password',
            id : 'iPassword',
            name : 'password',
            form : idForm,
            size : '35',
            placeholder : 'inserisci la tua password per continuare'
        });
        $('<div id='+id+'">').dialog({
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
            title : 'Modifica username',
            open : function(){
                $(this).html(iPassword);
            },
            close : function(){
                $(this).dialog('destroy');
            },
            buttons : [{
                text : 'OK',
                click : function(){
                    $('#'+idForm).submit();
                    $(this).dialog('destroy');
                }
            },
            {
                text : 'ANNULLA',
                click : function(){
                    $(this).dialog('destroy');
                }
            }]
        }); //$('<div id="eUser">').dialog
}

function chiamaAjax(dati){
    $.ajax({
        url : 'funzioni/modProfilo.php',
        method : 'post',
        data : dati,
        success : function(risposta, stato, xhr){
            //console.log(risposta);
            var risp = JSON.parse(risposta);
            message('dialog','Modifica profilo','auto','auto',risp.msg,'close');
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
    });
}

$(function(){
    $('input.field').on("mouseover",function(){
        $(this).css('background-color','yellow');
    });
    $('input.field').on("mouseout",function(){
        $(this).css('background-color','transparent');
    });
    $('input.field').on("focus",function(){
        $(this).css('background-color','orange');
    });
    $('input.field').on("blur",function(){
        $(this).css('background-color','transparent');
    });
    //il giocatore vuole modificare il suo username
    $('#bModifica').on('click',function(){
        passwordDialog('eUser','fUsername');
    });//$('#bModifica').on('click',function(e)
    //il giocatore vuole modificare la sua password
    $('#bModifica2').on('click',function(){
        passwordDialog('ePwd','fPassword');
    });
    //form cambio username
    $('#fUsername').on('submit',function(e){
        e.preventDefault();
        var dati = {};
        dati['ajax'] = '1';
        dati['edit'] = '1'; //operazione da effettuare(modifica username)
        dati['username'] = $('#username').val();
        dati['password'] = $('#iPassword').val();
        chiamaAjax(dati);
    });
    //form cambio password
    $('#fPassword').on('submit',function(e){
        e.preventDefault();
        var dati = {};
        dati['ajax'] = '1';
        dati['edit'] = '2'; //operazione da effettuare(modifica password)
        dati['nuova'] = $('#nuova').val();
        dati['confNuova'] = $('#confNuova').val();
        chiamaAjax(dati);
    });
});//$(function()