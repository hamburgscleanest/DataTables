<?php

namespace hamburgscleanest\DataTables\Models;

/**
 * Class Relation
 * @package hamburgscleanest\hamburgscleanest\DataTables\Models
 */
class Relation {

    /** @var string */
    public $name;

    /** @var string */
    public $aggregate = 'first';

    /** @var string */
    public $attributeName;

    /**
     * Relation constructor.
     * @param string $columnName
     * @internal param string $name
     */
    public function __construct(string $columnName)
    {
        $this->attributeName = $columnName;
        $this->name = $this->_extractAggregate($columnName);
    }

    /**
     * @param string $name
     * @return string
     */
    private function _extractAggregate(string $name) : string
    {
        $replaced = 0;
        $extractedName = preg_replace('/\((.*?)\)/', '#$1', $name, 1, $replaced);
        if ($replaced !== 0)
        {
            $parts = \explode('#', $extractedName);
            $this->aggregate = \mb_strtolower($parts[0]);

            $extractedName = $parts[1];
        }

        return \mb_substr($extractedName, 0, \mb_strpos($extractedName, '.'));
    }
}