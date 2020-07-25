<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//引入用户表model
use App\Model\P_users;
//引入随机字符串类
use Illuminate\Support\Str;
//引入token model
use App\Model\P_token;


class UserController extends Controller
{
    /**
     * 用户注册方法
     */
    public function reg(){
        //接值
        $user_name=request()->post('user_name');
        $user_email=request()->post('user_email');
        $password1=request()->post('password1');
        $password2=request()->post('password2');
        //密码加密
        $password1=password_hash($password1,PASSWORD_BCRYPT);
        //拼接数据数组
        $user_info=[
            'user_name'=>$user_name,
            'user_email'=>$user_email,
            'password'=>$password1,
            'reg_time'=>time()
        ];
        //添加入库 并判断
        $user_id=P_users::insertGetId($user_info);
        //判断
        if($user_id){
            return [
                'errno'=>0,
                'msg'=>"OK"
            ];
        }else{
            return [
                'errno'=>40000,
                'msg'=>"NO"
            ];
        }
    }

    /**
     * 用户登录方法
     */
    public function login(){
        //接值
        $user_name=request()->post('user_name');
        $password=request()->post('password');
        //查询信息
        $u=P_users::where(['user_name'=>$user_name])->first();
        //判断是否正确
        if($u){
            //用户存在 验证密码
            if(password_verify($password,$u->password)){
                //生成token 随机字符串
                $token=Str::random(32);
                //当前时间
                $time=time();
                //拼接数据
                $tokenInfo=[
                    'token'=>$token,
                    'user_id'=>$u->user_id,
                    'expired_time'=>$time+3600
                ];
                //将token存入数据库
                if(P_token::insert($tokenInfo)){
                    //成功返回结果
                    return json_encode([
                        'errno'=>0,
                        'msg'=>'登录成功',
                        'data'=>[
                            'token'=>$token,
                            'expired_time'=>3600
                        ]
                    ],JSON_UNESCAPED_UNICODE);
                }
            }else{
                return json_encode([
                    'errno'=>40002,
                    'msg'=>'用户或密码错误，请重新输入。'
                ],JSON_UNESCAPED_UNICODE);
            }
        }else{
            //用户不存在
            return json_encode([
                'errno'=>40001,
                'msg'=>'用户或密码错误，请重新输入。'
            ],JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 获取用户信息方法
     */
    public function center(){
        //获取token
        $token=request()->get('token');
        //判断是否有token
        if(empty($token)){
            return json_encode([
                'errno'=>40003,
                'msg'=>'token不可为空'
            ],JSON_UNESCAPED_UNICODE);
        }
        //根据token查询当前数据
        $token=P_token::where(['token'=>$token])->first();
        //判断token是否存在
        if(!$token){
            return json_encode([
                'errno'=>40004,
                'msg'=>'token不存在'
            ],JSON_UNESCAPED_UNICODE);
        }
        //判断时间  是否过期
        if(time()<$token->expired_time){
            //根据token查询用户信息
            $userInfo=P_users::select('user_id','user_name','user_email','reg_time')->find($token->user_id);
            //返回用户信息
            return json_encode([
                'errno'=>0,
                'msg'=>'OK',
                'data'=>$userInfo->Toarray()
            ],JSON_UNESCAPED_UNICODE);
        }else{
            //token过期
            return json_encode([
                'errno'=>40005,
                'msg'=>'token已过期',
            ],JSON_UNESCAPED_UNICODE);
        }
    }
}
