<?php

namespace hamburgscleanest\DataTables\Helpers;

use Illuminate\Http\Request;
use RuntimeException;

/**
 * Class UrlHelper
 * @package hamburgscleanest\DataTables\Helpers
 */
class UrlHelper {

    /** @var Request */
    private $_request;

    /**
     * UrlHelper constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->_request = $request;
    }

    /**
     * Array of all the query parameters for the current request.
     *
     * @return array
     * @throws \RuntimeException
     */
    public function queryParameters() : array
    {
        $queryString = $this->_request->getQueryString();

        return empty($queryString) ? [] : $this->_extractQueryParameters($queryString);
    }

    /**
     * @param string $queryString
     * @return array
     * @throws \RuntimeException
     */
    private function _extractQueryParameters(string $queryString) : array
    {
        $parameters = [];
        foreach (\explode('&', $queryString) as $query)
        {
            $queryParts = \explode('=', $query);
            if (\count($queryParts) !== 2)
            {
                throw new RuntimeException('Malformed query parameters.');
            }

            $parameters[$queryParts[0]] = $queryParts[1];
        }

        return $parameters;
    }
}