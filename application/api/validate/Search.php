<?php
/**
 * Created by 七月
 * User: 七月
 * Date: 2017/2/18
 * Time: 12:35
 */
namespace app\api\validate;

class Search extends BaseValidate
{
    protected $rule = [
        'q' => 'require','搜索内容必须添加',
        'page' => 'isPositiveInteger',
        'size' => 'isPositiveInteger',
        'cateid' => 'isInteger',
    ];
}
