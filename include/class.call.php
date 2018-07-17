<?php
/**
 * Created by PhpStorm.
 * User: Wellington Maia
 * Date: 05/07/2018
 * Time: 08:31
 */

class CallConf
{
    public function getCall(){
        try{
            $db = new PDO('mysql:host=localhost;dbname=helpdesk;', DBUSER, DBPASS);
        }catch (Exception $e){
            echo "error = ".$e;
        }
        $results = array();
        $sql = "SELECT * FROM ostau_call WHERE status IN (0,1)";
        $db = $db->prepare($sql);
        $db->execute();
        if ($db->rowCount() > 0) {
        	$results = $db->fetchAll();
        }
        return $results;
    }

    public function updateStatus($id){
        try{
            $db = new PDO('mysql:host=localhost;dbname=helpdesk;', DBUSER, DBPASS);
        }catch (Exception $e){
            echo "error = ".$e;
        }

        $sql = "UPDATE ostau_call SET status = :status WHERE id = :id";
        $db = $db->prepare($sql);
        $db->bindValue(':status', '1');
        $db->bindValue(':id', $id);
        exit();
        $db->execute();

    }
    public function registerCall($name, $subject){

        try{
            $db = new PDO('mysql:host=localhost;dbname=helpdesk;', DBUSER, DBPASS);
        }catch (Exception $e){
            echo "error = ".$e;
        }

        $sql = "INSERT INTO ostau_call SET ip = :ip, name = :name, status = :status ,time_ini = NOW(), subject = :subject";
        $db = $db->prepare($sql);
        $db->bindValue(':ip',rand(0,999999));
        $db->bindValue(':name', json_encode($name));
        $db->bindValue(':status',0);
        $db->bindValue(':subject', json_encode($subject));

        $db->execute();
    }

    public function existId($id){
        try{
            $db = new PDO('mysql:host=localhost;dbname=helpdesk;', DBUSER, DBPASS);
        }catch (Exception $e){
            echo "error = ".$e;
        }

        $sql = "SELECT * FROM ostau_call WHERE status = :status AND id = :id";
        $db = $db->prepare($sql);
        $db->bindValue(':status',0);
        $db->bindValue(':id',$id);
        $db->execute();

        if ($db->rowCount() > 0) {
            return true;
        }else{
            return false;
        }

    }
}