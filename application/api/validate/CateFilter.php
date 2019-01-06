<?php
/**
 * Created by 七月
 * User: 七月
 * Date: 2017/2/18
 * Time: 12:35
 */
namespace app\api\validate;

class CateFilter extends BaseValidate
{
    protected $rule = [
        'cateid' => 'require|isInteger',
        'price' => 'require|isString',
        'page' => 'isPositiveInteger',
        'size' => 'isPositiveInteger'
    ];
}
