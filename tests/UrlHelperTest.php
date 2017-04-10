<?php

namespace hamburgscleanest\DataTables\Tests;

use hamburgscleanest\DataTables\Facades\UrlHelper;
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
        $this->assertEquals(
            [
                'test'  => 'abc',
                'test2' => 'def'
            ],
            UrlHelper::parameterizeQuery('test=abc&test2=def')
        );
    }

    /**
     * @test
     */
    public function exception_is_thrown_for_malformed_query()
    {
        $this->expectException(RuntimeException::class);
        UrlHelper::parameterizeQuery('test');
    }
}