<?php
namespace DataFrame\Views;
class SmxView {

    protected $template_dir = './App/templates/';
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
         if(isset($this->dataView[$name])) {  
            return $this->dataView[$name];
        }
        else {//if(isset($this->vars[$name])) {
            return $this->vars[$name];
        }
        /*else {
            return false;
        }*/
        //return $this->vars[$name];
    }
	public function display($temp, $data = false){
        if($data) {
            echo $this->render($temp, $data);
        }else{
            echo $this->render($temp);
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

