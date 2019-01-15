<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/28
 * Time: 17:15
 */

namespace app\api\model;


use think\Model;

class BaseModel extends Model
{
    protected function prefixImgUrl($value, $data){
        if(!$value){
            return $value;
        }
        $finalUrl = config('APISetting.img_prefix').IMG_URL.$value;
        return $finalUrl;
    }

    protected function prefixVideoUrl($value, $data){
        if(!$value){
            return $value;
        }
        $finalUrl = config('APISetting.img_prefix').VIDEO_URL.$value;
        return $finalUrl;
    }

    protected function prefixWxCodeUrl($value, $data){
        if(!$value){
            return $value;
        }
        $finalUrl = config('APISetting.img_prefix').WX_URL.$value;
        return $finalUrl;
    }

    protected function prefixAPIUrl($value, $data){
        if(!$value){
            return $value;
        }
        $finalUrl = config('APISetting.img_prefix').API_URL.$value;
        return $finalUrl;
    }
}