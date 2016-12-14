<?php
namespace App;
use DataFrame\Models\Elegant;
class Group extends Elegant{
	protected static $table_name = "groups";
	//public $created_at = true;
	//public $updated_at = true;
	
	
	public function photos(){
		return $this->mergeableMany("Photograph","itemable");
	}
	
	public function member(){
		$member = new Group_user();
		return $member->associate($this);
	}
	
	public function owner(){
		return $this->belongsTo("User");
	}
	
	public function users(){
		return $this->belongsToMany("User");
	}
}