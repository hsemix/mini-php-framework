<?php
namespace DataFrame\Database\Elegant\Association;
use DataFrame\Database\Query\Builder;
use DataFrame\Database\Elegant\Model;
abstract class Association{
    private $query;
    private $parent;
    private $child;
    public function __construct(Builder $query, Model $parent){
        $this->query = $query;
        $this->parent = $parent;
        $this->child = $query->getModel();
        $this->addConditions();
    }

    abstract public function addConditions();
    /**
    *   Redirect all unknown methods to the query Builder, it could be aware of them
    */

    public function __call($method, $parameters){
        $result = call_user_func_array([$this->query, $method], $parameters);

        if ($result === $this->query) {
            return $this;
        }

        return $result;
    }
    
}