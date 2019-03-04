<?php
/**
 * Created by 七月
 * User: 七月
 * Date: 2017/2/18
 * Time: 12:35
 */
namespace app\admin\validate;

class Order extends BaseValidate
{
    protected $rule = [
        'status' => 'isInteger',
        'page' => 'isPositiveInteger',
        'size' => 'isPositiveInteger'
    ];
}
