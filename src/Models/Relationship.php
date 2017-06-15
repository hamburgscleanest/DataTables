<?php

namespace hamburgscleanest\DataTables\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Relationship
 * @package hamburgscleanest\hamburgscleanest\DataTables\Models
 */
class Relationship {

    /** @var string */
    public $name;
    /** @var string */
    private $_baseTable;
    /** @var string */
    private $_relatedTable;
    /** @var string */
    private $_baseKey;
    /** @var string */
    private $_foreignKey;

    /**
     * Relationship constructor.
     * @param string $name
     * @param Model $baseModel
     */
    public function __construct(string $name, Model $baseModel)
    {
        /** @var Model $related */
        $related = $baseModel->$name()->getRelated();

        $this->name = $name;
        $this->_baseTable = $baseModel->getTable();
        $this->_relatedTable = $related->getTable();
        $this->_baseKey = $baseModel->getKeyName();
        $this->_foreignKey = $related->getForeignKey();
    }

    /**
     * @param Model $baseModel
     * @param array $relations
     * @return array
     */
    public static function createFromArray(Model $baseModel, array $relations) : array
    {
        return \array_map(function($relation) use ($baseModel) {
            return new static($relation, $baseModel);
        }, $relations);
    }

    /**
     * Add the join for the relation to the query.
     *
     * @param Builder $queryBuilder
     * @return void
     */
    public function addJoin(Builder $queryBuilder) : void
    {
        $queryBuilder->join(
            $this->_relatedTable . ' AS ' . $this->name,
            $this->_baseTable . '.' . $this->_baseKey,
            '=',
            $this->name . '.' . $this->_foreignKey
        );
    }
}