<?php

namespace hamburgscleanest\DataTables\Tests;

use hamburgscleanest\DataTables\Helpers\SessionHelper;

/**
 * Class SessionHelperTest
 * @package hamburgscleanest\DataTables\Tests
 */
class SessionHelperTest extends TestCase {

    /**
     * @test
     */
    public function unset_state_returns_null()
    {
        $sessionHelper = new SessionHelper();

        $this->assertNull($sessionHelper->getState('test'));
    }

    /**
     * @test
     */
    public function saving_data_in_the_session_works()
    {
        $sessionHelper = new SessionHelper();

        $sessionHelper->saveState('test', true);
        $this->assertTrue($sessionHelper->getState('test'));
    }

    /**
     * @test
     */
    public function removing_data_from_the_session_works()
    {
        $sessionHelper = new SessionHelper();

        $sessionHelper->saveState('test', true);
        $sessionHelper->removeState('test');
        $this->assertNull($sessionHelper->getState('test'));
    }
}