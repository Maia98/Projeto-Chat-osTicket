(function($) {
    $(document).ready(function() {
        var $chatbox = $('.chatbox'),
            $chatboxTitle = $('.chatbox__title'),
            $chatboxTitleClose = $('.chatbox__title__close'),
            $chatboxCredentials = $('.chatbox__credentials');
        $chatboxTitle.on('click', function() {
            $chatbox.toggleClass('chatbox--tray');
        });
        $chatboxTitleClose.on('click', function(e) {
            e.stopPropagation();
            $chatbox.addClass('chatbox--closed');
        });
        $chatbox.on('transitionend', function() {
            if ($chatbox.hasClass('chatbox--closed')) $chatbox.remove();
        });
        $chatboxCredentials.on('submit', function(e) {
            e.preventDefault();
            $chatbox.removeClass('chatbox--empty');
        });
    });
})(jQuery);

function inserir(){
    var name = $("#inputName").val();
    var subject = $("#inputEmail").val();

        $.post('../ajax.php/call/registercall', {name: name, subject: subject }, function () {

    });

    }

function iniciarSuporte() {
    setTimeout(getCall,2000);
}

function getCall() {
    $.ajax({
        url: '../ajax.php/call/call',
        dataType:'json',
        success:function(json) {
        setTimeout(getCall,2000);
            resetCalls();
            if(json.calls.length > 0){
                for (var x in json.calls) {
                    if (json.calls[x].status == 1){
                    $('#areacalls').append("<tr class='call' data-id='"+json.calls[x].id+"'><td>"+
                    json.calls[x].time_ini+"</td><td>"+
                    json.calls[x].name+
                    "</td><td><button class='btn btn-sm btn-danger' disabled>Em Atendimento</button></td></tr>");
                    }else{
                        $('#areacalls').append("<tr class='call' data-id='"+json.calls[x].id+"'><td>"+
                            json.calls[x].time_ini+"</td><td>"+
                            json.calls[x].name+
                            "</td><td><button class='btn btn-sm btn-success' onclick='openCall(this)'>Abrir Chamado</button></td></tr>");

                    }
               }
            }

        console.log('getcallb...');
        },
        error:function() {
        setTimeout(getCall,2000);
        console.log('getcalle...');
        }
    });
}

function resetCalls() {
    $('.call').remove();

}
function openCall(obj) {
    var id = $(obj).closest('.call').attr('data-id');

    //fazer o windown receber o id da conversa

    $.post('../ajax.php/call/uc/', {id: id}, function () {
    });

    openCall2(id);

}

function openCall2(id){
    alert(id);
    $.post('../ajax.php/call/screechat', {id: id}, function () {

    });
    window.open("/osTicketPrenerTest/scp/chatsuportt.php", 'chat', "width=400,height=450");
}