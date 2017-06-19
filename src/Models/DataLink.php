<?php

namespace hamburgscleanest\DataTables\Models;

use Illuminate\Database\Eloquent\Model;

class DataLink {

    /** @var string */
    private $_url;

    /**
     * DataLink constructor.
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->_url = $url;
    }

    /**
     * Get the values for the needed parameters
     *
     * @param Model $rowModel
     * @return array
     */
    private function _getFieldValues(Model $rowModel) : array
    {
        $fieldValues = [];
        foreach ($rowModel->getAttributes() as $field => $value)
        {
            $fieldValues['{' . $field . '}'] = $value;
        }

        return $fieldValues;
    }

    /**
     * For example:
     *      $url = '/users/{id}';
     *      $parameters = ['id' => 1337];
     * --------------------------------
     *    = '/users/1337'
     *
     * @param Model $rowModel
     * @return string
     */
    public function generate(Model $rowModel) : string
    {
        return \strtr($this->_url, $this->_getFieldValues($rowModel));
    }
}