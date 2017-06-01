<?php
use DataFrame\Starter as Start;
include_once("mahad/autoloader.php");

Start::init(function($args){
	$args->setDbConnect([
		'dbtype' => 'mysql', 
		"dbuser"=>"root", 
		"dbhost"=>"localhost", 
		"dbname"=>"musawo", 
		"dbpass"=>"",
		"dbprefix" => ''
	]);
	$args->setGlobals([
		'session' => [
			'session_name' => 'user'
		],
		'token' => [
			'token_name' => 'token'
		]
	]);
});