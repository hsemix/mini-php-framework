<?php
namespace DataFrame\Files;
use DataFrame\Request;
class File{
    private $name;
    private $type;
    private $size;
    private $extension;
    private $file;
    private $temp_path;
    public function __construct($file){
        $request = new Request();
        $this->file = $request->getFile($file);
        $this->temp_path = $this->file['tmp_name'];
        $this->name = $this->file['name'];
        $this->size = $this->file['size'];
        $this->type = $this->file['type'];
    }

    public function getName(){
        return $this->name;
    }
    public function getExtension(){
        $file = explode('.', basename($this->getName()));
        return end($file);
    }

    public function getSize(){
        return $this->size;
    }

    public function realPath(){
        return $this->temp_path;
    }

    public function move($destination, $filename){
        $result = false;
        if($this->getName() != '' && $this->realPath() != ''){

            if(is_array($this->realPath())){
                for($i = 0; $i < count($this->realPath()); $i++){
                    if($filename[$i] != ''){
                        move_uploaded_file($this->realPath()[$i], $destination.'/'.$filename[$i]);
                        $result = true;
                    }
                }
            }else{
                if($filename != ''){
                     move_uploaded_file($this->realPath(), $destination.'/'.$filename);
                     $result = false;
                }
            }
        }
        return $result;
    }
}