<?php
namespace App;
use DataFrame\Models\Elegant;
class Feedback extends Elegant{
    protected static $table_name = 'feedback';
    public function comments(){
        return $this->hasMany("FeedbackComment", 'post_id');
    }
    public function user(){
        return $this->belongsTo("User");
    }
}