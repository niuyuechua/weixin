<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GoodsModel;
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
    public function detail($id){
        $goods_id=$id;
        $key='see_num';
        if($goods_id){
            Redis::incr($key);
            $data=GoodsModel::where(['id'=>$goods_id])->first();
            $see_num=Redis::get($key);
            $data=[
                'data'=>$data,
                'see_num'=>$see_num
            ];
        }else{
            die('无数据');
        }
        return view('goods.detail',$data);
    }
}
