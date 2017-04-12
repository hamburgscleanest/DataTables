<?php

namespace hamburgscleanest\DataTables\Tests;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TestModel
 * @package hamburgscleanest\DataTables\tests
 *
 * @property int id
 * @property string name
 * @property Carbon created_at
 */
class TestModel extends Model {

    public $timestamps = false;
    protected $table = 'testmodels';
    protected $guarded = [];
    protected $dates = ['created_at'];

    /**
     * @return string
     */
    public function getCustomColumnAttribute(): string
    {
        return 'custom-column';
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function testers()
    {
        return $this->hasMany(TestModel::class);
    }
}