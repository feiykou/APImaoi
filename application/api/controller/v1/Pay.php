<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/8
 * Time: 16:27
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\WxConfig;
use app\api\service\WxNotify;
use app\api\validate\IDMustBePositiveInt;
use app\api\service\Pay as PayService;


class Pay extends BaseController
{
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'getPreOrder']
    ];


    /**
     * 预订单
     * @url     /pay/pre_order?id=:id
     * @http    post
     * @param   string $id
     * @return  array
     */
    public function getPreOrder($id=''){
        (new IDMustBePositiveInt())->goCheck();
        $pay = new PayService($id);
        return $pay->pay();
    }

    /*
     * 给微信的接口
     */
    public function receiveNotify(){
        $notify = new WxNotify();
//        $config = new WxConfig();
        return $notify;
//        $notify->Handle($config);
    }


//    public function notifyConcurrency()
//    {
//        $notify = new WxNotify();
//        $notify->handle();
//    }

//    public function receiveNotify()
//    {
////        $xmlData = file_get_contents('php://input');
////        Log::error($xmlData);
////        $notify = new WxNotify();
////        $notify->handle();
//        $xmlData = file_get_contents('php://input');
//        $result = curl_post_raw('http:/tbaup.cn/api/v1/pay/re_notify?XDEBUG_SESSION_START=11025',
//            $xmlData);
//    }


}