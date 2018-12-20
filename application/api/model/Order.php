<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/7
 * Time: 21:05
 */

namespace app\api\model;


class Order extends BaseModel
{

    protected $hidden = ['user_id','delete_time'];

    public function getSnapItemsAttr($value){
        if(empty($value)){
            return null;
        }
        return json_decode($value);
    }

    public function getSnapAddressAttr($value){
        if(empty($value)){
            return null;
        }
        return json_decode(($value));
    }

    // 获取当前用户全部订单
    public static function getSummaryByUser($uid, $page=1, $size=10, $status){
        if($status != -1){
            $data = [
                'user_id' => $uid,
                'status' => $status
            ];
        }else{
            $data = [
                'user_id' => $uid
            ];
        }
        $pagingData = self::where($data)
            ->order('create_time desc')
            ->paginate($size, true, ['page' => $page]);
        return $pagingData;
    }

    public function getSummaryByPage($page=1, $size=20){
        $pagingData = self::order('create_time desc')
            ->paginate($size, true, ['page' => $page]);
        return $pagingData ;
    }
}