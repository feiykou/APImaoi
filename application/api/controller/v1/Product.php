<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/30
 * Time: 10:09
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\CateIDMustBePositiveInt;
use app\api\validate\Count;
use app\api\model\Product as ProductModel;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\ProductRescCount;
use app\api\validate\RescIDMustBePositiveInt;
use app\api\validate\Search;
use app\lib\exception\CategoryException;
use app\lib\exception\ProductException;
use app\api\model\Category as CategoryModel;
use catetree\Catetree;

class Product extends BaseController
{
    protected $beforeActionList = [
        'checkSuperScope' => ['only' => 'createOne,deleteOne']
    ];

    /**
     *
     * @url
     * @http
     * @param $data
     * @param int $page
     * @param int $size
     * @return array
     */
    public function search($page=1, $size=4){
        (new Search())->goCheck();
        $data = input('get.');
        $searchData = ProductModel::getSearchResult($data,$size,$page);
        if($searchData->isEmpty()){
            $searchData = [
                'data' => [],
                'total' => 0
            ];
        }else{
            $searchData = $searchData->hidden([
                'content', 'type_id', 'weight', 'unit', 'product_code'
            ]);
        }
        return $searchData;
    }


    /**
     * 获取分类下的产品
     * @url  product/recoIndexByCate?rescid=:rescid
     * @http
     * @param $rescid
     * @return array|false|\PDOStatement|string|\think\Collection
     */
    public function getProductsByCateID($rescid){
        (new RescIDMustBePositiveInt())->goCheck();
        $cateIndexArr = CategoryModel::getRecIndexCate(5, 0);
        foreach ($cateIndexArr as $k => &$v){
            $products = ProductModel::getRecIndexCate($rescid,$v['id']);
            $products = $products->hidden(['product_code','content','type_id','description','weight','unit','stock_total'])
                ->toArray();
            $v['product'] = $products;
        }
        return $cateIndexArr;
    }

    /**
     * 获取分类下的所有产品
     * @url  /product/cateProducts?cateid=:cateid
     * @http
     * @param $cateid
     * @return false|\PDOStatement|string|\think\Collection
     * @throws CategoryException
     */
    public function getProductByCate($cateid=0){
        (new CateIDMustBePositiveInt())->goCheck();
        if($cateid == 0){
            $productArr = ProductModel::where('on_sale',1)
                ->field('')
                ->order('create_time desc')
                ->select();
        }else{
            $catetree = new Catetree();
            $sonids = $catetree->childrenids($cateid, new CategoryModel());
            $sonids[] = intval($cateid);
            $productArr = ProductModel::getProductsByCateID($sonids);
        }
        if(empty($productArr)){
            throw new CategoryException([
                'msg' => '指定分类产品不存在',
                'errorCode' => 20001
            ]);
        }
        return $productArr;
    }


    /**
     * 获取首页推荐产品
     * @url     /product/recoIndex?count=:count
     * @http    get
     * @param   int $count
     * @return  false|\PDOStatement|string|\think\Collection
     * @throws  ProductException
   */
    public function getRecoIndex($rescid= 6,$count = 4)
    {
        (new ProductRescCount())->goCheck();
        $products = ProductModel::getIndex($rescid,$count);
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