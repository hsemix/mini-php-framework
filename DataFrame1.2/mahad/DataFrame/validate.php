<?php
namespace DataFrame;
class Validate {
    private $passed = false;
    private $errors = array();
    
    public function check($source, $items = array()){
        foreach($items as $item => $rules){
            $label = $item;
            foreach($rules as $rule => $rule_val){
                
                if(strstr($item, '|')){
                    $string = explode('|', $item);
                    $item = $string[0];
                    $label = $string[1];
                }
                
                $value = trim($source[$item]);
                
                if($rule == 'required' && empty($value)){
                    $this->addError("{$label} is required");
                }else if(!empty($value)){
                    switch($rule){ 
                        case 'min':
                            if(strlen($value) < $rule_val){
                                $this->addError("{$label} must be a minimum of {$rule_val} characters");
                            }
                        break;
                        case 'max':
                            if(strlen($value) > $rule_val){
                                $this->addError("{$label} must be a maximum of {$rule_val} characters");
                            }
                        break;
                        case 'matches':
                            if($value != $source[$rule_val]){
                                $this->addError("{$rule_val} must match {$label}");
                            }
                        break;
                        case 'unique':
                            if($rule_val::where("{$item}", $source[$item])->first()){
                                $this->addError("{$label} already exitst!");
                            }
                        break;
                    }
                }
            }
        }

        if(empty($this->errors)){
            $this->passed = true;
        }
        return $this;
    }

    private function addError($error){
        $this->errors[] = $error;
    }

    public function errors(){
        return $this->errors;
    }
    public function passed(){
        return $this->passed;
    }
}