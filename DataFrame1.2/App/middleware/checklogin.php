<?php
namespace App;
use DataFrame\Session;
use DataFrame\Response;
class CheckLogin{
    protected $auth;
    protected $res;
    public function __construct(Session $auth, Response $res){
        $this->auth = $auth;
        $this->res = $res;
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
            $this->res->redirectTo("/login");
        }
        return $next($request);
    }
}