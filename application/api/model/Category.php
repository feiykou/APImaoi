<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/29
 * Time: 5:35
 */

namespace app\api\model;


use catetree\Catetree;
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

    // 获取顶级分类
    public static function getTopCate(){
        $data = self::where('pid',0)
            ->order([
                'sort' => 'desc',
                'id' => 'desc'
            ])
            ->field('cate_name,id')
            ->select();
        return $data;
    }

    // 获取分类下的所有产品
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

    /*
     * 获取分类信息  --- 多个分类
     */
    private static function _getSelCate($ids=[]){
        $data = self::where('status','=','1')
            ->field('id,cate_name,cate_img')
            ->order([
                'sort' => 'desc',
                'id' => 'desc'
            ])->select($ids);
        return $data;
    }


    public static function getSonData($cateId){
        $cateTree = new Catetree();
        $ids = $cateTree->sonids($cateId, new self());

        $data = null;
        if(count($ids) > 0){
            $data = self::_getSelCate($ids);
        }
        return $data;
    }
}