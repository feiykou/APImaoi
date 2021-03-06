<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/4
 * Time: 10:55
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\Token as TokenService;
use app\api\service\Order as OrderService;
use app\api\validate\giftOrder;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\OrderPlace;
use app\api\model\Order as OrderModel;
use app\api\validate\PagingParameter;
use app\api\validate\Message as MessageValidate;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\FailMessage;
use app\lib\exception\OrderException;
use app\lib\exception\SuccessMessage;


class Order extends BaseController
{

    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'placeOrder'],
        'checkPrimaryScope' => ['only' => 'getDetail,getSummaryByUser,remove,cancel'],
        'checkSuperScope' => ['only' => 'getSummary']
    ];


    /**
     * 查看订单是否存在
     */
    public function findOrder($user_id,$order_id){
        (new giftOrder())->goCheck();
        $data = OrderModel::getGiftOrder($user_id,$order_id);
        if($data){
            return json(new SuccessMessage(),201);
        }
        return json(new FailMessage(),201);
    }


    /**
     * 下单
     * @url     /order
     * @http    post
     * @return  array|int
     */
    public function placeOrder(){
        (new OrderPlace())->goCheck();
        $oProducts = input('post.products/a');
        $addressId = input('post.addressId');
        $couponId = input('post.couponId');
        if(!isset($couponId)){
            $couponId = 0;
        }
        $uid = TokenService::getCurrentUid();
        $order = new OrderService();
        $status = $order->place($uid,$oProducts,$addressId,$couponId);
        return $status;
    }

    /**
     * 获取订单详情
     * @url     order/:id
     * @http    get
     * @param   $id
     * @return  $this
     * @throws  OrderException
     */
    public function getDetail($id){
        (new IDMustBePositiveInt())->goCheck();
        $orderMDetail = OrderModel::get($id);
        if(!$orderMDetail){
            throw new OrderException();
        }
        return $orderMDetail
            ->hidden(['prepay_id']);
    }


    /**
     * 根据用户id和状态分页获取订单列表（简要信息）
     * @url     order/by_user
     * @http    get
     * @param   int $page
     * @param   int $size
     * @return  array
     */
    public function getSummaryByUser($page=1, $size=4, $status=-1){
        (new PagingParameter())->goCheck();
        $uid = TokenService::getCurrentUid();
        $pagingOrders = OrderModel::getSummaryByUser($uid, $page, $size, $status);
        if($pagingOrders->isEmpty()){
            return [
                'has_more' => false,
                'data' => []
            ];
        }
        $data = $pagingOrders->hidden(['snap_address','prepay_id'])
            ->toArray();
        return $data;
    }


    /**
     * 获取全部订单简要信息（分页）
     * @url     order/paginate
     * @http    get
     * @param   int $page
     * @param   int $size
     * @return  array
     */
    public function getSummary($page=1, $size = 20){
        (new PagingParameter())->goCheck();
        $pagingOrders = OrderModel::getSummaryByPage($page, $size);
        if($pagingOrders->isEmpty()){
            return [
                'current_page' => $pagingOrders->currentPage(),
                'data' => []
            ];
        }
        $data = $pagingOrders->hidden(['snap_items', 'snap_address'])
            ->toArray();
        return [
            'current_page' => $pagingOrders->currentPage(),
            'data' => $data
        ];
    }

    /**
     * 发送模板消息
     * @url     order/delivery
     * @http    put
     * @param   $id
     * @return  SuccessMessage
     */
    public function delivery(){
        (new MessageValidate())->goCheck('delivery');
        $data = input('put.');
        $order = new OrderService();
        $success = $order->delivery($data['id'], '/pages/index/index', '');
        if($success){
            return new SuccessMessage();
        }
    }


    public function remove($id){
        (new IDMustBePositiveInt())->goCheck($id);
        return $this->changeOrderStatus($id, OrderStatusEnum::REMOVE);
    }

    public function cancel($id){
        (new IDMustBePositiveInt())->goCheck($id);
        return $this->changeOrderStatus($id, OrderStatusEnum::CANCEL);
    }

    private function changeOrderStatus($id,$status){
        $uid = TokenService::getCurrentUid();
        $result = OrderModel::changeOrderStatus($uid,$id,$status);
        if(!$result){
            throw new OrderException();
        }
        return new SuccessMessage();
    }

}