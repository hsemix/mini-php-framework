<?php
namespace DataFrame\Middleware;
use DataFrame\Response;
use DataFrame\Session;
class Authenticate {
    protected $auth;
    public function __construct(Session $auth){
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
            return Response::redirectTo("/auth/login");
        }
        return $next($request);
    }
}