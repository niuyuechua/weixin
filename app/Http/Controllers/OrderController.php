<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CartModel;
use App\GoodsModel;
use App\OrderModel;
use App\OrderDetailModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    //提交订单
    public function add(){
        //计算订单总金额
        $cart=CartModel::where(['uid'=>Auth::id(),'session_id'=>Session::getId()])->get()->toArray();
        //print_r($cart);die;
        $order_amount=0;
        foreach($cart as $k=>$v){
            $price=GoodsModel::where(['id'=>$v['goods_id']])->first()->price;
            $order_amount+=$price;
        }
        //echo $order_amount;
        $order_info=[
            'uid'=>Auth::id(),
            'order_sn'=>OrderModel::orderSn(Auth::id()),
            'order_amount'=>$order_amount,
            'add_time'=>time()
        ];
        //写入订单表
        $oid=OrderModel::insertGetId($order_info);

        //订单详情
        foreach($cart as $k=>$v){
            $goods=GoodsModel::where(['id'=>$v['goods_id']])->first();
            $detail=[
                'oid'=>$oid,
                'goods_id'=>$v['goods_id'],
                'goods_name'=>$goods->name,
                'goods_price'=>$goods->price,
                'uid'=>Auth::id()
            ];
            //写入订单详情表
            OrderDetailModel::insertGetId($detail);
        }
        header('Refresh:2;url=/order/list');
        echo "订单生成成功";
    }
    //订单列表
    public function order_list(){
        $order_list=OrderModel::where(['uid'=>Auth::id()])->orderBy("oid","desc")->get()->toArray();
        $data=[
            'order_list'=>$order_list
        ];
        return view('order.list',$data);
    }
    //查询订单支付状态
    public function paystatus(){
        $oid=intval($_GET['oid']);
        //echo $oid;die;
        $info=OrderModel::where(['oid'=>$oid])->first();
        $response=[];
        if($info){
            if($info->pay_time>0){      //已支付
                $response=[
                    'status'=>1,    //1 已支付
                    'msg'=>'ok'
                ];
            }
        }else{
            die('订单不存在');
        }
        die(json_encode($response));
    }
}
