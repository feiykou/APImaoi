<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/29
 * Time: 6:26
 */

namespace app\api\validate;


class Category extends BaseValidate
{
    protected $rule = [
        'recposId' => 'require|isPositiveInteger','推荐id必须添加|推荐id必须是正整数',
        'pid' => 'isPositiveInteger','pid必须是正整数',
    ];
}