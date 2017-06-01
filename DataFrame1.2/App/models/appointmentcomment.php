<?php
namespace App;
use DataFrame\Models\Elegant;
class AppointmentComment extends Elegant{
    protected static $table_name = 'appointment_comments';
    public function user(){
        return $this->belongsTo("User");
    }

    public function post(){
        return $this->belongsTo("Appointment");
    }

    public function likes(){
        return $this->mergeableMany("Like", "likeable");
    }
}