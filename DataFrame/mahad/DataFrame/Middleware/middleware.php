<?php
namespace DataFrame\Middleware;
class MiddleWare{
    /**
    * The apps route's middleware is to be registered here
    *
    * @var array
    */
    public $routerMiddleWare = [
        "auth" => \DataFrame\Middleware\Authenticate::class,
        "userProfile" => \Byakuno\UserProfile::class,
        "loggedIn" => \Byakuno\CheckLogin::class
    ];
}