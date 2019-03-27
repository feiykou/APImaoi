<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/29
 * Time: 5:34
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\CateFilter;
use app\api\validate\Category as CategoryValidate;
use app\api\model\Category as CategoryModel;
use app\api\model\Product as ProductModel;
use app\api\validate\CateIDMustBePositiveInt;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\CategoryException;
use catetree\Catetree;

class Category extends BaseController
{

    public function getProductsByCate($id){
        (new IDMustBePositiveInt())->goCheck();
        $data = CategoryModel::getProductAndCate($id);
        if(!$data){
            throw new CategoryException();
        }
        return $data;
    }


    /**
     * 分类筛选
     */
    public function filteCate($size=10,$page=1){
        $validate = new CateFilter();
        $validate->goCheck();
        $data['price'] = input('get.price');
        $data['category_id'] = input('get.cateid');

        if(intval($data['category_id']) == 0){
            unset($data['category_id']);
        }
        if(strlen($data['price']) == 1 && intval($data['price']) == 0){
            unset($data['price']);
        }else{
            $priceArr = explode('-',$data['price']);
            $data['price'] = ['between',[intval($priceArr[0]),intval($priceArr[1])]];
        }

        $resData = ProductModel::filterCate($data,$size,$page);
        if(!$resData){
            throw new CategoryException();
        }
        return $resData;
    }

    /**
     * 获取一级分类
     */
    public function getTopCate(){
        $data = CategoryModel::getTopCate();
        return $data;
    }

    public function getSonCate($cateid=0){
        (new CateIDMustBePositiveInt())->goCheck();
        if($cateid == 0){
            $data = $this->getTopCate();
        }else{
            $data = CategoryModel::getSonData($cateid);
        }
        return $data;
    }
}