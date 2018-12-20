<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/30
 * Time: 15:41
 */

namespace app\api\model;


class ProductImage extends BaseModel
{

    protected $hidden = ['id', 'product_id', 'sort'];
    public function getImgUrlAttr($value, $data)
    {
        return $this->prefixImgUrl($value, $data);
    }
}