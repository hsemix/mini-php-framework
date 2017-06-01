<?php
namespace DataFrame\Views;
class SmxView {

    protected $template_dir = './App/templates/';
    protected $template_cache_dir = './App/template_caches/';
    protected $vars = array();
    private $dataView = array(); 

    public function __construct($template_dir = NULL) {
        if ($template_dir) {
            // you should check here if this dir really exists
            //$this->template_dir = $_SERVER['DOCUMENT_ROOT'].'/'.$template_dir;
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
            throw new \Exception('no template file ' . $template_file . ' present in directory ' . $this->template_dir);
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

    public function renderHax($temp, $data = false){
        if($data) {
            $this->dataView = $data;
        }
        $path = $temp;
        
        if (file_exists($this->template_dir.$path)) {
            extract($this->vars);
            $content = file_get_contents($this->template_dir.$path);
            /*foreach($this->vars as $key => $value){
                $content = preg_replace('/\{\$'.$key.'\}/', $value, $content);
            }

            $content = $this->replace($content, $this->vars);
            $content = preg_replace('~@if((.*))~', '<?php if($1): ?>', $content);
            $content = preg_replace('~@else~', '<?php else: ?>', $content);
            $content = preg_replace('~@endif~', '<?php endif; ?>', $content);

            $content = preg_replace('~@foreach.*(\$\w*).*(\$\w*).*~', '<?php foreach(\\1 as \\2):?>', $content);
            $content = preg_replace('~@endforeach~', '<?php endforeach; ?>', $content);
            ob_start(); 
            eval(' ?>'.$content.'<?php ');
           // echo $content;
            return ob_get_clean();   */   

            $eot='_EOT_'.rand(1,999999).'_EOT_';
            $do='passthrough';
            $content=preg_replace(
                array(
                    '#@if((.*))#', 
                    '#@elseif((.*))#', 
                    '#@else#', 
                    '#@endif#', 
                    '#@foreach.*(\$\w*).*(\$\w*).*(\$\w*).*#', 
                    '#@foreach.*(\$\w*).*(\$\w*).*#', 
                    '#@endforeach#',
                    '#{#', '#}#',
                    '#{_#', '#_}#',
                ),
                array(
                    "<?php if($1):?>",
                    "<?php elseif($1):?>",
                    "<?php else:?>",
                    "<?php endif;?>",
                    "<?php foreach (\\1 as \\2=>\\3): ?>", 
                    "<?php foreach (\\1 as \\2): ?>", 
                    "<?php endforeach ?>",
                    "<?php echo <<<$eot\n{\$this->passthrough(", ")}\n$eot\n ?>",
                    "<?php ", " ?>",
                ), 
                $content
            );
            ob_start(); 
            eval(' ?>'.$content.'<?php ');
            //echo $content;
            return ob_get_clean();      
        }
    }

    function renderHaxTemplate($templateName) {
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
                    
                    if(file_exists($masterLocation)){
                        $masterContents = file_get_contents($masterLocation);
                        $masterYieldVals = [];
                        $childYieldVals = [];
                        if(preg_match_all("~@yield(.*)~", $masterContents, $matches)){
                            $masterYield = preg_replace("~\(\'~", '', $matches[1]); 
                            $masterYield = preg_replace("~\'\)~", '', $masterYield); 
                            $masterYield = preg_replace("~\s+~", '', $masterYield);
                            $masterYieldVals = $masterYield;
                        }
                        if(preg_match_all("#@section(.*)#", $code, $tempMatches)){
                            $tempSection = preg_replace("~\(\'~", '', $tempMatches[1]); 
                            $tempSection = preg_replace("~\'\)~", '', $tempSection); 
                            $tempSection = preg_replace("~\s+~", '', $tempSection);
                            $childYieldVals = $tempSection;
                        }

                        foreach($childYieldVals as $yieldVals){
                            $template = preg_match("#\\@section(.*)\\@endsection#s", $code, $tempMatches);
                            $template = preg_replace("~\(\'".$yieldVals."\'\)~", '', $tempMatches[1]);

                            $masterTemplate = preg_match_all("#@yield(.*)#", $masterContents, $masterMatches);
                            $masterTemplate = preg_replace("~\(\'".$yieldVals."\'\)~", $template, $masterContents);
                            $masterTemplate = preg_replace("~\@yield~", '', $masterTemplate);
                            //$code = $masterTemplate;
                            
                        }
                           print_r($masterTemplate); 
                            /*if(preg_match("~@section(.*)~", $code, $matches)){
                                $tempSection = preg_replace("~\(\'~", '', $matches[1]); 
                                $tempSection = preg_replace("~\'\)~", '', $tempSection); 
                                $tempSection = preg_replace("~\s+~", '', $tempSection);
                                if($masterYield == $tempSection){
                                    $template = preg_match("#\\@section(.*)\\@section#s", $code, $tempMatches);
                                    $template = preg_replace("~\(\'".$tempSection."\'\)~", '', $tempMatches[1]);
                                    $masterTemplate = preg_match("#@yield(.*)#", $masterContents, $masterMatches);
                                    $masterTemplate = preg_replace("~\(\'".$masterYield."\'\)~", $template, $masterContents);
                                    $masterTemplate = preg_replace("~\@yield~", '', $masterTemplate);
                                    
                                }
                            }*/
                        //}
                        
                    }else{
                       // echo 'no';
                    }
                    die();
                   //$code = $masterTemplate;
                }
                die();

                
                $code = preg_replace('~\{%\s*(.+?)\s*\}~', '<?php $1 ?>', $code); // single line php code
                $code = preg_replace('~\{%~', '<?php', $code); // start php block
                $code = preg_replace('~\{\s*(.+?)\s*\}~', '<?php echo htmlspecialchars($1, ENT_QUOTES) ?>', $code);
                $code = preg_replace('~\{!!\s*(.+?)\s*\}~', '<?php echo $1 ?>', $code);
                $code = preg_replace('~\}~', '?>', $code); // end php block
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

    function callingTrace(){
        $trace = debug_backtrace();
        foreach ($trace as $i => $location) {
            if ($location['file'] !== __FILE__) {
                return array_slice($trace, $i);
            }
        }
    }

    public function masterPage($section){
        echo 'kdkdkd';
    }

    public function passthrough ($v) { return $v; }
    public function displayArr ($fileName, $dataAr) { 
        $rendered = "";
        if(count($dataAr && is_array($dataAr))) {
            foreach($dataAr AS $data) {
                $rendered.= $this->display($fileName, $data);
            }
        }
        return $rendered;
    
    }
    private function replace($content, $vars) {
        foreach ($vars as $key => $value) {
            if (!is_array($vars[$key])) {
                $tag = '{$' . $key . '}';
                $content = str_replace($tag, $value, $content);
            }else{
                
                $this->vars[$value][$key] = $value;
            }
        }

        return $content;
    }
	

}
$smxview = new SmxView("views/");

