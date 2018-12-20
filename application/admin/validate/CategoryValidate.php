<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/27
 * Time: 23:56
 */

namespace app\admin\validate;


class CategoryValidate extends BaseValidate
{

    protected $rule = [
        ['cate_name','require|max:30','分类名必须填写|分类名不能超过30个字符'],
        ['show_cate','number|in:0,1','是否展示分类必须是数字|是否展示分类范围不合法']
    ];

    protected $scene = [
        'save' => ['cate_name','show_cate']
    ];
}