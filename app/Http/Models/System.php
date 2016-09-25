<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

class System extends Eloquent
{
    use SoftDeletes;

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $guarded = ['id'];
}