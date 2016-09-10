<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;



class LoginToken extends Eloquent
{
    use SoftDeletes;

    protected $dates = ['create_at', 'update_at', 'deleted_at'];
    protected $guarded = ['id'];

    const SMS_TYPE_LOGIN = 'sms_login';
    const SMS_TYPE_LOGIN_RESEND = 'sms-login-resend';
    const SMS_AUTH_ID = 'auth_mobile';



    public static function makeToken()
    {
        return Uuid::uuid4()->toString();
    }

    public static function saveToken($user,$token)
    {
        $res = self::where('user_id',$user->id)->first();
        if ($res) $res->delete();
        return self::create(['token'=>$token,'user_id'=>$user->id]);
    }
}