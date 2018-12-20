<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/30
 * Time: 17:26
 */

namespace app\api\model;


class ProductProp extends BaseModel
{
    protected $hidden = ['id','prop_id'];
    public function getImgUrlAttr($value, $data)
    {
        return $this->prefixImgUrl($value, $data);
    }
    public static function getProductPropByIds($ids){
        $ids = explode(',',$ids);
        $result = self::all($ids);
        return $result;
    }
}