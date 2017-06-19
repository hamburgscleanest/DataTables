<?php

namespace hamburgscleanest\DataTables\Exceptions;

/**
 * Class MultipleComponentAssertionException
 * @package hamburgscleanest\DataTables\Exceptions
 */
class MultipleComponentAssertionException extends \RuntimeException {

    protected $message = 'A component can not be asserted twice. Make sure to choose a unique component name';
}