<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    protected $table = 'api_logs';

    protected $fillable = ['api_name','endpoint','method','parameters','response_code','success','error_message','response_time_ms','called_at'];
    protected $casts    = ['parameters'=>'array','success'=>'boolean','called_at'=>'datetime','response_time_ms'=>'integer','response_code'=>'integer'];

    public function scopeFailed($q)        { return $q->where('success',false); }
    public function scopeForApi($q,$name)  { return $q->where('api_name',$name); }

    public static function record(string $api, string $endpoint, bool $ok, int $code=200, int $ms=0, array $params=[], string $error=null): void
    {
        static::create(['api_name'=>$api,'endpoint'=>$endpoint,'method'=>'GET','parameters'=>$params,'response_code'=>$code,'success'=>$ok,'error_message'=>$error,'response_time_ms'=>$ms,'called_at'=>now()]);
    }
}
