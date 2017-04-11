<?php

namespace hamburgscleanest\DataTables\Tests;

use hamburgscleanest\DataTables\Facades\SessionHelper;
use Illuminate\Http\Request;
use Mockery;

/**
 * Class SessionHelperTest
 * @package hamburgscleanest\DataTables\Tests
 */
class SessionHelperTest extends TestCase {

    private $_request;

    public function setUp()
    {
        parent::setUp();

        $this->_request = Mockery::mock(Request::class);
        $this->_request
            ->shouldReceive('session')->andReturn(session())
            ->shouldReceive('url')->andReturn('localhost');
    }

    /**
     * @test
     */
    public function unset_state_returns_null()
    {
        $this->assertNull(SessionHelper::getState($this->_request, 'test'));
    }

    /**
     * @test
     */
    public function saving_data_in_the_session_works()
    {
        SessionHelper::saveState($this->_request, 'test', true);
        $this->assertTrue(SessionHelper::getState($this->_request, 'test'));
    }

    /**
     * @test
     */
    public function removing_data_from_the_session_works()
    {
        SessionHelper::saveState($this->_request, 'test', true);
        SessionHelper::removeState($this->_request, 'test');
        $this->assertNull(SessionHelper::getState($this->_request, 'test'));
    }
}