<?php
namespace DataFrame\Database\Elegant\Association;
use DataFrame\Database\Elegant\Model;
use DataFrame\Database\Query\Builder;
use DataFrame\Database\Elegant\Collection;
class BelongsTo extends Association{
    protected $foreignKey;
    protected $otherKey;
    protected $child;
    public function __construct(Builder $query, Model $parent, $foreignKey, $otherKey, $child){
        $this->otherKey = $otherKey;
        $this->child = $child;
        $this->foreignKey = $foreignKey;
        $this->parent = $parent;
        $this->query = $query;
        parent::__construct($query, $parent);
    }
    public function addConditions(){
        $table = $this->child->getTable();
        $this->query->where($table.'.'.$this->otherKey, '=', $this->parent->{$this->foreignKey});
    }
}