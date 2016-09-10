<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

class Constant extends Eloquent
{
    use SoftDeletes;

    protected $dates = ['create_at', 'update_at', 'deleted_at'];
    protected $guarded = ['id'];

    static public function getScoreAmount($type='')
    {
        if ($type){
            return self::where('table','scores')->where('type',$type)->first()['amount'];
        } else {
            return self::where('table','scores')->pluck('amount','type');
        }
    }

}