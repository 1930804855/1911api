<?php

namespace App\Http\Middleware;

use Closure;
//引入redis
use Illuminate\Support\Facades\Redis;

class CacheViewCount
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
        //有序集合 key键值
        $sorted_sets='viewCount';
        //获取接口路径
        $path=$request->url();
        //有序集合 字段
        $field=date('Y-m-d',time()).'|'.$path;
        //存入redis 有序集合
        Redis::zincrby($sorted_sets,1,$field);
        //获取
        $count=Redis::zrange($sorted_sets,0,-1,true);
        //输出
        echo json_encode($count);
        return $next($request);
    }
}
