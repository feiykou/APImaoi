<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function attr($attr,$attr_name='暂无'){
    $str = '';
    switch ($attr){
        case 1:
            $str = "<span class='label label-success radius'>$attr_name</span>";
            break;
        case 2:
            $str = "<span class='label label-danger radius'>$attr_name</span>";
            break;
        default:
            $str = "<span class='label label-default radius'>$attr_name</span>";
    }
    return $str;
}

function orderStatus($status){
    $str = '';
    switch ($status){
        case 1:
            $str = "<span class='layui-btn layui-btn-small layui-btn-warm'>未支付</span>";
            break;
        case 2:
            $str = "<span class='layui-btn layui-btn-small'>已支付</span>";
            break;
        case 3:
            $str = "<span class='layui-btn layui-btn-small layui-btn-normal'>已发货</span>";
            break;
        case 4:
            $str = "<span class='layui-btn layui-btn-small layui-btn-danger'>库存不足</span>";
            break;
        case 6:
            $str = "<span class='layui-btn layui-btn-small layui-btn-danger'>已取消</span>";
            break;
        case 7:
            $str = "<span class='layui-btn layui-btn-small layui-btn-danger'>已签收</span>";
            break;
        case 8:
            $str = "<span class='layui-btn layui-btn-small layui-btn-danger'>已评价</span>";
            break;
        default:
            $str = '';
    }
    return $str;
}