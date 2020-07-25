<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class P_token extends Model
{
    //指定表名
    protected $table='p_token';
    //指定主键
    protected $primaryKey='id';
    //关闭时间戳
    public $timestamps=false;
}
