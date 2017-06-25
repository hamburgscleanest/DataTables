<?php

namespace hamburgscleanest\DataTables\Models\Cache;

use Illuminate\Support\Collection;

/**
 * Class NoCache
 * @package hamburgscleanest\DataTables\Models\Cache
 */
class NoCache {

    /**
     * @param \Closure $dataFunction
     * @return Collection
     */
    public function retrieve(\Closure $dataFunction) : Collection
    {
        return $dataFunction();
    }
}