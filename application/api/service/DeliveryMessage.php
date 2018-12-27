<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/27
 * Time: 10:15
 */

namespace app\api\service;


use app\api\model\User;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;

class DeliveryMessage extends WxMessage
{
    const DELIVERY_MSG_ID = '3kKi8U3HEeedO0kDcZJ9wI60GnKfHUPdlXJhVc9OR0E';

    public function sendDeliveryMessage($order, $tplJumpPage='', $expressNum=''){
        if(!$order){
            throw new OrderException();
        }
        $this->tplID = self::DELIVERY_MSG_ID;
        $this->page = $tplJumpPage;
        $this->formID = $order->prepay_id;
        $this->prepareMessageData($order,$expressNum);
        return parent::sendMessage($this->getUserOpenID($order->user_id));
    }

    private function prepareMessageData($order, $expressNum){
        $dt = new \DateTime();
        $data =[
            "keyword1"=> [
                "value"=> "顺丰速运"
            ],
            "keyword2"=> [
                "value"=> $dt->format("Y-m-d H:i")
            ],
            "keyword3"=> [
                "value"=> $order->snap_name
            ],
            "keyword4"=> [
                "value"=> $order->order_no
            ],
            "keyword5"=> [
                "value"=> $expressNum
            ]
        ];
        $this->data = $data;
        $this->emphasisKeyword = 'keyword3.DATA';
    }

    private function getUserOpenID($uid){
        $user = User::get($uid);
        if(!$user){
            throw new UserException();
        }
        return $user->openid;
    }
}