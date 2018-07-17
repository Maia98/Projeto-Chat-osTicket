<?php

require_once (INCLUDE_DIR.'class.call.php');

Class Call extends AjaxController{

 function getCall(){

    $dados = array(
        'calls' => ''
    );

    $call = new CallConf();
    $dados['calls'] = $call->getCall();

    	echo json_encode($dados);
    
    
}

function updateCall(){
        $id = addslashes($_POST['id']);

        $call = new CallConf();
     if ($call->existId($id)){
         $call->updateStatus($id);
     }else{
         echo json_encode('id não existe');
     }
}

function screenChat(){
     $id = addslashes($_POST['id']);
    //echo json_encode("chegou aqui...".$id);
    // echo "<script>href.location='/osTicketPrenerTest/scp/chatsuportt.php'</script>";
    header('Location: /osTicketPrenerTest/scp/chatsuportt.php');
 }

 function registerCall(){

     $name = addslashes($_POST['name']);
     $subject = addslashes($_POST['subject']);

     $call = new CallConf();
     $call->registerCall($name,$subject);

     echo $this->json_encode($name."/".$subject);
 }
}
?>