<?php
namespace DataFrame;
use DataFrame\Session;
use DataFrame\Request;
use DataFrame\Response;
use DataFrame\MiddleWare\MiddleWare;
trait Universal{
    public $session;
    public $request ;
    public $response;

    public function globalSession(){
        $session = Session::instance();

        return $session;
    }
    public function getLoggedInUserId(){
        
        if(Session::exists(getGlobals('session.session_name'))){
            $session = $this->globalSession();
            $session->user_id = Session::get(getGlobals('session.session_name'));
            return Session::get(getGlobals('session.session_name'));
        }
    }
    public function getRequest(){
        $this->request = Request::instance();
        return $this->request;
    }
    public function getResponse(){
        $this->response = Response::instance();
        return $this->response;
    }
	public function middleWare($ware){
		$middleWare = new MiddleWare();
		$routeMiddleWare = new $middleWare->routerMiddleWare[$ware](Session::instance());
        $request = request();
        $results = $routeMiddleWare->run($request, function() use($request){
            if(!$request){
                return false;
            }
            return $request;
            
        });
	}
}