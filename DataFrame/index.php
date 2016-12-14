<?php
include_once("header.php");

use DataFrame\Route;
use DataFrame\Views\View;

Route::get("/", function(){
    return new View("index");
});
Route::get("/login", function(){
    return new View("index");
});

Route::get("/home",["middleware" => "loggedIn","App\HomeController@feeds"]);
Route::post("/home",["middleware" => "loggedIn","App\HomeController@postFeeds"]);
Route::get("/logout", function(DataFrame\Response $res) use ($session){ // $session can be globally access
    if($session->is_logged_in()){
        $session->logout();
    }
    return $res->redirectTo("/login");
});
Route::post("/register","App\RegisterController@registerUser");



Route::get("/pages",["middleware" => "loggedIn","App\PageController@userPages"]);
Route::get("/pages/new",["middleware" => "loggedIn","App\PageController@newUserPage"]);

