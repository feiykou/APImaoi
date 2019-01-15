<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/13
 * Time: 19:56
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\Coupons as CouponsValidate;
use app\api\model\Coupons as CouponsModel;
use app\api\service\Token as TokenService;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\FailMessage;
use app\lib\exception\SuccessMessage;

class Coupons extends BaseController
{

    protected $beforeActionList = [
        'checkPrimaryScope' => ['only' => 'getCoupons']
    ];

    public function getCoupons($status,$size=10,$page=1){
        (new CouponsValidate())->goCheck();
        $uid = TokenService::getCurrentUid();

        $data = [];
        // 获取已使用的数据
        if($status == 1 ||  $status == 2){
            $data = CouponsModel::getCouponsByStatus($uid,$status,$size,$page);
        }
        // 获取已领取的过期数据
        if($status == 3){
            $data = CouponsModel::getCouponsByStatus($uid,1,$size,$page,true);
        }
        // 获取未领取和已领取（未过期未使用）的数据
        if($status == 0){
            $data = CouponsModel::getConponsList($uid,$size,$page);
        }
        return $data;
    }

    public function receiveCoupon($id){
        (new IDMustBePositiveInt())->goCheck();
        $uid = TokenService::getCurrentUid();
        $data = CouponsModel::getCouponPrice($uid,$id);
        if(CouponsModel::isExistCard($uid,$id)){
            return json(new FailMessage());
        }
        $result = CouponsModel::addCard($id,$uid);
        if(!$result){
            return json(new FailMessage());
        }else{
            return json(new SuccessMessage());
        }
    }


}