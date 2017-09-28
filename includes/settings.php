<?php
class settings {
    
    static function set($name, $value){
        $settings_id = db::get_one("SELECT id FROM ".PREF."settings WHERE name = ~~ LIMIT 1", array($name));
        if($settings_id){
            $res = db::query("UPDATE ".PREF."settings SET value = ~~ WHERE id = '".intval($settings_id)."'", array($value));
        }else{
            $res = db::query("INSERT INTO ".PREF."settings SET name = ~~, value = ~~", array($name, $value));
        }
        return $res;
    }
    
    static function get($name){
        $val = db::get_one("SELECT value FROM ".PREF."settings WHERE name = ~~ LIMIT 1", array($name));
        return $val;
    }
    
}
