<?php
namespace DataFrame\Database\Support\Helpers;
use ReflectionClass;
use InvalidArgumentException;
class FileLocator{
    private $namespaceMap = [];
    private $defaultNamespace = 'global';
    private static $instances = array();
    public function __construct(){
        $this->traverseClasses();
    }
    public function getNamespaceFromClass($class){
        $reflection = new ReflectionClass($class);
        return $reflection->getNameSpaceName()===''
                ? $this->defaultNamespace:
                $reflection->getNameSpaceName();
    }
    public function traverseClasses(){
        $classes = get_declared_classes();
        foreach($classes as $class){
            $namespace = $this->getNamespaceFromClass($class);
            $this->namespaceMap[$namespace][] = $class;
        }
    }

    public function getClassesOfNamespace($namespace){
        if(!isset($this->namespaceMap[$namespace]))
            throw new InvalidArgumentException('The Namespace '.$namespace.' doesnot exist');
        return $this->namespaceMap[$namespace];
    }
    public function getNameSpaces(){
        return array_keys($this->namespaceMap);
    }
    final public static function instance(){
		$class_name = get_called_class();

		if (!isset(self::$instances[$class_name]))
			self::$instances[$class_name] = new $class_name;

		return self::$instances[$class_name];
	}
}