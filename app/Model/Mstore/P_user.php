<?php

namespace App\Model\Mstore;

use Illuminate\Database\Eloquent\Model;

class P_user extends Model
{
    //指定表名
    protected $table='p_user';
    //指定主键
    protected $primaryKey='user_id';
    //关闭时间戳
    public $timestamps=false;
}
