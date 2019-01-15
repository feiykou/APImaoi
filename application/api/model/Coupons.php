<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/13
 * Time: 16:52
 */

namespace app\api\model;


use think\Db;
use think\Exception;
use think\Log;

class Coupons extends BaseModel
{

    protected $hidden = ['create_time','delete_time','update_time','taken_num','status'];

    public function couponsTaken(){
        return $this->hasOne('coupons_taken');
    }

    public function couponsTakens(){
        return $this->hasMany('coupons_taken','coupons_id','id');
    }

    public static function getCouponsByStatus($uid,$status,$size=10,$page=1,$mark=false){
        $sign = $mark ? '<' : '>';
        $data = [
            'c.status' => 1,
            'c.end_date' => [$sign,time()]
        ];

        $condition = 't.coupons_id = c.id and t.user_id = '.$uid.' and t.status = '.$status;

        $result = db('coupons')->alias('c')
            ->field('c.id,c.name,c.start_date,c.end_date,c.least_cost,c.reduce_cost,c.description,c.url')
            ->join('coupons_taken t',$condition)
            ->where($data)
            ->order('least_cost desc')
            ->paginate($size,false,['page'=>$page]);
        return $result;
    }


    public static function getConponsList($uid,$size=10,$page=1){
        $idData = self::getTakenCouponids($uid);
        $data = [
            'status' => 1,
            'end_date' => ['>',time()],
            'id' => ['not in',$idData['use']]
        ];
        $result = self::where($data)
            ->order('create_time desc')
            ->paginate($size,false,['page'=>$page])->toArray();
        foreach ($result['data'] as $key => &$val){
            if(in_array($val['id'], $idData['nouse'])){
                $val['is_revice'] = 1;
            }else{
                $val['is_revice'] = 2;
            }
        }
        return $result;
    }

    public static function addCard($id,$uid){
        Db::startTrans();
        try{
            $coupon = self::get($id);
            $coupon->setDec('num');
            $coupon->setInc('taken_num');
            $result = $coupon->couponsTaken()->save([
                'user_id' => $uid,
                'taken_time' => time()
            ]);
            Db::commit();
            return $result;
        }catch(Exception $ex){
            Db::rollback();
            Log::error($ex);
            return false;
        }
    }

    public static function getTakenCouponids($uid){
        $couponsIdArr = model('coupons_taken')->where(['user_id' => $uid])
            ->field(['coupons_id','status'])
            ->select()
            ->toArray();
        $arr = [];
        $useArr = [];
        foreach ($couponsIdArr as $val){
            if($val['status'] == 1){
                array_push($arr,$val['coupons_id']);
            }else{
                array_push($useArr,$val['coupons_id']);
            }
        }
        return [
            'nouse' => $arr,
            'use' => $useArr
        ];
    }

    public static function isExistCard($uid,$id){
        $result = model('coupons_taken')->where([
            'user_id' => $uid,
            'coupons_id' => $id
        ])->find();
        if($result){
            return true;
        }
        return false;
    }

    public static function getCouponPrice($uid,$id){
        $result = self::hasWhere('couponsTaken',['user_id'=>$uid,'coupons_id'=>$id,'status'=>1])
            ->where('end_date','>',time())
            ->column('reduce_cost');
        return $result;
    }
}