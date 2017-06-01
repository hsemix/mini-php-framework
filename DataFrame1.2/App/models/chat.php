<?php
namespace App;
use DataFrame\Models\Elegant;
class Chat extends Elegant{
    public function messages(){
        return $this->hasMany('Message');
    }
}