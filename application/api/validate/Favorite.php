<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/9
 * Time: 12:17
 */

namespace app\api\validate;


class Favorite extends BaseValidate
{
    protected $rule = [
        'product_id' => 'require|isPositiveInteger',
    ];
}