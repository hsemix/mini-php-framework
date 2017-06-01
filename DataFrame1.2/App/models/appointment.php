<?php
namespace App;
use DataFrame\Models\Elegant;
class Appointment extends Elegant{
    public function user(){
        return $this->belongsTo("User");
    }
    public function comments(){
        return $this->hasMany('AppointmentComment', 'post_id');
    }
}