<?php
namespace DataFrame\Database\Support\Interfaces;
interface Methods{
	public static function find_by_id($id);
	public static function find_all();
	public static function find_by_sql($sql);
	//public static function count_all();
	public function save();
	public static function update(array $args);
	//public function update();
	public static function create(array $args);
	public static function delete();
}