<?php

namespace hamburgscleanest\DataTables\Exceptions;

/**
 * Class ColumnNotFoundException
 * @package hamburgscleanest\DataTables\Exceptions
 */
class ColumnNotFoundException extends \RuntimeException {

    /**
     * ColumnNotFoundException constructor.
     * @param string $columnName
     */
    public function __construct(string $columnName)
    {
        $message = $columnName . ': Column not found.';
        parent::__construct($message);
    }
}