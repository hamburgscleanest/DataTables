<?php

namespace hamburgscleanest\DataTables\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class Translator
 * @package hamburgscleanest\DataTables\Models
 */
class Translator extends DataComponent {

    /** @var array */
    private $_translations = [];

    public function __construct(string $translationFile)
    {
        $this->_translations = \trans($translationFile);
    }

    /**
     * @return Builder
     */
    public function shapeData(): Builder
    {
        // TODO: translate

        return $this->_queryBuilder;
    }

    /**
     * @return string
     */
    public function render(): string
    {
        return '';
    }
}