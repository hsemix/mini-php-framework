<?php
namespace DataFrame\Database\Elegant\Association;
use DataFrame\Database\Query\Builder;
use DataFrame\Database\Elegant\Model;
class HasOne extends Association{
    private $otherKey;
    private $child;
    private $foreignKey;
    private $parent;
    private $query;
    public function __construct(Builder $query, Model $parent, $foreignKey, $otherKey){
        $this->otherKey = $otherKey;
        $this->child = $query->getModel();
        $this->foreignKey = $foreignKey;
        $this->query = $query;
        $this->parent = $parent;
        parent::__construct($query, $parent);
    }
    public function addConditions(){
        $this->query->where($this->foreignKey, '=', $this->getParentIdValue())->limit(1);
    }
    public function getParentIdValue(){
        return $this->parent->getAttribute($this->otherKey);
    }

    public function save(Model $model){
        $model->setAttribute($this->getPlainForeignKey(), $this->getParentIdValue());

        return $model->save() ? $model : false;
    }

    public function getPlainForeignKey(){
        $foreign = explode(".", $this->foreignKey);
        return end($foreign);
    }
    public function saveMany($models){
        foreach ($models as $model) {
            $this->save($model);
        }

        return $models;
    }
}