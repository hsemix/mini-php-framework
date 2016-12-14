# mini-php-framework
# Author is Semix Hamidouh
The DataFrame php Framework is a mini php framework to help in daily php problems while development is taking place.

Basic features found are:-
- Basic Routing
- MVC archtecture
- Use of namespaces
- ORM support similar to that of the famous Laravel's Eloquent
- A Basic php Template engine (purely php)

Quick Start up is below:
```php
<?php
include_once("mahad/starter.php");

use DataFrame\Starter as start;
use DataFrame\Route;
use DataFrame\Views\View;
start::init(function($args){
	$args->setModelDir("models");  // Could also be MyApp/models if you app is in a subfolder
	$args->setDbConnect(
		array('dbtype' => 'mysql', 
			"dbuser"=>"root", 
			"dbserver"=>"localhost", 
			"dbname"=>"your_database", 
			"dbpass"=>""
		)
	);
	$args->setTemplate(true,
		array(
			"tempDir" => "views"  // Could also be MyApp/view
		)
	);
	$args->setControllerDir("controllers"); // Could also be MyApp/controller
});

Route::get("/home",function(){
	return new View("test");
});

$users = ['semix', 'hamidouh', 'Al fayeed'];

Route::get("/users", function() use ($users){
  print_r($users);
});


// Controller Based

Route::get("/users", "YourNameSpace\MembersController@getUsers");

// Get Info From DB
Route::get("/products", function(DataFrame\Response $res){
  $products = YourNameSpace\Product::findAll()->get;
  
  print_r($products);
  
});

```


