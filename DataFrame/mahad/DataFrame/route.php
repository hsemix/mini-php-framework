<?php
namespace DataFrame;
use DataFrame\Middleware\MiddleWare;
class Route {
	private $uri = array();
	private $methods = [];
	private $url = [];
	private $method;
	private $segments;
	public $params;
	public $vars = array();
	private static $instances = array();
	public static $route_found = false;
	public static $middleware_found = false;
	public $fed_uri;
	public function __construct(){
		ob_start();
		$request = new Request();
		$this->url = $request->getPath();
		$this->segments = explode('/', trim($this->url, '/'));
		$this->method = $request->getMethod();
	}

	final public static function instance(){
		$class_name = get_called_class();

		if (!isset(self::$instances[$class_name]))
			self::$instances[$class_name] = new $class_name;

		return self::$instances[$class_name];
	}
	public static function get($uri, $method){
		$request = new Request();
		$self = self::instance();
		$self->method = $request->getMethod();
		$self->fed_uri = $uri;
		static::foundRoute($uri, $method, "GET");
		$self->fed_uri = "";
		//return $self;
	}

	public static function foundRoute($uri, $method, $type){
		$self = self::instance();

		if( $type == 'XMLHttpRequest' )
		  $self->method = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? $_SERVER['HTTP_X_REQUESTED_WITH'] : 'GET';

		$route_and_route_symbols = static::getRouteSymbols($uri);
		$route = $route_and_route_symbols['route'];
		$route_and_route_symbols['values'];
		

		if(static::$route_found || (!preg_match('@^'.$route.'(?:\.(\w+))?$@uD', $self->url, $matches) || $self->method != $type) ) {
			return false;
		}
		static::$route_found = true;
		$self->setParams($route_and_route_symbols['values']);
		
		if(is_string($method)){
			$useMethod = explode("@", $method, 2);
			$response = new Response();
			$response->setController(array("controller"=>$useMethod[0], "method"=>$useMethod[1]));
		}elseif(is_array($method)){
			$middleWare = $method['middleware'];
			$method = $method[0];
			
			
			$self->middleware($middleWare);
			if(is_string($method)){
				$useMethod = explode("@", $method, 2);
				$response = new Response();
				$response->setController(array("controller"=>$useMethod[0], "method"=>$useMethod[1]));
			}else{
				$reflection = new \ReflectionFunction($method);
				$classes = array();
				foreach($reflection->getParameters() as $param){
					if($param->getClass() != ''){
						$class = $param->getClass()->name;
						$classes[] = new $class();
					}elseif(!empty($route_and_route_symbols['values'])){
							foreach($route_and_route_symbols['values'] as $var => $val){
								if($var == $param->name){
									$classes[] = $val;
								}
							}
					}else{
						$classes[] = self::instance();
					}
					
				}
				call_user_func_array($method, $classes);
			}
		}else{
			$reflection = new \ReflectionFunction($method);
			$classes = array();
			foreach($reflection->getParameters() as $param){
				if($param->getClass() != ''){
					$class = $param->getClass()->name;
					$classes[] = new $class();
				}elseif(!empty($route_and_route_symbols['values'])){
						foreach($route_and_route_symbols['values'] as $var => $val){
							if($var == $param->name){
								$classes[] = $val;
							}
						}
				}else{
					$classes[] = self::instance();
				}
				
			}
			call_user_func_array($method, $classes);
		}
	}

	private function extractParams($route) {
		$params = [];
		$pathParts = '/'.$this->segments;
		$routeParts = explode('/', $route);
		foreach ($routeParts as $key => $routePart) {
			if (strpos($routePart, ':') === 0) {
				$name = substr($routePart, 1);
				$params[$name] = $pathParts[$key+1];
			}
		}
		return $params;
	}

	
	public static function getRouteSymbols($route){
		$self = self::instance();
		$segments = explode('/', trim($route, '/'));
		$r_segments = [];	
		$route_and_route_symbols = [];
		$route_symbols = [];
		$values = [];
		for($i=0; $i < sizeof($segments); $i++){
			$seg = $segments[$i];
			if(!empty($seg)){
				if($seg[0] == ':'){
					$route_symbols[$i+1] = str_replace(':', '', $seg);
					$r_seg = '(.*?)';
					$values[$route_symbols[$i+1]] = isset($self->segments[$i]) ? $self->segments[$i] : null;
				}else {
					$r_seg = $seg;
				}	
				$r_segments[] = $r_seg;	
			}
		}
		$route =  '/' . implode('/', $r_segments);
		$route_and_route_symbols['route'] = $route;
		$route_and_route_symbols['route_symbols'] = $route_symbols;
		$route_and_route_symbols['values'] = $values;
		$self->params[] = $values;
		
		return $route_and_route_symbols;
	}
	private function setParams($params){
		$this->params = $params;
	}

	

	public function getParams(){
		return $this->params;
	}

	public function post($uri, $method){
		$request = new Request();
		$self = self::instance();
		$self->method = $request->getMethod();
		static::foundRoute($uri, $method, "POST");
		return $self;
	}
	public function put($uri, $method){
		$request = new Request();
		$self = self::instance();
		$self->method = $request->getMethod();
		static::foundRoute($uri, $method, "PUT");
		return $self;
	}
	public function delete($uri, $method){
		$request = new Request();
		$self = self::instance();
		$self->method = $request->getMethod();
		static::foundRoute($uri, $method, "DELETE");
		return $self;
	}

	public function middleware($string) {
		$middleWare = new MiddleWare();
		$self = self::instance();
		$request = Request::instance();
		
		
		if(in_array($string, array_keys($middleWare->routerMiddleWare))){
			if(strstr($request->getPath(), $self->segments[0])){
				$routeMiddleWare = new $middleWare->routerMiddleWare[$string](Session::instance());
				$self = self::instance();
				$results = $routeMiddleWare->run($request, function() use($request){
					if(!$request){
						return false;
					}
					return $request;
					
				});
				if($results instanceof $request){
					
				}else{
					exit();
				}
				
			}
		}
	}

	
	
}

$route = new Route();