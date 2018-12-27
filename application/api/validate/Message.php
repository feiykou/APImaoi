<?php
/**
 * Created by 七月.
 * Author: 七月
 * 微信公号：小楼昨夜又秋风
 * 知乎ID: 七月在夏天
 * Date: 2017/2/23
 * Time: 3:01
 */

namespace app\api\validate;


class Message extends BaseValidate
{
    // 为防止欺骗重写user_id外键
    // rule中严禁使用user_id
    // 获取post参数时过滤掉user_id
    // 所有数据库和user关联的外键统一使用user_id，而不要使用uid
    protected $rule = [
        'id' => 'require|isPositiveInteger'
//        'tplJumpPage' => 'require|isNotEmpty',
//        'expressNum' => 'require|isNotEmpty'
    ];

    protected $message  =   [
        'order.require'           => '订单必须填写',
        'order.isNotEmpty'        => '订单不能为空',
        'tplJumpPage.require'     => '跳转必须填写',
        'tplJumpPage.isNotEmpty'  => '跳转不能为空',
        'expressNum.require'      => '快递单号必须填写',
        'expressNum.isNotEmpty'   => '快递单号不能为空'
    ];

    protected $scene = [
        'delivery' => ['order','tplJumpPage','expressNum']
    ];
}