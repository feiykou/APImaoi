<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/9
 * Time: 12:24
 */

namespace app\api\model;


class UserFavorite extends BaseModel
{
    public function user(){
        return $this->belongsTo('user','user_id','id');
    }
    public function product(){
        return $this->belongsTo('product','product_id','id');
    }



    // 判断收藏产品是否存在
    public static function checkIsExist($uid, $product_id){
        $data = [
            'user_id' => $uid,
            'product_id' => $product_id
        ];
        $isExist = self::where($data)->find();

        if($isExist){
            if($isExist['status'] == 0 ){
                $result = self::updateFavo($uid,$product_id,1);
                return $result;
            }
            return true;
        };

        return false;
    }

    public static function updateFavo($uid, $product_id, $status){
        $data = [
            'user_id' => $uid,
            'product_id' => $product_id
        ];
        $result = self::where($data)
            ->update(['status' => $status]);
        return $result;
    }

    // 收藏列表
    public static function listFavo($uid, $page=1,$size=10){
        $data = [
            'user_id' => $uid,
            'status' => 1
        ];

        $result = self::where($data)->with(['product' => function($query){
            $query->where(['on_sale'=>1])->field('name,main_img_url,id,price,description');
        }])->paginate($size,false,['page'=>$page]);
        return $result;
    }

}