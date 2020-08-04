<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

/**
 * phpinfo php信息
 */
Route::get('/phpinfo','IndexController@phpinfo');

/**
 * redis hash类型练习
 */
Route::get('hash','IndexController@hash');

/**
 * redis list列表练习
 */
Route::get('rlist','IndexController@rlist');

/**
 * 微信access_token 路由
 */
Route::get('/wx/token','IndexController@getToken');
/**
 * curl获取access_token路由
 */
Route::get('/wx/curltoken','IndexController@getCurlToken');
/**
 * 使用Guzzle获取access_token
 */
Route::get('/wx/gtoken','IndexController@getGuzzleToken');


/**
 * www项目调用本项目接口测试路由
 */
Route::get('user/info','IndexController@userinfo');

/**
 * api项目调用www项目测试
 */
Route::get('test','IndexController@test');

/**
 * 用户模块
 */
Route::prefix('user')->group(function(){
    //用户注册
    Route::post('reg','UserController@reg');
    //用户登录
    Route::post('login','UserController@login');
    //获取用户信息
    Route::get('center','UserController@center');
});


/**
 * 自习练习作业
 */
Route::prefix('test')->group(function(){
    //商品信息
    Route::get('goods_info','TestController@goods_info');
    //接口限制
    Route::get('sets','TestController@sets');
    //有序集合 签到
    Route::get('sorted_sets','TestController@sorted_sets');

    //www项目 解密路由
    Route::get('dec','TestController@dec');
    //www项目 非对称解密
    Route::get('pridec','TestController@pridec');
    //对向加密解密路由
    Route::get('decs','TestController@decs');
    //MD5()验签 加密
    Route::get('sign1','TestController@sign1');
    //验证签名
    Route::get('verify','TestController@verify');
    //数据加密+公钥验证签名
    Route::get('datasign','TestController@dataSign');
    //使用header传值 接值
    Route::get('header1','TestController@header1');
});

/**
 * mstore h5商城项目 api接口
 */
Route::prefix('mstore')->group(function(){
    //登录接口
    Route::post('loginDo','Mstore\LoginController@loginDO');
    //注册接口
    Route::post('registerDo','Mstore\LoginController@registerDo');
});
