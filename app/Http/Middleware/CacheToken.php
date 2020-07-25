<?php

namespace App\Http\Middleware;

use Closure;
//引入token表
use App\Model\P_token;

class CacheToken
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
        //判断是否为空
        if(empty($token)){
            //错误提示
            $arr=[
                'errno' => 40001,
                'msg'   => '未授权'
            ];
            //返回错误提示
            return response()->json($arr);
        }
        //不为空 在token表中查找
        $in_token=P_token::where(['token'=>$token])->first();
        //判断token是否有效
        if(empty($in_token)){
            //错误提示
            $response=[
                'errno'=>40002,
                'msg'=>'授权失败'
            ];
            //返回错误提示
            echo json_encode($response,JSON_UNESCAPED_UNICODE);die;
        }else if(time()>$in_token->expired_time){
            //错误提示
            $response=[
                'errno'=>40003,
                'msg'=>'token失效'
            ];
            //返回错误提示
            echo json_encode($response,JSON_UNESCAPED_UNICODE);die;
        }
        return $next($request);
    }
}
