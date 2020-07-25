<?php

namespace App\Http\Middleware;

use Closure;
//引入用户model
use App\Model\P_token;
//引入redis
use Illuminate\Support\Facades\Redis;

class CacheAccessCount
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //获取token
        $token=$request->token;
        //获取访问路径
        $path=$request->url();
        //查询对应的用户id
        $uid=P_token::where(['token'=>$token])->first();
        //hash 键值
        $key = 'h:view_count:'.$uid->user_id;
        //存入redis hash
        Redis::hincrby($key,$path,1);
        //获取redis hash
        $count=Redis::hgetall($key);
        //输出
        echo json_encode($count);
        return $next($request);
    }
}
