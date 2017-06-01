<?php
namespace DataFrame\Database\Support\Helpers;
abstract class Database {
	abstract public function open_connection();
	abstract public function query($sql);
	abstract public function lastInsertId();
}