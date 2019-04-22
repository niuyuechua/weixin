<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OrderModel extends Model
{
    protected $table = 'order';
    public $timestamps = 'false';
    protected $primaryKey='oid';

    //生成订单编号
    public static function orderSn($uid){
        $order_sn='1809a_'.date("ymdH").'_';
        $str=time().$uid.rand(1000,9999).Str::random(16);
        $order_sn.=substr(md5($str),5,16);
        return $order_sn;
    }
}
