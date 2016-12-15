# mini-php-framework
# Author is Semix Hamidouh
The DataFrame php Framework is a mini php framework to help in daily php problems while development is taking place.

Basic features found are:-
- Basic Routing
- MVC architecture
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
- creation of a Controller

```php
<?php
namespace MyApp;
use DataFrame\Controllers\Controller;
use DataFrame\Views\View;
class HomeController extends Controller{
	public function index(){
		$data = [
			'name' => 'Hamidouh'
		];
		return new View("home", $data);
	}
}
```




- creation of a Model
```php
<?php 
namespace MyApp;
use DataFrame\Models\Elegant;
class User extends Elegant{
	protected static $table_name = "users_table"; // custome tablename, the framework assumes the plural version of the class name
	
}

```



- how to use the model from with in the controller

```php
<?php
namespace MyApp;
use DataFrame\Controllers\Controller;
use DataFrame\Views\View;
class HomeController extends Controller{
	public function getUsers(){
		$users = User::all();
		$usersNumber = User::count();
		$usersWhoseAgeIsMoreThanTen = User::where("age", ">", 10)->get; // it is optional to use get() or just get
		// count these uses
		$usersCount = User::where("age", ">", 10)->count;
		$data = [
			'allUsers' => $users,
			'userCount' => $usersNumber,
			'usersAges' => $usersWhoseAgeIsMoreThanTen,
			'usersAgesCount' => $usersCount
		];
		return new View("home", $data);
	}
}
```

- how to handle basic relations i.e (one-to-one, one-to-many, many-to-many, and polymorphic) 
- This simple framework will help you as well with this hard task

```php

<?php
namespance App;
Use DataFrame\Models\Elegant;
class User extends Elegant{
	protected static $table_name = "users";
	public function userType(){
		return $this->hasOne("Type"); // you can change the relational foreign key but assumes it is user_id in types;
	}
	
	public function photos(){
		return $this->mergeableMany("Photograph", "imageable"); // polymophic
	}
	
	public function products(){
		return $this->hasMany("Product"); // one to many
	}
	public function groups(){
		return $this->belongsToMany("Group"); // many to many ,assumes pivot table to be group_user
	}
}

class Product extends Elegant{
	public function user(){
		return $this->belongsTo("User"); // reverse one to one
	}
	public function photos(){
		return $this->mergeableMany("Photograph", "imageable"); // polymophic
	}
}

class Group extends Elegant{
	public function users(){
		return $this->belongsToMany("User"); // many to many ,assumes pivot table to be group_user
	}
}

class Photograph extends Elegant{
	public function imageable(){
		return $this->mergeable();
	}
}

```
- How to use these models
```php
<?php

$user = User::find(1); // finds user whose primary key is 1
$userPhotos = $user->photos; // returns an array of photos where imageable_id = 1 & imageable_type = 'user'

// you can also trim result sets function using the query builder that comes with the Elegant Mode class

$userPhotos = $user->photos()->where("created_at", "2000-12-20")->get;

$userProducts = $user->products;

```
