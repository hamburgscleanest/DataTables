<?php

namespace hamburgscleanest\DataTables\Models\Column;

/**
 * Class Relation
 * @package hamburgscleanest\hamburgscleanest\DataTables\Models\Column
 */
class ColumnRelation {

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
        $extractedName = \preg_replace('/\((.*?)\)/', '#$1', $name, 1, $replaced);
        if ($replaced === 0)
        {
            return \str_replace('.', '_', \mb_strtolower($extractedName));
        }

        $parts = \explode('#', $extractedName);
        $this->aggregate = \mb_strtolower($parts[0]);

        return \str_replace('.', '_', \mb_strtolower($parts[1]));
    }
}