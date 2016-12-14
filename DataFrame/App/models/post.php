<?php
namespace App;
use DataFrame\Models\Elegant;
class Post extends Elegant{
	protected static $table_name = "post";
	
	
	public function photos(){
		return $this->mergeableMany("Photograph","itemable");
	}
	
	public function user(){
		return $this->belongsTo("App\User");
	}
	
	public function comments(){
		return $this->hasMany("Comment");
	}
}