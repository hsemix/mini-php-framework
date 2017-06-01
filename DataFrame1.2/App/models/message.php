<?php
namespace App;
use DataFrame\Models\Elegant;
class Message extends Elegant{
    public function user(){
        return $this->belongsTo("User");
    }
    public function chat(){
        return $this->belongsTo("Chat");
    }
    public function sender(){
    	return $this->belongsTo("User");
    }
    public function reciever(){
    	return $this->belongsTo("User");
    }

    public static function getDocs(){
        $sql = "SELECT m.* FROM messages m, users u WHERE m.reciever_id=u.id AND u.type_id=2 GROUP BY chat_id ORDER BY created_at DESC";

        return new \DataFrame\Database\Elegant\Collection(self::find_by_sql($sql));
    }
    public static function checkForNew(){
         $sql = "SELECT m.* FROM messages m, users u WHERE m.reciever_id=u.id AND u.type_id=2 GROUP BY chat_id ORDER BY created_at DESC";

        return new \DataFrame\Database\Elegant\Collection(self::find_by_sql($sql));
    }
}