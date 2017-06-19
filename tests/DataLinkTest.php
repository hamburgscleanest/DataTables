<?php

namespace hamburgscleanest\DataTables\Tests;

use hamburgscleanest\DataTables\Models\DataLink;

/**
 * Class DataLinkTest
 * @package hamburgscleanest\DataTables\Tests
 */
class DataLinkTest extends TestCase {

    /**
     * @test
     */
    public function generates_correct_url_without_fields()
    {
        $rowModel = \factory(TestModel::class)->create();
        $url = '/tests/overview';
        $datalink = new DataLink($url);

        self::assertEquals('/tests/overview', $datalink->generate($rowModel));
    }

    /**
     * @test
     */
    public function generates_correct_url_with_fields()
    {
        $name = 'hello';
        $rowModel = \factory(TestModel::class)->create(['name' => $name]);
        $url = '/tests/{name}';
        $datalink = new DataLink($url, ['name']);

        self::assertEquals('/tests/' . $name, $datalink->generate($rowModel));
    }
}