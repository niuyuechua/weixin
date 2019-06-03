<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JssdkController extends Controller
{
    //获取权限验证配置信息
    public function getConfig(){
        //获取生成签名的参数
        $ticket=getTicket();
        //echo $ticket;
        $nonceStr=Str::random(10);
        $timestamp=time();
        $current_url=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        //echo $current_url;die;
        //生成签名
        $string1="jsapi_ticket=$ticket&noncestr=$nonceStr&timestamp=$timestamp&url=$current_url";
        //echo $string1;die;
        $signature=sha1($string1);
        //echo $signature;

        $js_config=[
            'appId'=>env('WX_APP_ID'),  //公众号APPID
            'timestamp'=>$timestamp,    //时间戳
            'nonceStr'=>$nonceStr,     //随机字符串
            'signature'=>$signature,    //签名
        ];
        $data=[
            'js_config'=>$js_config
        ];
        return view('jssdk.img',$data);
    }

    public function getImg(){
        echo '<pre>';print_r($_GET);echo '</pre>';
    }
}
