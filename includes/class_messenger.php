<?php
class class_messenger{

    public $load_time;
    
    function __construct() {
        $this->load_time = time();
    }
    
    function send_message($sender, $recepient, $content){
        db::query("INSERT INTO ".PREF."messages SET 
                      sender_id = '".intval($sender)."', 
                      recepient_id = '".intval($recepient)."',
                      date = '".$this->load_time."',
                      content = ~~
                      ", array(htmlspecialchars($content)));
        return db::get_last_id();
    }
    
    function get_conversation($user, $with){
        $aMessages = db::get("SELECT * FROM ".PREF."messages WHERE
                            (sender_id = '".intval($user)."' AND recepient_id = '".intval($with)."') OR
                            (sender_id = '".intval($with)."' AND recepient_id = '".intval($user)."')
                            ORDER BY date DESC");
        return $aMessages;
    }
    
    function get_total_unread_incoming($user){
        $num = db::get_one("SELECT COUNT(*) FROM ".PREF."messages WHERE recepient_id = '".intval($user)."' AND status = '0'");
        return $num;
    }
    
    function get_total_unread_incoming_by_users($user){
        $aNums = db::get("SELECT sender_id AS id, COUNT(*) AS num FROM ".PREF."messages WHERE recepient_id = '".intval($user)."' AND status = '0' GROUP BY sender_id");
        return $aNums;
    }
    
    function get_total_unread_user_incoming($user, $sender){
        $num = db::get_one("SELECT COUNT(*) FROM ".PREF."messages WHERE recepient_id = '".intval($user)."' AND status = '0' AND sender_id= '".intval($sender)."'");
        return $num;
    }
    
    function set_read_status($user, $with){
        db::query("UPDATE ".PREF."messages SET status = 1 WHERE status = 0 AND sender_id = '".intval($with)."' AND recepient_id = '".intval($user)."'");
        return true;
    }
    
    function delete_message($id){
        db::query("DELETE FROM ".PREF."messages WHERE id = '".intval($id)."'");
        return true;
    }
    
    
}