<?php
namespace DataFrame;
use DateTime as NativeDateTime;
use DateTimeZone as NativeDateTimeZone;
class DateTime {
    public $datetime;
    public function __construct($datetime = null, $timezone = null){
        $zone = 'UTC';
        if($timezone){
            $zone = $timezone;
        }
        if($datetime){
            $this->datetime = new NativeDateTime($datetime, new NativeDateTimeZone($zone));
        }
    }

    public function __toString(){
        return $this->datetime->format("Y-m-d H:i:s");
    }
}