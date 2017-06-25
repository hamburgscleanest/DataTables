<?php

namespace hamburgscleanest\DataTables\Tests;

use hamburgscleanest\DataTables\Facades\DataTable;
use hamburgscleanest\DataTables\Models\Cache\SimpleCache;
use Illuminate\Support\Facades\Cache;

/**
 * Class SimpleCacheTest
 * @package hamburgscleanest\DataTables\Tests
 */
class SimpleCacheTest extends TestCase {

    /**
     * @test
     */
    public function can_cache_data()
    {
        // This key will be used for caching this explicit piece of data..
        $cacheKey = 'cc719d550b73b0162e54a8cd8f1ae1f4';

        $dataTable = DataTable::model(TestModel::class, ['id', 'created_at', 'name'], new SimpleCache());

        self::assertNull(Cache::get($cacheKey));

        $dataTable->render();

        self::assertNotNull(Cache::get($cacheKey));
    }
}