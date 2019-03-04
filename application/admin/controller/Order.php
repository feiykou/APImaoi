<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/27
 * Time: 11:59
 */

namespace app\admin\controller;


use app\admin\validate\PagingParameter;
use app\admin\model\Order as OrderModel;
use app\admin\validate\Order as OrderValidate;

class Order extends Base
{
    public function index($status=-1, $page=1, $size=20){
        (new OrderValidate())->gocheck();
        // 获取全部订单
        $orderData = $this->getSummary($status, $page, $size);
        $this->assign('orderData', $orderData);
        return $this->fetch();
    }

    public function getSummary($status,$page=1, $size=20){
        $pagingOrders = OrderModel::getSummaryByPage($status,$page, $size);
        $data = $pagingOrders->hidden(['snap_items', 'snap_address']);
        return $data;
    }
}