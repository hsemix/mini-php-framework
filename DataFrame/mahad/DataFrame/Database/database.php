<?php
namespace DataFrame\Database;
abstract class Database {
	abstract public function open_connection();
	abstract public function query($sql);
	abstract public function lastInsertId();
}