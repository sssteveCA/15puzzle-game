

//finestra di dialogo con titolo, messaggio e pulsante OK
function message(id,titolo,h,w,msg,evento){
    $('<div id="'+id+'">').dialog({
        resizable : false,
        draggable : false,
        position : {
            my : 'center center',
            at : 'center center',
            of : window
        },
        height : h, //'auto',
        width :  w, //'400px',
        modal : true,
        dialogClass : 'no-close',
        title : titolo,
        open : function(){
            $(this).html(msg);
        },
        buttons : [{
            text : 'OK',
            click : function(){
                $(this).dialog(evento);
            }
        }]
    });
}

//va alla pagina di redirect (finestra = finestra di dialogo da distruggere, ref = pagina di redirect)
function redirect(finestra, ref){
    finestra.dialog('destroy');
    window.location.href = ref;
}

//finestra di dialogo con redirect (titolo = titolo finestra, msg = messaggio,ref = pagina di redirect)
function DialogRedirect(titolo,msg,ref){
    $('<div>').dialog({
        resizable : false,
        draggable : false,
        position : {
            my : 'center center',
            at : 'center center',
            of : window
        },
        height : 'auto',
        width : '500px', 
        modal : true,
        title : titolo,
        open : function(){
            $(this).html(msg);
        },
        buttons : [{
                    text : 'SÌ',
                    click : function(){
                        redirect($(this),ref);
                    }
                },
                {
                    text : 'NO',
                    click : function(){
                        $(this).dialog('destroy');
                    }

        }]
    });
}