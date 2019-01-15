<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/27
 * Time: 23:56
 */

namespace app\admin\validate;


class CouponsValidate extends BaseValidate
{

    protected $rule = [
        ['name','require|max:30|min:2','优惠券必须填写|优惠券不能超过30个字符|优惠券至少2个字符'],
        ['num','require|number','库存必须填写'],
        ['least_cost','require|number','限制条件必须填写'],
        ['reduce_cost','require|number','优惠额度必须填写'],
        ['end_date','require|date','结束日期必须填写|日期格式不对'],
        ['start_date','require|date','开始日期必须填写|日期格式不对']
    ];

}