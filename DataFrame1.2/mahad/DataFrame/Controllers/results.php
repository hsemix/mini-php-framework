<?php
namespace DataFrame\Controllers;
use DataFrame\Request;
use DataFrame\Response;
trait Results {
    public $request ;
    public $response;
    public function request(){
        return Request::instance();
    }
    public function response(){
        return Response::instance();
    }
}