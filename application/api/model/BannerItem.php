<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/28
 * Time: 17:16
 */

namespace app\api\model;


class BannerItem extends BaseModel
{
    protected $hidden = ['id','sort','banner_id','create_time','update_time'];

    public function getImgUrlAttr($value, $data)
    {
        return $this->prefixImgUrl($value, $data);
    }

    public function getVideoUrlAttr($value, $data)
    {
        return $this->prefixVideoUrl($value, $data);
    }
}