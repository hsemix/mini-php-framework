<?php
namespace DataFrame\Views;
include_once('ti.php');
class SmxView {

    protected $template_dir = './App/templates/';
    protected $template_cache_dir = './App/template_caches/';
    protected $vars = array();
    private $dataView = array(); 

    public function __construct($template_dir = NULL) {
        if ($template_dir) {
            $this->template_dir = $template_dir;
        }
    }
    
    public function render($template_file, $data = false) {
        if($data) {
            $this->dataView = $data;
        }
		$rendered = "";
        if (file_exists($this->template_dir.$template_file)) {
			extract($this->vars);
			ob_start();
            require_once $this->template_dir.$template_file;
			$rendered = ob_get_contents(); 
			ob_end_clean();  
        } else {
            try{
                throw new \Exception('no template file ' . $template_file . ' present in directory ' . $this->template_dir);
            }catch(\Exception $e){
                return $e->getMessage();
            }
        }
        $this->dataView = array();
		return $rendered;
    }
    
    public function __set($name, $value) {
        $this->vars[$name] = $value;
    }
    
    public function __get($name) {
         if(isset($this->dataView[$name])){  
            return $this->dataView[$name];
        }else{
            return $this->vars[$name];
        }
    }
	public function display($temp, $data = false){
        if(strstr($temp, ".hax")){
           //echo $this->renderHax($temp);
           echo $this->renderHaxTemplate($temp);
        }else{
            if($data) {
                echo $this->render($temp, $data);
            }else{
                echo $this->render($temp);
            }
        }
		
	}
    public function renderHaxTemplate($templateName) {
        $templateLocation = $this->template_dir.$templateName;
        $cacheLocation    = $this->template_cache_dir.$templateName;
        if (file_exists($this->template_dir.$templateName)) {
            if (!file_exists($cacheLocation) || filemtime($cacheLocation) < filemtime($templateLocation)) { 
                $code = file_get_contents($templateLocation);
                if(strstr($code, "<?php")){
                    die("php tags detected in $templateLocation, use {% } instead");
                }

                if(strstr($code, "<?")){
                    die("php tags detected in $templateLocation, use {% } instead");
                }
                if(strstr($code, "<?=")){
                    die("php tags detected in $templateLocation, use {% } instead");
                }
                
                if(strstr($code, "?>")){
                    die("php tags detected in $templateLocation, use {% } instead");
                }

                if(preg_match("~@extends(.*)~", $code, $matches)){
                    
                    $master = preg_replace("~\(\'~", '', $matches[1]); 
                    $master = preg_replace("~\'\)~", '', $master); 
                    $master = preg_replace("~\s+~", '', $master); 
                    $file = $master.".hax.php";
                    $masterLocation = $this->template_dir.$file;
                    $masterCacheLocations = $this->template_cache_dir.$file;
                    
                    if(file_exists($masterLocation)){
                        if (!file_exists($masterCacheLocations) || filemtime($masterCacheLocations) < filemtime($masterCacheLocations)) {
                            $masterContents = file_get_contents($masterLocation);
                            $masterContents = preg_replace('~@yield(.*)~', '<?php  emptyblock$1 ?>', $masterContents);
                            $masterContents = preg_replace('~@section(.*)~', '<?php  startblock$1 ?>', $masterContents);
                            $masterContents = preg_replace('~@endsection(.*)~', '<?php  endblock() ?>', $masterContents);
                            
                            file_put_contents($masterCacheLocations, $masterContents);
                        }
                    }
                }

               // print_r(_ti_callingTrace());
                //die();
                $code = preg_replace('~@extends(.*)~', '<?php  include_once("'.$file.'") ?>', $code);
                $code = preg_replace('~@section(.*)~', '<?php  startblock$1 ?>', $code);
                $code = preg_replace('~@parent~', '<?php  superblock() ?>', $code);
                $code = preg_replace('~@endsection(.*)~', '<?php  endblock() ?>', $code);
                $code = preg_replace('~\{%\s*(.+?)\s*\}~', '<?php $1 ?>', $code); // single line php code
                $code = preg_replace('~\{%~', '<?php', $code); // start php block
                $code = preg_replace('~\{{\s*(.+?)\s*\}}~', '<?php echo htmlspecialchars($1, ENT_QUOTES) ?>', $code);
                $code = preg_replace('~\{{!!\s*(.+?)\s*\}}~', '<?php echo $1 ?>', $code);
                $code = preg_replace('~\%}~', '?>', $code); // end php block
                $code = preg_replace('~@if(.*)~', '<?php  if $1: ?>', $code); // php if
                $code = preg_replace('~@elseif(.*)~', '<?php  elseif $1: ?>', $code);
                $code = preg_replace('~@else~', '<?php  else: ?>', $code);
                $code = preg_replace('~@endif~', '<?php  endif; ?>', $code);
                $code = preg_replace('~@foreach(.*)~', '<?php  foreach $1: ?>', $code);
                $code = preg_replace('~@endforeach~', '<?php  endforeach; ?>', $code);
                file_put_contents($cacheLocation, $code);
            }
            extract($this->vars, EXTR_SKIP);
            include_once $cacheLocation;
        }else{
            die('no template file ' . $templateName . ' present in directory ' . $this->template_dir);
        }
    }
    public function displayArr ($fileName, $dataAr) { 
        $rendered = "";
        if(count($dataAr && is_array($dataAr))) {
            foreach($dataAr AS $data) {
                $rendered.= $this->display($fileName, $data);
            }
        }
        return $rendered;
    
    }
}
$smxview = new SmxView("views/");