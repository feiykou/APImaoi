<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/29
 * Time: 6:26
 */

namespace app\api\validate;


class WxCode extends BaseValidate
{
    protected $rule = [
        'path' => 'require','path必须填写',
        'width' => 'isPositiveInteger','width必须是正整数',
        'auto_color' => 'boolean',
        'line_color' => 'array',
        'is_hyaline' => 'boolean',
        'product_id' => 'require|isPositiveInteger','product_id必须填写|product_id必须是正整数'
    ];
}