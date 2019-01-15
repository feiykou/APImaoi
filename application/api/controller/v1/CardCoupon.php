<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/12
 * Time: 11:59
 */

namespace app\api\controller\v1;


use cardCoupon\CardApi;

class CardCoupon
{
    public function addCard(){
        $cardApi = new CardApi();
        $data = $cardApi->get();
        return json_encode($data);
    }
}