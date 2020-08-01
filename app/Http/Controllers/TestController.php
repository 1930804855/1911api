<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//引入商品model
use App\Model\P_goods;
//引入redis
use Illuminate\Support\Facades\Redis;

class TestController extends Controller
{
    /**
     * 商品详情页面 练习
     */
    public function goods_info(){
        //获取商品id
        $id=request()->id;
        //商品信息键
        $goods_key='goods_info_'.$id;
        //取出缓存商品信息
        $goods_info=Redis::hgetall($goods_key);
        //判断是否为空
        if(empty($goods_info)){
            //查询单条数据
            $goods_info=P_goods::select('goods_id','goods_sn','goods_name','goods_number','shop_price')->find($id)->toarray();
            //将商品信息存入redis
            Redis::hmset($goods_key,$goods_info);
            //给redis键添加过期时间
            Redis::expire($goods_key,10);
        }
        //访问量的键值
        $views_key='views_'.$id;
        //访问量
        $views=Redis::incr($views_key,1);
        echo "访问量：".$views;
        //打印输出
        dd($goods_info);
    }

    /**
     * 接口调用限制
     */
    public function sets(){
        //获取token
        $token=request()->token;
        //判断是否有token
        if(empty($token)) {
            die('缺少token，请填写。');
        }
        //key键值
        $sets_key='sets_'.$token;
        //检测集合中是否存在
        if(Redis::sismember($sets_key,$token)){
            die('您的调用次数已上限。');
        }
        //自增值
        $count=Redis::incr($sets_key,1);
        //判断值大于10 添加到集合中
        if($count>10){
            //添加到集合中
            Redis::sadd($sets_key,$token);
            die('您的调用次数已上限。');
        }else{
            echo $count."<br>";
            echo "欢迎来到 PHP。";
        }
    }

    /**
     * 有序集合 签到
     */
    public function sorted_sets(){
        //获取用户id
        $uid=request()->uid;
        //获取时间戳
        $time=time();
        //有序集合 key键值
        $key='store_sets';
        //存入有序集合中
        Redis::zadd($key,$time,$uid);
        //获取
        $info=Redis::zrange($key,0,-1,true);
        //$info=Redis::zrangeByScore($key,0,-1,['withscores'=>TRUE]);
        dd($info);
    }

    /**
     * 进行解密www项目
     */
    public function dec(){
        //获取要解密的数据
        $enc_str=request()->data;
        //使用base64解密第一层
        $enc_str=base64_decode($enc_str);
        //openssl解密第二层
        //请求方式
        $method='AES-256-CBC';
        //请求key 密钥
        $key='1911api';
        //加密补全选项
        $options=OPENSSL_RAW_DATA;
        //初始化
        $iv='aaaabbbbccccdddd';
        //解密
        $dec_str=openssl_decrypt($enc_str,$method,$key,$options,$iv);
        //返回
        return $dec_str;
    }

    /**
     * 进行非对称解密www项目
     */
    public function pridec(){
        //获取要进行解密的数据
        $enc_str=request()->data;
        //base64进行解密
        $enc_str=base64_decode($enc_str);
        //获取私钥
        $prikey=file_get_contents(storage_path('key/api.priv.key'));
        //非对称解密
        openssl_private_decrypt($enc_str,$dec_str,$prikey,OPENSSL_PKCS1_PADDING);
        echo $dec_str;
    }

    /**
     * 对向解密方法
     */
    public function decs(){
        //获取要解密的数据
        $enc_str=request()->data;
        //使用base64解密第一层
        $enc_str=base64_decode($enc_str);
        //获取api解密私钥
        $apipriv=file_get_contents(storage_path('key/api.priv.key'));
        //使用私钥进行解密第二层
        openssl_private_decrypt($enc_str,$dec_str,$apipriv,OPENSSL_PKCS1_PADDING);

        //给www解密的待加密数据
        $data='api项目发送给www项目 www解密';
        //获取www公钥
        $wpub=file_get_contents(storage_path('key/www.pub.key'));
        //使用www公钥进行加密
        openssl_public_encrypt($data,$enc_str,$wpub,OPENSSL_PKCS1_PADDING);
        //使用base64加密第二层
        $enc_str=base64_encode($enc_str);
        //输出 返回
        $response=[
            'www'=>'api解密结果：'.$dec_str,
            'api'=>$enc_str
        ];
        echo json_encode($response,JSON_UNESCAPED_UNICODE);
    }

    /**
     * MD5()验签加密
     */
    public function sign1(){
        //公共key
        $key='api1911';
        //接值
        $data=request()->get('data');
        $sign=request()->get('sign');
        //生成签名
        $sign1=md5($data.$key);
        //判断 验签
        if($sign==$sign1){
            echo "验签成功。";
        }else{
            echo "验签失败。";
        }
    }

    /**
     * 验证签名
     */
    public function verify(){
        //接收数据
        $data=request()->get('data');
        $sign=request()->get('sign');
        //使用base64解密签名
        $sign=base64_decode($sign);
        //获取公钥
        $pub_key=file_get_contents(storage_path('key/www.pub.key'));
        //验证签名
        $d=openssl_verify($data,$sign,$pub_key,OPENSSL_ALGO_SHA1);
        echo $d;
    }

    /**
     * 数据加密+公钥验证签名
     */
    public function dataSign(){
        //接值
        $dec_str=request()->get('data');
        $sign=request()->get('sign');

        //base64解密 第二层
        $dec_str=base64_decode($dec_str);
        $sign=base64_decode($sign);

        //解密数据
        //加密算法
        $method='AES-256-CBC';
        //key 键
        $key='1911api';
        //加密补全选项
        $options=OPENSSL_RAW_DATA;
        //初始化向量
        $iv='aaaabbbbccccdddd';
        $dec_str=openssl_decrypt($dec_str,$method,$key,$options,$iv);

        //获取公钥 验证签名
        $pub_key=file_get_contents(storage_path('key/www.pub.key'));
        //验证签名
        $d=openssl_verify($dec_str,$sign,$pub_key,OPENSSL_ALGO_SHA1);
        //判断签名
        if($d==1){
            echo "验签成功，数据为：".$dec_str;
        }else{
            echo "验签失败。";
        }
    }

    /**
     * 使用header 传值 接值
     */
    public function header1(){
        //接值
        $server=$_SERVER;
        echo $server['HTTP_UID'].'<br>';
        echo $server['HTTP_TOKEN'];
    }
}
