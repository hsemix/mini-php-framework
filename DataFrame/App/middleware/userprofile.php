<?php
namespace App;
use DataFrame\Views\View;
use Closure;
class UserProfile{
    
    public function run($request, Closure $next){
        $uris = ['/terms', '/info'];
        if(in_array($request->getPath(), $uris)){
             return new View('test');
        }
        return $next($request);
    }
}