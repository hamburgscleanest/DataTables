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

    protected $table = 'testmodels';

    protected $guarded = [];

    protected $dates = ['created_at'];

    public $timestamps = false;
}