<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/27
 * Time: 10:43
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\DeliveryMessage;
use app\api\validate\Message as MessageValidate;
use app\lib\exception\MissException;
use app\lib\exception\SuccessMessage;

class Message extends BaseController
{
    public function delivery(){
        if(request()->isPost()){
            (new MessageValidate())->goCheck('delivery');
            $data = input('post.');
            $deliveryMsg = new DeliveryMessage();
            $result = $deliveryMsg->sendDeliveryMessage($data['order'], $data['tplJumpPage'],$data['expressNum']);
            if($result){
                return json(new SuccessMessage(), 201);
            }else{
                return json(new MissException(),404);
            }
        }
    }
}