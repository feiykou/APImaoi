<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/8
 * Time: 18:43
 */

namespace app\api\service;
use app\api\model\ProductStock;
use app\lib\enum\OrderStatusEnum;
use think\Db;
use think\Exception;
use think\Loader;
use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use think\Log;

Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');

class WxNotify extends \WxPayNotify
{
    public function NotifyProcess($data, &$msg){
        if($data['result_code'] == 'SUCCESS'){
            $orderNo = $data['out_trade_no'];
            Db::startTrans();
            try{
                // 检查库存，并更新订单状态
                $order = OrderModel::where('order_no','=',$orderNo)
                    ->lock(true)
                    ->find();
                if($order->status == OrderStatusEnum::UNPAID){
                    $service = new OrderService();
                    $status = $service->checkOrderStock($order->id);
                    if($status['pass']){
                        $this->updateOrderStock($order->id, true);
                        $this->reduceStock($status);
                    }else{
                        $this->updateOrderStock($order->id, false);
                    }
                }
                Db::commit();
            }catch (Exception $ex){
                Db::rollback();
                Log::error($ex);
                return false;
            }
        }
    }

    private function reduceStock($status){
        foreach ($status['pStatusArray'] as $singlePStatus){
            ProductStock::where('id','=',$singlePStatus['stockId'])
                ->setDec('stock',$singlePStatus['count']);
        }
    }

    private function updateOrderStock($orderID, $success){
        $status = $success?OrderStatusEnum::PAID : OrderStatusEnum::PAID_BUT_OUT_OF;
        OrderModel::where('id','=',$orderID)
            ->update(['status'=>$status]);
    }
}