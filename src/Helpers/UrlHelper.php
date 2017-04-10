<?php

namespace hamburgscleanest\DataTables\Helpers;

use RuntimeException;

/**
 * Class UrlHelper
 * @package hamburgscleanest\DataTables\Helpers
 */
class UrlHelper {

    /**
     * @param string|null $queryString
     *
     * @return array
     * @throws \RuntimeException
     */
    public function parameterizeQuery(? string $queryString = null)
    {
        if (empty($queryString))
        {
            return [];
        }

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