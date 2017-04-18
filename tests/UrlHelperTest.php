<?php

namespace hamburgscleanest\DataTables\Tests;

use hamburgscleanest\DataTables\Helpers\UrlHelper;
use Illuminate\Http\Request;
use Mockery;
use RuntimeException;

/**
 * Class UrlHelperTest
 * @package hamburgscleanest\DataTables\Tests
 */
class UrlHelperTest extends TestCase {

    private $_request;

    public function setUp()
    {
        parent::setUp();

        $this->_request = Mockery::mock(Request::class);
    }

    /**
     * @test
     */
    public function query_parameters_are_set_correctly()
    {
        $this->_request->shouldReceive('getQueryString')->andReturn('test=abc&test2=def');

        $urlHelper = new UrlHelper($this->_request);
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

        $this->_request->shouldReceive('getQueryString')->andReturn('test');

        $urlHelper = new UrlHelper($this->_request);
        $urlHelper->queryParameters();
    }
}