<?php
namespace App;
use DataFrame\Models\Elegant;
class Book extends Elegant{
	protected static $table_name = "book";
	//protected static $pk = "bookId";
	public function photos(){
		return $this->mergeableMany("Photograph","itemable");
	}
}