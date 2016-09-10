<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

class Score extends Eloquent
{
    use SoftDeletes;

    protected $dates = ['create_at', 'update_at', 'deleted_at'];
    protected $guarded = ['id'];

    static public function add($user_id,$type)
    {
        $amount = Constant::getScoreAmount($type);
        self::Create(['type'=>$type,'user_id'=>$user_id,'amount'=>$amount]);
        return;
    }
}