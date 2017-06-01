<?php
//error_reporting(0);
function view($args, $data = false){
    if($data){
        return new \DataFrame\Views\View($args, $data);
    }else{
        return new \DataFrame\Views\View($args);
    }
}

function request(){
    return \DataFrame\Request::instance();
}
function response($text = null, $status = 200){
    return new \DataFrame\Response($text, $status);
}

function resolve(){
    return new \DataFrame\Helpers\Resolve();
}

function session(){
    return \DataFrame\Session::instance();
}

function cookie(){
    return \DataFrame\Cookie::instance();
}

function token(){
    return new \DataFrame\Token();
}


function getGlobals($path = null){
    if($path){ 
        $config = (isset($GLOBALS['config']))?$GLOBALS['config']:'';
        $path = explode('.', $path);  
        foreach($path as $bit){  
            if(isset($config[$bit])){
                $config = $config[$bit];
            }      
        }	
        return $config;   
    } 
    return false;  
}


function csrf_token(){
    return \DataFrame\Token::generate();
}

//register_shutdown_function('errorHandler');
function errorHandler() {
   $err = error_get_last();
   $css = response()->getOrSetVars()->host;
   if($err)
     include_once "custom_error.php"; // your custom error page
     
}

function class_base($class){
    $class = is_object($class) ? get_class($class) : $class;
    return basename(str_replace('\\', '/', $class));
}


if (! function_exists('data_get')) {
    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param  mixed   $target
     * @param  string|array  $key
     * @param  mixed   $default
     * @return mixed
     */
    function data_get($target, $key, $default = null)
    {
        if (is_null($key)) {
            return $target;
        }

        $key = is_array($key) ? $key : explode('.', $key);

        while (($segment = array_shift($key)) !== null) {
            if ($segment === '*') {
                if ($target instanceof \DataFrame\Collection) {
                    $target = $target->all();
                } elseif (! is_array($target)) {
                    return value($default);
                }

                $result = \DataFrame\Arr::pluck($target, $key);

                return in_array('*', $key) ? \DataFrame\Arr::collapse($result) : $result;
            }

            if (\DataFrame\Arr::accessible($target) && \DataFrame\Arr::exists($target, $segment)) {
                $target = $target[$segment];
            } elseif (is_object($target) && isset($target->{$segment})) {
                $target = $target->{$segment};
            } else {
                return value($default);
            }
        }

        return $target;
    }
}

if (! function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    function value($value){
        return $value instanceof Closure ? $value() : $value;
    }
}