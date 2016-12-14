<?php
//namespace App;
use DataFrame\Models\Elegant;
class Author extends Elegant{
	protected static $table_name = "author";
	protected static $view_name = "authorView";
	/*public function book(){
		return $this->hasOne("Book", "author_id");
	}*/
	
	public function books(){
		return $this->hasMany("Book");
	}
	
	public function newBook(){
		$book = new Book();
		return $book->associate($this);
	}
	
	public function photos(){
		return $this->mergeableMany("Photograph","itemable");
	}
	
	public static function fromView(){
		return self::getFromView("authorView");
	}
}