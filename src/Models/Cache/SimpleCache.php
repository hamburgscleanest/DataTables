<?php

namespace hamburgscleanest\DataTables\Models\Cache;

use Illuminate\Support\Collection;

/**
 * Class SimpleCache
 * @package hamburgscleanest\DataTables\Models\Cache
 */
class SimpleCache implements Cache {

    /** @var int */
    protected $_minutes;
    /** @var string */
    private $_key;

    /**
     * SimpleCache constructor.
     * @param int $minutes For how long should the data be cached?
     */
    public function __construct(int $minutes = 60)
    {
        $this->_minutes = $minutes;
        $this->_key = \md5(\sprintf('%s-%s', $minutes, \request()->url()));
    }

    /**
     * @param \Closure $dataFunction
     * @return Collection
     */
    public function retrieve(\Closure $dataFunction) : Collection
    {
        return \Illuminate\Support\Facades\Cache::remember($this->_key, $this->_minutes, function() use ($dataFunction) {
            return $dataFunction();
        });
    }
}