<?php
namespace App;
use DataFrame\Models\Elegant;
class User extends Elegant{
	protected static $table_name = "user";
	protected static $pk = "userId";
	public $updated_at = true;
	
	public function photos(){
		return $this->mergeableMany("Photograph","itemable");
	}
	
	public function groups(){
		return $this->hasMany("Group");
	}
	
	public function userGroups(){
		return $this->belongsToMany("App\Group");
	}
	
	public function posts(){
		return $this->hasMany("Post");
	}
	
	public function comment(){
		$comment = new Comment();
		return $comment->associate($this);
	}
	public function type(){
		return $this->belongsTo("Apps\Type");
	}
	public function company(){
		return $this->hasOne("App\Company");
	}
}