<?php
    /**
     * 验证签名
     * @param $sign 需要验证的签名
     */
    function verify($data,$sign){
        //base64 解密
        $sign=base64_decode($sign);
        //获取公钥
        $www_pub=file_get_contents(storage_path('key/www.pub.key'));
        //验证签名
        $verify=openssl_verify($data,$sign,$www_pub,OPENSSL_ALGO_SHA1);
        //返回结果
        return $verify;
    }

    /**
     * @param $data 需要解密的内容
     * @return mixed
     */
    function decode($data){
        //解密base64
        $data=base64_decode($data);
        //获取api私钥
        $api_priv=file_get_contents(storage_path('key/api.priv.key'));
        //私钥进行解密
        openssl_private_decrypt($data,$dec_str,$api_priv,OPENSSL_PKCS1_PADDING);
        //返回结果
        return $dec_str;
    }

    /**
     * @param $msg 提示语
     * @return string 返回类型 json类型
     */
    function success($msg){
        return json_encode([
            'code' => 0,
            'msg'  => $msg
        ],JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param $code 提示状态码
     * @param $msg  提示语
     * @return string 返回类型 json类型
     */
    function error($code,$msg){
        return json_encode([
            'code' => $code,
            'msg'  => $msg
        ],JSON_UNESCAPED_UNICODE);
    }