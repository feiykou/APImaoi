<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/29
 * Time: 5:34
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\Category as CategoryValidate;
use app\api\model\Category as CategoryModel;
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

}