<?php

namespace hamburgscleanest\DataTables\Tests;

use hamburgscleanest\DataTables\Models\Column;
use hamburgscleanest\DataTables\Models\Header;

/**
 * Class HeaderTest
 * @package hamburgscleanest\DataTables\Tests
 */
class HeaderTest extends TestCase {

    /**
     * @test
     */
    public function attribute_name_is_set()
    {
        $name = 'testcolumn';
        $header = new Header(new Column($name));

        $this->assertEquals($name, $header->name);
        $this->assertEquals($name, $header->getAttributeName());
    }
}