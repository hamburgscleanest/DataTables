<?php

namespace hamburgscleanest\DataTables\Models;

use Illuminate\Support\Collection;

class DataTable {

    private $_data;

    /**
     * Initialize a table with the given data.
     * @param Collection $data
     *
     * @return $this
     */
    public function data(Collection $data)
    {
        $this->_data = $data;

        return $this;
    }
}