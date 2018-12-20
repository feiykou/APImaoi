<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/28
 * Time: 17:14
 */

namespace app\api\model;


class Banner extends BaseModel
{

    protected $hidden = ['description','create_time','update_time'];

    public function items(){
        return $this->hasMany('BannerItem', 'banner_id','id');
    }



    public static function getBannerById($id){
        $banner = self::with(['items' => function($query){
            $query->order('sort desc');
        }])->find($id);
        return $banner;
    }
}