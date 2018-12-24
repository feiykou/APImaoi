<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;
Route::get('/','admin/index/index');
Route::get('/app_login','admin/app_login/index');
Route::get('/login','admin/login/index');
Route::post('/submitLogin','admin/login/submitLogin');


//Route::get('/admin/category','admin/category/lst');
//Route::get('/admin/category/add','admin/category/add');
//Route::get('/admin/category/edit','admin/category/edit');
