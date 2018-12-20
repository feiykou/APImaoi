<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/30
 * Time: 10:09
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\Count;
use app\api\model\Product as ProductModel;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\ProductException;

class Product extends BaseController
{
    protected $beforeActionList = [
        'checkSuperScope' => ['only' => 'createOne,deleteOne']
    ];
    /**
     * 获取首页推荐产品
     * @url     /product/recoIndex?count=:count
     * @http    get
     * @param   int $count
     * @return  false|\PDOStatement|string|\think\Collection
     * @throws  ProductException
     */
    public function getRecoIndex($count = 15)
    {
        (new Count())->goCheck();
        $products = ProductModel::getIndex($count);
        if($products->isEmpty()){
            throw new ProductException();
        }
        $products = $products->hidden([
            'content', 'type_id', 'weight', 'unit', 'product_code'
        ]);
        return $products;
    }

    /**
     * 获取产品详情
     * @url     /product/:id/detail
     * @http    get
     * @param   $id
     * @return  array|false|\PDOStatement|string|\think\Model
     * @throws  ProductException
     */
    public function getOne($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $product = ProductModel::getProductDetail($id);
        if(!$product){
            throw new ProductException();
        }
        return $product;
    }

    /**
     * 获取当前产品属性
     * @url     product/singleProp?id=:id
     * @http    get
     * @param   $id
     * @return  array
     */
    public function getSingleProp($id){
        (new IDMustBePositiveInt())->goCheck();
        $stockProp = ProductModel::getProductProp($id);
        return $stockProp;
    }

    public function createOne()
    {
        $product = new ProductModel();
        $product->save([
            'id' => 1
        ]);
    }

    public function deleteOne($id)
    {
        ProductModel::destroy($id);
    }

}