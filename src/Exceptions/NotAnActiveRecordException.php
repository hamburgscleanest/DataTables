<?php

namespace hamburgscleanest\DataTables\Exceptions;

/**
 * Class NotAnActiveRecordException
 * @package hamburgscleanest\DataTables\Exceptions
 */
class NotAnActiveRecordException extends \RuntimeException {

    /**
     * ColumnNotFoundException constructor.
     * @param string $modelName
     */
    public function __construct(string $modelName)
    {
        $message = $modelName . ': Class is not an active record.';
        parent::__construct($message);
    }
}