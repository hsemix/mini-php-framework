<?php
include_once("mahad/starter.php");
use DataFrame\Starter as start;
start::init(function($args){
	$args->setModelDir("App/models");
	$args->setDbConnect(
		array('dbtype' => 'mysql', 
			"dbuser"=>"root", 
			"dbserver"=>"localhost", 
			"dbname"=>"tabamiruka", 
			"dbpass"=>""
		)
	);
	$args->setTemplate(true,
		array(
			"tempDir" => "App/views"
		)
	);
	$args->setControllerDir("App/controllers");
});


