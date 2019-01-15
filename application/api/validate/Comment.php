<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/10
 * Time: 22:13
 */

namespace app\api\validate;


use app\lib\exception\ParameterException;

class Comment extends BaseValidate
{

    protected $rule = [
        'comments' => 'require|checkComments'
    ];


    protected function checkComments($values){
        if(empty($values)){
            throw new ParameterException([
                'msg' => '参数异常'
            ]);
        }
        foreach ($values as $value)
        {
            $this->checkComment($value);
        }
        return true;
    }

    protected function checkComment($value){
        $validate = new BaseValidate($this->singRule);
        $result = $validate->check($value);
        if(!$result){
            throw new ParameterException([
                'msg' => '参数异常'
            ]);
        }
    }

    protected $singRule = [
        'product_id' => 'require|isPositiveInteger','产品id必须添加|产品id必须是正整数',
        'order_id' => 'require|isPositiveInteger','订单id必须添加|订单id必须是正整数',
        'prop_ids' => 'require','属性id必须添加',
        'prop_value' => 'require','属性名必须添加',
        'content' => 'require','评论必须添加',
        'scope' => 'require|isPositiveInteger','评分必须添加|评分必须是正整数',
        'counts' => 'require|isPositiveInteger','产品数量必须添加|产品数量必须是正整数',
        'img_urls' => 'isString',
    ];
}