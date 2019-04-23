<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GoodsModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class GoodsController extends Controller
{
    public function list(){
        $data=GoodsModel::all()->toArray();
        //dump($data);
        $data=[
            'data'=>$data
        ];
        return view('goods.list',$data);
    }
    //商品详情页
    public function detail($id){
        $goods_id=$id;
        if($goods_id){
            $data=GoodsModel::where(['id'=>$goods_id])->first();
            if(!$data){
                die('商品不存在');
            }
            $key='see_num'.$goods_id;
            //redis自增
            Redis::incr($key);
            $see_num=Redis::get($key);
            $sort_key='ss:goods';
            //redis储存有序集合
            Redis::zAdd($sort_key,$see_num,$goods_id);
            //浏览历史
            $h_key='ss:history'.Auth::id();
            Redis::zAdd($h_key,time(),$goods_id);
            //获取商品排序信息
            $goods=$this->getSeeSort();
            //获取浏览记录
            $history=$this->history();
            $data=[
                'data'=>$data,
                'see_num'=>$see_num,
                'goods'=>$goods,
                'history'=>$history
            ];
        }else{
            die('参数错误');
        }
        return view('goods.detail',$data);
    }
    //获取浏览量排行
    public function getSort(){
        $sort_key='ss:goods';
        $list1=Redis::zRangeByScore($sort_key,0,10000,['withscores'=>true]);     //正序
        echo '<pre>';print_r($list1);echo '<hr>';
        $list2=Redis::zRevRange($sort_key,0,10000,true);     //倒序
        echo '<pre>';print_r($list2);echo '<hr>';
    }
    //获取商品浏览量排行
    public function getSeeSort(){
        $sort_key='ss:goods';
        $arr=Redis::zRevRange($sort_key,0,10000,true);
        $goods_id=array_keys($arr);
        //print_r($goods_id);
        $data=[];
        foreach($goods_id as $k=>$v){
            $goods=GoodsModel::where(['id'=>$v
            ])->first()->toArray();
            $data[]=$goods;
        }
        //print_r($data);
        return $data;
    }
    //获取浏览记录
    public function history(){
        $h_key='ss:history'.Auth::id();
        $arr=Redis::zRevRange($h_key,0,1999999999,true);
        $goods_id=array_keys($arr);
        //print_r($goods_id);
        $data=[];
        foreach($goods_id as $k=>$v){
            $goods=GoodsModel::where(['id'=>$v])->first()->toArray();
            $data[]=$goods;
        }
        //print_r($data);
        return $data;
    }
}
