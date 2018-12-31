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
        if($status != -1 && $status != 0){
            $data = [
                'user_id' => $uid,
                'status' => $status
            ];
        }else{
            $data = [
                'user_id' => $uid,
                'status' => ['neq', 0]
            ];
        }
        $pagingData = self::where($data)
            ->order('create_time desc')
            ->paginate($size, true, ['page' => $page]);
        return $pagingData;
    }

    public function getSummaryByPage($page=1, $size=20){
        $data = [
            'status' => ['neq',0]
        ];
        $pagingData = self::order('create_time desc')
            ->where($data)
            ->paginate($size, true, ['page' => $page]);
        return $pagingData ;
    }

    public static function removeOrder($uid,$id,$status=1){
        $data = [
            'id' => $id,
            'user_id' => $uid,
        ];
        $result = self::where($data)->update(['status' => $status]);
        return $result;
    }
}