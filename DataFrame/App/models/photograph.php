<?php
namespace App;
use DataFrame\Models\Elegant;
class Photograph extends Elegant{
	protected static $table_name = "photos";
	
	public function itemable(){
		return $this->mergeable();
	}
}