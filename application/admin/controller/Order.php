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

class Order extends Base
{
    public function index($page=1, $size=20){
        // 获取全部订单
        $orderData = $this->getSummary($page, $size);
        $this->assign('orderData', $orderData);
        return $this->fetch();
    }

    public function getSummary($page=1, $size=20){
        (new PagingParameter())->goCheck();
        $pagingOrders = OrderModel::getSummaryByPage($page, $size);
        $data = $pagingOrders->hidden(['snap_items', 'snap_address']);
        return $data;
    }
}