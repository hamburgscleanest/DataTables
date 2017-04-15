<?php

namespace hamburgscleanest\DataTables\Models;

use hamburgscleanest\DataTables\Interfaces\ColumnFormatter;
use Illuminate\Database\Eloquent\Model;


/**
 * Class Header
 * @package hamburgscleanest\hamburgscleanest\DataTables\Models
 */
class Column {

    /** @var string */
    private $_name;

    /** @var Relation */
    private $_relation;

    /** @var ColumnFormatter */
    private $_formatter;

    /**
     * Column constructor.
     * @param string $name
     * @param ColumnFormatter|null $columnFormatter
     */
    public function __construct(string $name, ? ColumnFormatter $columnFormatter = null)
    {
        $this->setName($name);
        $this->_formatter = $columnFormatter;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->_name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $posDivider = \mb_strpos($name, '.');
        if ($posDivider === false)
        {
            $this->_name = $name;

            return;
        }

        $this->_relation = new Relation($name);
        $this->_name = \str_replace(')', '', \mb_substr($name, $posDivider + 1));
    }

    /**
     * @return null|Relation
     */
    public function getRelation(): ? Relation
    {
        return $this->_relation;
    }

    /**
     * @param ColumnFormatter $columnFormatter
     * @return Column
     */
    public function setFormatter(ColumnFormatter $columnFormatter): Column
    {
        $this->_formatter = $columnFormatter;

        return $this;
    }

    /**
     * Get the value from the column's relation
     *
     * @param Model $rowModel
     * @return string
     */
    private function _getValueFromRelation(Model $rowModel) : string
    {
        $relation = $model->getRelation($this->_relation->name);
        if ($relation instanceof Model)
        {
            return $relation->{$this->_name};
        }
        
        return $this->_relation->getValue($this->_name, $relation);
    }
    
    /**
     * @param Model $rowModel
     * @return string
     */
    private function _getValue(Model $rowModel) : string 
    {
        if(!property_exists($rowModel, $this->_name)) 
        {
            return '';
        }
        
        return (string) $rowModel->{$this->_name};
    }
    
    /**
     * Get the value of this column for the given row.
     *
     * @param Model $rowModel
     * @return string
     */
    public function getValue(Model $rowModel) : string
    {
        if($this->_relation !== null)
        {
            return $this->_getValueFromRelation($rowModel);
        }
        
        return $this->_getValue($rowModel);
    }
    
    /**
     * Get the formatted column value.
     *
     * @param Model $rowModel
     * @return string
     */
    public function getFormattedValue(Model $rowModel): string
    {
        $value = $this->getValue($rowModel);
        
        return $this->_formatter !== null ? $this->_formatter->format($value) : $value;
    }
}
