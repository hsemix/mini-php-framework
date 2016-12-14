<?php
namespace DataFrame;
use DataFrame\Session;
use DataFrame\Request;
use DataFrame\Response;
trait Universal{
    public $session;
    public $request ;
    public $response;

    public function globalSession(){
        @$session = Session::instance();
        return $session;
    }
    public function getLoggedInUserId(){
        @$this->session = $this->globalSession()->user_id;
        return @$this->session;
    }
    public function getRequest(){
        $this->request = Request::instance();
        return $this->request;
    }
    public function getResponse(){
        $this->response = Response::instance();
        return $this->response;
    }
}