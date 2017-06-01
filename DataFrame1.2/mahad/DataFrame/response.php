<?php
namespace DataFrame;
use DataFrame\Helpers\Resolve;
class Response {
    private static $instances = array();
    public function __construct($text = null, $status = 200){
        http_response_code($status);
        if($text){
            echo $text;
        }
        return $this;
    }
    public function header($contextType, $type){
        header("{$contextType}: {$type}");
        return $this;
    }
    public function json($array, $status = 100){
        $this->header("Content-Type", "application/json");
        $array['status'] = $status;
        //return 
        die(json_encode($array));
    }
    public function setController($options){
        
        $route = Route::instance();
        if(is_array($options)){
            $controller = ucfirst($options['controller']);
            
            $controller = (new Resolve)->resolve($controller);
            //$controller = new $controller($parameters); //new $controller($params);//$controller::instance();
            $method = $options['method'];
            
            if(isset($options['extras'])){
                $params = array_merge($route->getParams(), $options['extras']);
            }else{
                $params = $route->getParams();
            }
        }else{
            $options = explode("@", $options, 2);
            $controller = $options[0];
            $controller = $controller::instance();
            $method = $options[1];
            $params = $route->getParams();
        }
        $rc = new \ReflectionClass($controller);
        if($rc->hasMethod($method)){
            $rm = $rc->getMethod($method);
            $rp = $rm->getParameters();
            $dependecies = [];
            foreach($rp as $p){
                $dependency = $p->getClass();
                if(!is_null($dependency)){
                    $dependecies[] = new $dependency->name;
                }
            }
            $params = array_merge($dependecies, $params);
        }
        
        if(call_user_func_array([$controller, $method], $params)){
            return call_user_func_array([$controller, $method], $params);
        }else{
           // return "The method $method in class $controller doesnot exist";
            //print_r($controller);
        }

    }

    public static function redirectTo($url){
        $settings = parse_ini_file("config/config.ini");
        $req = Request::instance();
        if($req->isAjax()){
            self::contentType('application/json');
            if(is_array($url)){
                if(isset($url['status'])){
                    $status = $url['status'];
                }else{
                    $status = 200;
                }
                if(isset($url['status_string'])){
                    $status_string = $url['status_string'];
                }else{
                    $status_string = "status";
                }
                if(isset($url['string'])){
                    $string = $url['string'];
                }else{
                    $string = "redirect_url";
                }
                if(isset($settings['root'])){
                    $uri = "/".$settings['root'].$url['url'];
                }else{
                    $uri = $url['url'];
                }


                die(json_encode(array($status_string => $status, $string => $uri)));
            }else{
                if(isset($settings['root'])){
                    $url = "/".$settings['root'].$url;
                }
                die(json_encode(['status' => 200, 'redirect_url' => $url]));
               // return header("Location: $url");
            }
        }else{
            header("HTTP/1.1 301 Moved Permanently");
            if(isset($settings['root'])){
                $url = "/".$settings['root'].$url;
            }

            
            header("Location:$url");
            exit();
        }
        
    }
    public static function returnMessage($msg, $stat = false){
        $req = Request::instance();
        if($req->isAjax()){
            self::contentType('application/json');
            $status = 100;
            if($stat){
                $status = $stat;
            }
            die(json_encode(array("status" => $status, "responseText" => $msg)));
        }else{
            $_SESSION['msg'] = $msg;
            if($stat){
                $self = self::instance();
                return $self->writeLine($msg);
            }
        }
    }
    public static function contentType($type){
        return header("Content-type: $type");
    }

    public function writeLine($text){
        print($text);
    }

    final public static function instance(){
        $class_name = get_called_class();

        if (!isset(self::$instances[$class_name]))
            self::$instances[$class_name] = new $class_name;

        return self::$instances[$class_name];
    }
    public function getOrSetVars(){
        return Starter::instance()->temp();
    }
}