<?php
namespace DataFrame\Database\Elegant\Association;
use DataFrame\Database\Elegant\Model;
use DataFrame\Database\Query\Builder;
use DataFrame\Database\Elegant\Collection;
class Mergeable extends BelongsTo{


    protected $models;
    protected $mergeType;
   
    public function __construct(Builder $query, Model $parent, $foreignKey, $otherKey, $type, $relation){
        $this->mergeType = $type;
        parent::__construct($query, $parent, $foreignKey, $otherKey, $relation); 
    }

    
}