<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

class SearchHistory extends Eloquent
{
    use SoftDeletes;

    protected $dates = ['create_at', 'update_at', 'deleted_at'];
    protected $guarded = ['id'];
}