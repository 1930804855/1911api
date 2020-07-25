<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class P_goods extends Model
{
    //指定表名
    protected $table='p_goods';
    //指定主键
    protected $primaryKey='goods_id';
    //关闭时间戳
    public $timestamps=false;
}
