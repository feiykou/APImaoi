<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/29
 * Time: 5:35
 */

namespace app\api\model;


use think\Model;

class Category extends BaseModel
{

    protected $hidden = ['sort','show_cate','keywords','create_time','update_time'];

    public function product(){
        return $this->hasMany('product','category_id','id');
    }

    public static function getProducts($size=1,$page=5){
        $result = self::with('product')
            ->paginate($size,'',['page' => $page]);
        return $result;
    }

    public function getCateImgAttr($value,$data){
        return $this->prefixImgUrl($value, $data);
    }

    public static function getProductAndCate($id){
        $data = self::with(['product' => function($query){
            $query->where('on_sale','=',1);
        }])->where('id',$id)->find();
        return $data;
    }


    public static function getRecIndexCate($recposId, $pid){
        $data = [
            'recpos_id' => $recposId,
            'value_type' => 2
        ];
        $_cateRes = db('rec_item')->where($data)
            ->select();
        $cateIdArr = [];
        foreach ($_cateRes as $k => $v){
            array_push($cateIdArr, $v['value_id']);
        }
        $data = self::where(['id' => ['in', $cateIdArr],'pid' => $pid])
            ->order('sort desc')
            ->select();

        return $data;
    }
}