<?php

namespace hamburgscleanest\DataTables\Tests;

use hamburgscleanest\DataTables\Helpers\SessionHelper;
use Illuminate\Support\Facades\Request;

/**
 * Class SessionHelperTest
 * @package hamburgscleanest\DataTables\Tests
 */
class SessionHelperTest extends TestCase {

    public function setUp()
    {
        parent::setUp();

        Request::setLaravelSession($this->app['session.store']);
    }

    /**
     * @test
     */
    public function unset_state_returns_null()
    {
        $sessionHelper = new SessionHelper();

        static::assertNull($sessionHelper->getState('test'));
    }

    /**
     * @test
     */
    public function saving_data_in_the_session_works()
    {
        $sessionHelper = new SessionHelper();

        $sessionHelper->saveState('test', true);
        static::assertTrue($sessionHelper->getState('test'));
    }

    /**
     * @test
     */
    public function removing_data_from_the_session_works()
    {
        $sessionHelper = new SessionHelper();

        $sessionHelper->saveState('test', true);
        $sessionHelper->removeState('test');
        static::assertNull($sessionHelper->getState('test'));
    }
}