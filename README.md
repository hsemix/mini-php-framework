# mini-php-framework
# Author is Semix Hamidouh
The DataFrame php Framework is a mini php framework to help in daily php problems while development is taking place.

Basic features found are:-
- Basic Routing with some advanced routing features e.g. post, get, put, delete and resource for api
- MVC archtecture
- Use of namespaces
- ORM support similar to that of the famous Laravel's Eloquent. This has been rewritten from scratch to support more features
(i.e it nolonger returns an array for models but returns the collection object that supports things like filtering, counting results, chunking results and many more.)
- A Basic php Template engine (I called it hax templating engine)
- Blade like templating inheritance
- The validate class has been added,
- The Token class has been added to solve csrf problems
- The Session object has been improved and 
- Cookie class has been added to support for remember me features on forms
- The request class has been improved to support file uploads and
- The controllers now have Constructor dependency injections and method dependency injections
- The Response Class has also been improved to support json
- The Models can now be converted to arrays, json and can be converted to plain strings

Quick Start up is below:
```php
<?php
use DataFrame\Starter as Start;
include_once("mahad/autoloader.php");

Start::init(function($args){
	$args->setDbConnect([
		'dbtype' => 'mysql', 
		"dbuser"=>"root", 
		"dbhost"=>"localhost", 
		"dbname"=>"your_data_base", 
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
// Index in the App dir has the following cond

```php
<?php
use App\User;
use DataFrame\Route;
use DataFrame\Token;
use DataFrame\Session;
use DataFrame\Request;
use DataFrame\Response;
use DataFrame\validate;
include_once("header.php");

Route::get("/", function(){
	return view("register");
});

Route::get("/login", function(){
	return view("login");
});

Route::get("/reset", function(){
	return view("resetpassword");
});

Route::post("/login", function(Request $request, Response $response, Session $session, User $user, Token $token, Validate $validate){
	if($request->exists()){
		if($token->check($request->get(getGlobals('token.token_name')))){
			$validation = $validate->check($_POST, [
				'username|Email or Username' => [
					'required' => true,

				],
				'password|Password' => [
					'required' => true
				]
			]);

			if($validation->passed()){
				$username = $request->get('username');
				$password = $request->get('password');
				if($user->where('username', $username)->orWhere('email', $username)->first()){
					$login = $user->where('username', $username)->orWhere('email', $username)->first();
					if($login->password == $password){
						if($login->type_id == 3){
							$session->login($login);
							$response->json(['redirect_url' => '/dashboard'], 200);
						}else{
							$msgs = [
			                	'errors' => 'Sorry You need to be an admin',
			                    'token' => csrf_token()
			               	];
			            	$response->json(['responseText' => $msgs], 1000);
						}
						
					}else{
						$msgs = [
		                	'errors' => 'Wrong Username/Email and Password combination',
		                    'token' => csrf_token()
		               	];
		            	$response->json(['responseText' => $msgs], 1000);
					}
				}else{
					$msgs = [
	                	'errors' => 'Unknown Username or Email',
	                    'token' => csrf_token()
	               	];
	            	$response->json(['responseText' => $msgs], 1000);
				}
			}else{
				$msgs = [
                	'errors' => implode("<br />",$validation->errors()),
                    'token' => csrf_token()
               	];
            	$response->json(['responseText' => $msgs], 1000);
			}
		}
	}
	//$response->json(['responseText' => $request->get('password')]);
});


```
- creation of a Controller

```php
<?php
namespace App;
use DataFrame\Controllers\Controller;
class HomeController extends Controller{
	public function index(){
		$data = [
			'name' => 'Hamidouh'
		];
		return view("home", $data);
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
class HomeController extends Controller{
	private $user;
	private $post;
	public function __construct(User $user, Post $post){
		parent::__construct();
		$this->user = $user;
		$this->post = $post;
	}
	public function userInfo(Request $request, $userId){
		// a new Request object is return and the variable in the url for $userId is also return in this case
		echo $userId;
	}
}
```

Let's make Some Relations in the DataModels
- one-to-one, one-to-many, many-to-many, polymophic relations are the few that come with the framework

```php
<?php

namespace App;
use DataFrame\Models\Elegant;

class User extends Elegant{
	protected static $table_name = "user_table"; // optional assumed to be users if absent
	public $created_at = true; // if you want the framework to manage your dates
	public $updated_at = true;
	public function userType(){
		return $this->hasOne("Type");
	}
	public function posts(){
		return $this->hasMany("Post");
	}
	
	public function photos(){
		return $this->mergeableMany("Photograph", "imageable");
	}
	
	public function comments(){
		return $this->hasMany("Comment");
	}
}

```
```php
<?php
namespace App;
use DataFrame\Models\Elegant;
// Post.php

class Post extends Elegant{
	// framework assumes the table to be the lowercase plural form of the class name
	protected $primaryKey = "postId";
	public function user(){ // takes it the the foreign key is the function name underscore id (user_id)
		return $this->belongsTo("User");
	}
	public function comments(){
		// assumes foreign key to be post_id, so you can define yours i.e postId
		return $this->hasMany("Comment", "postId");
	}
	public function likes(){
		return $this->mergeableMany("Like", "likeable");
	}
}

```
```php
<?php

namespace App;
use DataFrame\Models\Elegant;
// Comment.php

class Comment extends Elegant{
	protected static $table_name = "comment";
	public function post(){
		return $this->belongsTo("Post");
	}
	
	public function user(){
		return $this->belongsTo("User");
	}
	
	public function likes(){
		return $this->mergeableMany("Like", "likeable");
	}
}


```

```php
<?php

namespace App;
use DataFrame\Models\Elegant;
// Like.php

class Like extends Elegant{
	public function likeable(){ // this will return the appropriate object that was liked
		return $this->mergeable(); // 
	}
}



``hax
// main.hax.php

<html>
<title>Hello</title>
<body>
@yield('main_content')

</body>



// home.hax.php


@extends('main')

@section("main_content")
<div>
	Hello World
</div>
@endsection
