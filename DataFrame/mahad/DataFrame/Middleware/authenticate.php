<?php
namespace DataFrame\Middleware;
use DataFrame\Response;
class Authenticate{
    protected $auth;
    public function __construct($auth){
        $this->auth = $auth;
    }
	/**
    * Run an incoming request.
    *
    * @param \DataFrame\Request $request
    * @param \Closure $next
    * @return mixed
    */
    public function run($request, \Closure $next){
        if(!$this->auth->is_logged_in()){
            Response::redirectTo("/auth/login");
        }else{
            return $next($request);
        }
       // return $next($request);
    }
}