<?php

namespace app\admin\validate;

class LoginValidate extends BaseValidate
{
    protected $rule = [
        'ac' => 'require|isNotEmpty',
        'se' => 'require|isNotEmpty'
    ];
}
