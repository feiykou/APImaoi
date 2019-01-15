<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/11
 * Time: 18:04
 */

namespace app\api\validate;


class Common extends BaseValidate
{
    protected $rule = [
        'page' => 'isPositiveInteger','分页参数必须是正整数',
        'size' => 'isPositiveInteger','分页参数必须是正整数',
        'id' => 'require|isPositiveInteger', 'id必须添加|id必须是正整数'
    ];

    protected $scene = [
        'pageId' => ['page','size','id']
    ];

}