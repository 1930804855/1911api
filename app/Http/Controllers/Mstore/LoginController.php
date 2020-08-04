<?php

namespace App\Http\Controllers\Mstore;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//引入用户表model
use App\Model\Mstore\P_user;

class LoginController extends Controller
{
    /**
     * 登录方法(接口)
     */
    public function loginDo(){
        //接值
        $username=$_POST['username'];
        $userpwd=$_POST['userpwd'];
        $sign=$_POST['sign'];
        //调用验证签名函数
        if(verify($username.$userpwd,$sign)==1){
            //验证成功  进行解密数据
            //解密 调用解密函数
            $username=decode($username);
            $userpwd=decode($userpwd);
            //根据用户名进行查询
            $user=P_user::where(['user_name'=>$username])->first();
            //判断用户是否存在
            if($user){
                //存在 验证密码
                if($userpwd==decrypt($user['user_pwd'])){
                    echo success('登录成功。');
                }else{
                    echo error(40002,'用户名或密码错误。');
                }
            }else{
                //不存在 调用错误提示
                echo error(40002,'用户名或密码错误。');
            }
        }else{
            //验证失败 调用错误函数
            echo error(40001,'验证失败。');
        }
    }

    /**
     * 注册接口
     */
    public function registerDo(){
        //接值
        $username=$_POST['username'];
        $useremail=$_POST['useremail'];
        $userpwd=$_POST['userpwd'];
        $sign=$_POST['sign'];

        //签名验证
        if(verify($username.$userpwd.$useremail,$sign)==1){
            //验证成功
            //解密用户名
            $username=decode($username);
            //根据用户名查询 判断唯一
            $user=P_user::where(['user_name'=>$username])->first();
            //判断用户是否存在
            if(empty($user)){
                //不存在 添加
                $user=new P_user;
                $user->user_name=$username;
                $user->user_email=decode($useremail);
                $user->user_pwd=decode($userpwd);
                $user->add_time=time();
                //添加
                if($user->save()){
                    //注册成功 调用成功函数
                    return success('注册成功。');
                }else{
                    //注册失败 调用失败函数
                    return error(40003,'注册失败。');
                }
            }else{
                //用户已存在 调用错误提示
                return error(40004,'此用户名已存在，请重新填写。');
            }
        }else{
            //验证失败 调用错误函数
            return error(40001,'验证失败。');
        }
    }
}
