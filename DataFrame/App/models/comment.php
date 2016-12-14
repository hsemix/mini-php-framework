<?php
use DataFrame\Models\Elegant;
class Comment extends Elegant{
	protected static $table_name = "comment";
	
	
	public function photos(){
		return $this->mergeableMany("Photograph","itemable");
	}
}