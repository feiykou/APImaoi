<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/13
 * Time: 16:52
 */

namespace app\api\model;


class CouponsTaken extends BaseModel
{
    public function coupons(){
        return $this->hasMany('coupons','id','coupons_id');
    }

    public static function updateStatus($uid, $couponid){
        $result = self::where([
            'user_id' => $uid,
            'coupons_id' => $couponid
        ])->update(['status' => 2]);
        return $result;
    }
}