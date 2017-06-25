<?php

namespace hamburgscleanest\DataTables\Models\Cache;

use Illuminate\Support\Collection;

/**
 * Interface Cache
 * @package hamburgscleanest\DataTables\Models\Cache
 */
interface Cache {

    /**
     * @param \Closure $dataFunction
     * @return Collection
     */
    public function retrieve(\Closure $dataFunction) : Collection;
}