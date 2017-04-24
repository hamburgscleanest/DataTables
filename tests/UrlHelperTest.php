<?php

namespace hamburgscleanest\DataTables\Tests;

use hamburgscleanest\DataTables\Helpers\UrlHelper;
use RuntimeException;

/**
 * Class UrlHelperTest
 * @package hamburgscleanest\DataTables\Tests
 */
class UrlHelperTest extends TestCase {

    /**
     * @test
     */
    public function query_parameters_are_set_correctly()
    {
        \request()->server->set('QUERY_STRING', 'test=abc&test2=def');

        $urlHelper = new UrlHelper();
        $this->assertEquals(
            [
                'test'  => 'abc',
                'test2' => 'def'
            ],
            $urlHelper->queryParameters()
        );
    }

    /**
     * @test
     */
    public function exception_is_thrown_for_malformed_query()
    {
        $this->expectException(RuntimeException::class);

        \request()->server->set('QUERY_STRING', 'test');

        $urlHelper = new UrlHelper();
        $urlHelper->queryParameters();
    }
}