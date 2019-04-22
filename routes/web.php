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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('cart/index','CartController@index');
Route::get('cart/add/{goods_id?}','CartController@add');
Route::get('order/add','OrderController@add');
Route::get('order/list','OrderController@order_list');
Route::get('order/paystatus','OrderController@paystatus');
Route::get('goods/list','GoodsController@list');
Route::get('goods/detail/{id}','GoodsController@detail');
//微信支付
Route::get('weixin/test/{oid}','WxPayController@test');
Route::get('weixin/paysuccess','WxPayController@paysuccess');
Route::post('pay/notice','WxPayController@notice');
