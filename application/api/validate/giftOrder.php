<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-06-14
 * Time: 16:14
 */

namespace app\api\validate;


class giftOrder extends BaseValidate
{
    protected $rule = [
        'user_id' => 'isPositiveInteger',
        'order_id' => 'isPositiveInteger'
    ];

    protected $message = [
        'user_id' => '分页参数必须是正整数',
        'order_id' => '分页参数必须是正整数'
    ];
}