<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//引入guzzle类
use GuzzleHttp\Client;

class IndexController extends Controller
{
    /**
     * 获取access_token方法
     */
    public function getToken(){
        //appid
        $appid="wxb7f6d217acb5db86";
        //appsecret
        $appsec="7b31e98e4dee5bc700886582ba214c24";
        //获取access_token的接口
        $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsec;
        //获取
        $cont=file_get_contents($url);
        //输出
        echo $cont;
    }

    /**
     * 使用curl获取access_token
     */
    public function getCurlToken(){
        //appid
        $appid="wxb7f6d217acb5db86";
        //appsecret
        $appsec="7b31e98e4dee5bc700886582ba214c24";
        //获取access_token的接口
        $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsec;
        // 创建一个新cURL资源
        $ch = curl_init();
        // 设置URL和相应的选项
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        // 抓取URL并把它传递给浏览器
        $result=curl_exec($ch);
        // 关闭cURL资源，并且释放系统资源
        curl_close($ch);
        //打印 输出
        echo $result;
    }

    /**
     * 使用Guzzle获取access_token
     */
    public function getGuzzleToken(){
        //appid
        $appid="wxb7f6d217acb5db86";
        //appsecret
        $appsec="7b31e98e4dee5bc700886582ba214c24";
        //获取access_token的接口
        $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsec;
        //实例化guzzle
        $client = new Client();
        //发送请求
        $response = $client->request('GET',$url);
        //获取响应的主体部分
        $body = $response->getBody();
        //打印 输出
        echo $body;
    }
}