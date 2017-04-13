<?php

namespace hamburgscleanest\DataTables\Tests;

use hamburgscleanest\DataTables\Helpers\SessionHelper;
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
        $sessionHelper = new SessionHelper($this->_request);

        $this->assertNull($sessionHelper->getState('test'));
    }

    /**
     * @test
     */
    public function saving_data_in_the_session_works()
    {
        $sessionHelper = new SessionHelper($this->_request);

        $sessionHelper->saveState('test', true);
        $this->assertTrue($sessionHelper->getState('test'));
    }

    /**
     * @test
     */
    public function removing_data_from_the_session_works()
    {
        $sessionHelper = new SessionHelper($this->_request);

        $sessionHelper->saveState('test', true);
        $sessionHelper->removeState('test');
        $this->assertNull($sessionHelper->getState('test'));
    }
}