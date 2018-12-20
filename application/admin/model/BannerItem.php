<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/20 0020
 * Time: 下午 18:16
 */

namespace app\admin\model;


use think\Model;

class BannerItem extends Model
{
    public function getBannerData($banner_id=0){
        $data = [
            'banner_id' => $banner_id
        ];
        $order = [
            'sort' => 'desc'
        ];
        $result = $this->where($data)->order($order)->select();
        return $result;
    }
}