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

Route::get('/dashboard', ['middleware' => 'loggedIn','App\HomeController@index']);
Route::get('/messenger', ['middleware' => 'loggedIn','App\HomeController@messenger']);
Route::get('/signout', ["middleware" => "loggedIn","App\HomeController@userLogout"]);
Route::post('/reset', "App\UserController@passwordReset");
