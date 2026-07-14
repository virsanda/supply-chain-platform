<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{
    protected $table = 'user_activity_logs';

    protected $fillable = ['user_id','action','subject','ip_address','user_agent','metadata'];
    protected $casts    = ['metadata'=>'array'];

    public function user() { return $this->belongsTo(User::class); }

    public static function record(?int $userId, string $action, string $subject=null, array $meta=[]): void
    {
        static::create(['user_id'=>$userId,'action'=>$action,'subject'=>$subject,'ip_address'=>request()->ip(),'user_agent'=>request()->userAgent(),'metadata'=>$meta]);
    }
}
