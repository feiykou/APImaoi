<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/4
 * Time: 20:59
 */

namespace app\api\service;


/*
 * 订单类
 *
 * 思考：
 *  创建订单时检测库存量，但并不会预扣库存量，因为这需要队列支持
 *  未支付的订单再次支付时可能会出现库存不足的情况
 *
 * 项目采用3次检测：
 *  1：创建订单时检测库存
 *  2：支付前检测库存
 *  3：支付成功后检测库存
 */
use app\api\model\OrderProduct;
use app\api\model\Product;
use app\api\model\ProductProp;
use app\api\model\UserAddress;
use app\api\model\Order as OrderModel;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use think\Db;
use think\Exception;


class Order
{

    protected $oProducts;
    protected $products;
    protected $uid;
    protected $addressId;

    function __construct()
    {
    }

    public function place($uid,$oProducts,$addressId){
        $this->uid = $uid;
        $this->oProducts = $oProducts;
        $this->addressId = $addressId;
        $this->products = $this->getProductsByOrder($oProducts);
        $status = $this->getOrderStatus();
        if(!$status['pass']){
            $status = -1;
            return $status;
        }
        $orderSnap = $this->snapOrder();
        $status = self::createOrderByTrans($orderSnap);
        $status['pass'] = true;
        return $status;
    }

    // 创建订单时没有预扣除库存量，简化处理
    // 如果预扣除了库存量需要队列支持，且需要使用锁机制
    private function createOrderByTrans($snap){
        Db::startTrans();
        try{
            $orderNo = $this->makeOrderNo();
            $order = new OrderModel();
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->total_price = $snap['orderPrice'];
            $order->total_count = $snap['totalCount'];
            $order->snap_img = $snap['snapImg'];
            $order->snap_name = $snap['snapName'];
            $order->snap_address = $snap['snapAddress'];
            $order->snap_items = json_encode($snap['pStatus']);
            $order->save();

            $orderID = intval($order->id);
            $create_time = $order->create_time;

            foreach ($this->oProducts as &$p){
                $p['order_id'] = $orderID;
            }
            $orderProduct = new OrderProduct;
            $product = $this->oProducts;
            $orderProduct->saveAll($this->oProducts);
            Db::commit();
            return [
                'orderNo' => $orderNo,
                'order_id' => $orderID,
                'create_time' => $create_time
            ];
        }catch (Exception $ex){
            Db::rollback();
            throw $ex;
        }
    }

    public static function makeOrderNo()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn =
            $yCode[intval(date('Y')) - 2018] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
    }

    // 预检测并生成订单快照
    private function snapOrder(){
        // status可以单独定义一个类
        $snap = [
            'orderPrice' => 0,
            'totalCount' => 0,
            'pStatus' => [],
            'snapAddress' => json_encode($this->getUserAddress()),
            'snapName' => $this->products[0]['name'],
            'snapImg' => $this->products[0]['main_img_url']
        ];

        if(count($this->products) > 1){
            $snap['snapName'] .= '等';
        }

        for($i=0; $i < count($this->products); $i++){
            $product = $this->products[$i];
            $oProduct = $this->oProducts[$i];

            $pStatus = $this->snapProduct($product, $oProduct['count']);
            $snap['orderPrice'] += $pStatus['totalPrice'];
            $snap['totalCount'] += $pStatus['count'];
            array_push($snap['pStatus'], $pStatus);
        }
        return $snap;
    }

    // 单个商品订单
    private function snapProduct($product, $oCount){
        $pStatus = [
            'id' => null,
            'name' => null,
            'main_img_url' => null,
            'count' => $oCount,
            'totalPrice' => 0,
            'price' => 0
        ];

        // 以服务器价格为准，生成订单
        $pStatus['totalPrice'] = $oCount * $product['price'];
        $pStatus['name'] = $product['name'];
        $pStatus['main_img_url'] = $product['main_img_url'];
        $pStatus['price'] = $product['price'];
        $pStatus['id'] = $product['id'];
        return $pStatus;
    }

    private function getUserAddress(){
        $data = [
            'id' => $this->addressId,
            'user_id' => $this->uid,
            'status' => 1
        ];
        $userAddress = UserAddress::where($data)
            ->find();

        if(!$userAddress){
            throw new UserException([
                'msg' => '用户收货地址不存在，下单失败',
                'errorCode' => 60001
            ]);
        }
        return $userAddress->toArray();
    }

    private function getOrderStatus(){
        $status = [
            'pass'  =>  true,
            'orderPrice' => 0,
            'pStatusArray' => []
        ];

        foreach ($this->oProducts as $oProducts){
            $pStatus = $this->getProductStatus(
                $oProducts['product_id'],
                $oProducts['count'],
                $oProducts['product_prop_ids'],
                $this->products);

            if(!$pStatus['haveStock']){
                $status['pass'] = false;
            }
            $status['orderPrice'] += $pStatus['totalPrice'];
            array_push($status['pStatusArray'], $pStatus);
        }
        return $status;
    }

    private function getProductStatus($oPID, $oCount, $oPropIds, $products){
        $pIndex = -1;
        $pStatus = [
            'id' => null,
            'haveStock' => false,
            'count' => 0,
            'name' => '',
            'totalPrice' => 0
        ];

        for ($i=0; $i<count($products) ;$i++){
            $oPids = explode(',',$oPropIds);
            sort($oPids);
            $pids = $products[$i]['propIds'];
            $mark = $oPID == $products[$i]['id'];
            if($mark && $pids == $oPids){
                $pIndex = $i;
            }
        }

        if($pIndex == -1){
            throw new OrderException([
                'msg' => 'id为' . $oPID . '的商品不存在或者所选参数不存在，订单创建失败'
            ]);
        }else{
            $product = $products[$pIndex];
            $pStatus['id'] = $product['id'];
            $pStatus['name'] = $product['name'];
            $pStatus['prop_value'] = $product['product_prop'];
            $pStatus['prop_ids'] = $oPropIds;
            $pStatus['stockId'] = $product['stockId'];
            $pStatus['count'] = $oCount;
            $pStatus['totalPrice'] = $product['price'] * $oCount;
            if($product['stock_num'] - $oCount >= 0){
                $pStatus['haveStock'] = true;
            }
        }
        return $pStatus;
    }

    private function getProductsByOrder($oProducts){
        $oPIDs = [];
        $oPropIDs = [];
        foreach ($oProducts as $k => $item){
            array_push($oPIDs,$item['product_id']);
            $oPropIDs[$item['product_id'].'-'.$k] = $item['product_prop_ids'];
        }
        $prducts = Product::getProductOrProStock($oPIDs)
            ->visible(['id', 'name', 'price', 'market_price', 'main_img_url','product_stock'])
            ->toArray();

        $productArr = [];
        foreach ($prducts as $k => $v){
            $productArr[$v['id']] = $v;
        }
        // 获取产品，属性和库存
        $productData = $this->resolveProduct($oPropIDs,$productArr);

        return $productData;
    }

    // 解析产品，属性和库存
    private function resolveProduct($oPropIDs,$productArr){
        $productData = [];
        foreach ($oPropIDs as $k=>$v){
            $product_id = explode('-',$k)[0];
            $product = $productArr[$product_id];
            // 获取库存
            foreach ($product['product_stock'] as $sk=>$sv){
                // 判断是否有属性
                if(empty($sv['product_prop'])){
                    $product['stock_num'] = $sv['stock_num'];
                    $product['propIds'] = [""];
                    $product['stockId'] = $sv['id'];
                    unset($product['product_stock']);
                    break;
                }
                // 获取库存
                $product_propArr = explode(',',$sv['product_prop']);
                $ov = explode(',',$v);
                sort($ov);
                sort($product_propArr);
                if($ov == $product_propArr){
                    $product['stock_num'] = $sv['stock_num'];
                    $product['propIds'] = $product_propArr;
                    $product['stockId'] = $sv['id'];
                    $product['price'] = $sv['price'];
                    unset($product['product_stock']);
                    break;
                }
            }
            // 获取属性和价格
            $ProductProps = ProductProp::getProductPropByIds($v)->toArray();
            $proStr = '';
            foreach ($ProductProps as $ppk => $ppv){
                $proStr .= $ppv['prop_value'].',';
            }
            $product['product_prop'] = trim($proStr,',');
            $productData[] = $product;
        }
        return $productData;
    }

    public function checkOrderStock($orderID){
        // 一定要从订单商品表中直接查询
        // 不能从商品表中查询订单商品，这将导致被删除的商品无法查询出订单商品来
        $oProducts = OrderProduct::where('order_id','=',$orderID)
            ->select();
        $this->products = $this->getProductsByOrder($oProducts);
        $this->oProducts = $oProducts;
        $status = $this->getOrderStatus();
        return $status;
    }
}