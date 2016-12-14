<?php
namespace DataFrame\Database;
interface Methods{
	public static function find_by_id($id);
	public static function find_all();
	public static function find_by_sql($sql);
	//public static function count_all();
	public function save();
	public function update();
	public function create();
	public static function delete();
}