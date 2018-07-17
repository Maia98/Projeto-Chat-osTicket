 <!-- <script type="text/javascript">
    var LHCChatOptions = {};
    LHCChatOptions.opt = {widget_height:340,widget_width:300,popup_height:520,popup_width:500};
    (function() {
        var _l = '';var _m = document.getElementsByTagName('meta');var _cl = '';for (i=0; i < _m.length; i++) {if ( _m[i].getAttribute('http-equiv') == 'content-language' ) {_cl = _m[i].getAttribute('content');}}if (document.documentElement.lang != '') _l = document.documentElement.lang;if (_cl != '' && _cl != _l) _l = _cl;if (_l == undefined || _l == '') {_l = 'por/';} else {_l = _l[0].toLowerCase() + _l[1].toLowerCase(); if ('eng' == _l) {_l = ''} else {_l = _l + '/';}}
        var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
        var referrer = (document.referrer) ? encodeURIComponent(document.referrer.substr(document.referrer.indexOf('://')+1)) : '';
        var location  = (document.location) ? encodeURIComponent(window.location.href.substring(window.location.protocol.length)) : '';
        po.src = '//localhost:8080/livehelperchat/lhc_web/index.php/'+_l+'chat/getstatus/(click)/internal/(position)/bottom_right/(ma)/br/(check_operator_messages)/true/(top)/350/(units)/pixels/(leaveamessage)/true/(disable_pro_active)/true?r='+referrer+'&l='+location;
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
    })();
</script> -->

 <link type="text/css" rel="stylesheet" href="/osTicketPrenerTest/css/chatbox.css">
 <script type="text/javascript" src="/osTicketPrenerTest/js/chatbox.js"></script>


 <div class="chatbox chatbox--tray chatbox--empty">
     <div class="chatbox__title">
         <h5><a href="#">Customer Service</a></h5>
         <button class="chatbox__title__tray">
             <span></span>
         </button>
         <button class="chatbox__title__close">
            <span>
                <svg viewBox="0 0 12 12" width="12px" height="12px">
                    <line stroke="#FFFFFF" x1="11.75" y1="0.25" x2="0.25" y2="11.75"></line>
                    <line stroke="#FFFFFF" x1="11.75" y1="11.75" x2="0.25" y2="0.25"></line>
                </svg>
            </span>
         </button>
     </div>
     <div class="chatbox__body">
         <div class="chatbox__body__message chatbox__body__message--left">
             <img src="https://s3.amazonaws.com/uifaces/faces/twitter/brad_frost/128.jpg" alt="Picture">
             <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
         </div>
         <div class="chatbox__body__message chatbox__body__message--right">
             <img src="https://s3.amazonaws.com/uifaces/faces/twitter/arashmil/128.jpg" alt="Picture">
             <p>Nulla vel turpis vulputate, tincidunt lectus sed, porta arcu.</p>
         </div>
         <div class="chatbox__body__message chatbox__body__message--left">
             <img src="https://s3.amazonaws.com/uifaces/faces/twitter/brad_frost/128.jpg" alt="Picture">
             <p>Curabitur consequat nisl suscipit odio porta, ornare blandit ante maximus.</p>
         </div>
         <div class="chatbox__body__message chatbox__body__message--right">
             <img src="https://s3.amazonaws.com/uifaces/faces/twitter/arashmil/128.jpg" alt="Picture">
             <p>Cras dui massa, placerat vel sapien sed, fringilla molestie justo.</p>
         </div>
         <div class="chatbox__body__message chatbox__body__message--right">
             <img src="https://s3.amazonaws.com/uifaces/faces/twitter/arashmil/128.jpg" alt="Picture">
             <p>Praesent a gravida urna. Mauris eleifend, tellus ac fringilla imperdiet, odio dolor sodales libero, vel mattis elit mauris id erat. Phasellus leo nisi, convallis in euismod at, consectetur commodo urna.</p>
         </div>
     </div>
     <form method="post" class="chatbox__credentials" onsubmit="inserir()" id="cadUsuario">
         <div class="form-group">
             <label for="inputName">Name:</label>
             <input type="text" class="form-control" id="inputName" required>
         </div>
         <div class="form-group">
             <label for="inputEmail">Assunto:</label>
             <input type="text" class="form-control" id="inputEmail" required>
         </div>
         <button type="submit"  class="btn btn-success btn-block">Enter Chat</button>
     </form>
     <textarea class="chatbox__message" placeholder="Write something interesting"></textarea>
 </div>