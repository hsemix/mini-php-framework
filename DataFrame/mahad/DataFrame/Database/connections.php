<?php
namespace DataFrame\Database;
trait Connections{
	private $connection;
	private $last_query;
	private $folder = "c:/Database";
	private $magic_quotes_active;
	public 	$database_type;
	private $mysql_real_escape_string_exists;
	
}