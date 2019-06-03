<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CartModel;
use App\GoodsModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    //购物车页面
    public function index(){
        //echo __METHOD__;
        $cart_list=CartModel::where(['uid'=>Auth::id(),'session_id'=>Session::getId()])->get();
        if($cart_list){
            $cart_list=$cart_list->toArray();
            //echo '<pre>';print_r($cart_list);echo '<pre>';
            $goods_list=[];
            foreach($cart_list as $k=>$v){
                $goods_list[]=GoodsModel::where(['id'=>$v['goods_id']])->first()->toArray();
            }
            //echo '<pre>';print_r($goods_list);echo '<pre>';die;
            $total_price=0;
            foreach($goods_list as $k=>$v){
                $total_price+=$v['price'];
            }
            $data=[
                'goods_list'=>$goods_list,
                'total_price'=>$total_price
            ];
            return view('cart.index',$data);
        }else{
            die("购物车为空");
        }
    }
    //加入购物车
    public function add($goods_id=0){
        //echo 'goods_id:'.$goods_id;
        if(empty($goods_id)){
            header('Refresh:2;url=/cart/index');
            die("请选择商品，2秒后自动跳转至购物车");
        }
        //判断商品是否下架
        $goods=GoodsModel::where(['id'=>$goods_id])->first();
        if($goods){
            if($goods->is_del==1){
                header('Refresh:2;url=/cart/index');
                echo "商品已下架";die;
            }
            //echo '<pre>';print_r($goods->toArray());echo '<pre>';
            //入库
            $cartInfo=[
                'goods_id'=>$goods_id,
                'uid'=>Auth::id(),
                'add_time'=>time(),
                'session_id'=>Session::getId()
            ];
            $cart_id=CartModel::insertGetId($cartInfo);
            if($cart_id){
                header('refresh:2;url=/cart/index');
                echo "添加购物车成功";
            }else{
                echo "添加购物车失败";
            }
        }else{
            echo "商品不存在";
        }
    }

}
