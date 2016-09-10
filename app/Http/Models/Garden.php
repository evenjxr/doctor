<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

class Garden extends Eloquent
{
    use SoftDeletes;

    protected $dates = ['create_at', 'update_at', 'deleted_at'];
    protected $guarded = ['id'];

    const RAND_MAX = 12;
    const RAND_MIN = 8;
    const GROW_CYCLE = 3;

    public static function addGrade($user_id,$flower_num)
    {
        $growup_to_time = self::GROW_CYCLE*3600+time();
        $fruit = rand(self::RAND_MIN,self::RAND_MAX);
        return self::create(['user_id'=>$user_id,'flower_num'=>$flower_num,'growup_to_time'=>$growup_to_time,'fruit'=>$fruit]);
    }



}