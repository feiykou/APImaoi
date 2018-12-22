<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// [ 应用入口文件 ]

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');

// 图片路径
define('IMG_URL', '/upload/images/');
// 视频路径
define('VIDEO_URL', '/upload/videos/');

// 删除文件路径
define('DEL_FILE_URL', __DIR__);

// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';

\think\Log::init([
    'type'  =>  'File',
    'path'  =>  LOG_PATH,
    'level' => ['sql']
]);
