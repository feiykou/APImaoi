<?php

use think\Route;


// Banner
Route::get('api/:version/banner/:id', 'api/:version.Banner/getBanner');

// Product
Route::get('api/:version/product/recoIndex', 'api/:version.Product/getRecoIndex');
Route::get('api/:version/product/:id/detail', 'api/:version.Product/getOne',[],['id'=>'\d+']);
Route::get('api/:version/product/singleProp', 'api/:version.Product/getSingleProp');
Route::get('api/:version/product/recoIndexByCate', 'api/:version.Product/getProductsByCateID');
Route::get('api/:version/product/cateProducts', 'api/:version.Product/getProductByCate');


// Token
Route::post('api/:version/token/user','api/:version.Token/getToken');
Route::post('api/:version/token/verify', 'api/:version.Token/verifyToken');

// User 获取用户信息
Route::post('api/:version/login','api/:version.User/updateUser');

// Address
Route::put('api/:version/address/:id/delete','api/:version.Address/delete',[],['id'=>'\d+']);
Route::post('api/:version/address','api/:version.Address/createOrUpdateAddress');
Route::put('api/:version/address','api/:version.Address/createOrUpdateAddress');
Route::get('api/:version/address','api/:version.Address/editAddress');
Route::get('api/:version/addressAll','api/:version.Address/getUserAddress');
Route::get('api/:version/getDefaultAddress','api/:version.Address/getDefaultAddress');

// Order
Route::post('api/:version/order','api/:version.Order/placeOrder');
Route::get('api/:version/order/:id','api/:version.Order/getDetail',[],['id'=>'\d+']);
Route::put('api/:version/order/delivery', 'api/:version.Order/delivery');
//不想把所有查询都写在一起，所以增加by_user，很好的REST与RESTFul的区别
Route::get('api/:version/order/by_user','api/:version.Order/getSummaryByUser');
Route::get('api/:version/order/paginate','api/:version.Order/getSummary');
Route::put('api/:version/order/cancel','api/:version.Order/cancel');
Route::put('api/:version/order/remove','api/:version.Order/remove');


// Pay
Route::post('api/:version/pay/pre_order','api/:version.Pay/getPreOrder');
Route::post('api/:version/pay/notify','api/:version.Pay/receiveNotify');
Route::post('api/:version/pay/re_notify', 'api/:version.Pay/redirectNotify');

// Message
//Route::post('api/:version/message/delivery','api/:version.Message/delivery');

Route::get('api/:version/cate/getProducts','api/:version.Category/getProductsByCate');


