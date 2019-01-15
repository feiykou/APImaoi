<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/11
 * Time: 14:43
 */

namespace app\api\model;


class UserComment extends BaseModel
{

    protected $hidden = ['id','delete_time','update_time','status','user_id','product_id'];


    protected function getImgUrlsAttr($value, $data){
        if(!$value){
           return $value;
        }
        $imgArr = explode(';',$value);
        $dataArr = [];
        foreach ($imgArr as $val){
            $img_url = $this->prefixAPIUrl($val, $data);
            array_push($dataArr,$img_url);
        }
        return $dataArr;
    }


    public function user(){
        return $this->belongsTo('user','user_id','id');
    }

    public function product(){
        return $this->belongsTo('product','product_id','id');
    }



    public static function getProductComment($product_id, $size=10, $page=1){
        $data = [
            'product_id' => $product_id,
            'status' => 1
        ];
        $order = [
            'create_time' => 'desc'
        ];
        $data = self::where($data)
            ->with(['user' => function($query){
                $query->field('id, nickName,avatar_img');
            }])
            ->order($order)
            ->paginate($size,false,['page' => $page]);
        return $data;
    }

    public static function getUserComment($uid, $id, $size=10, $page=1){
        $data = [
            'user_id' => $uid,
            'order_id' => $id
        ];
        $order = [
            'create_time' => 'desc'
        ];
        $data = self::where($data)
            ->with(['product' => function($query){$query->field('id,name,main_img_url');}])
            ->order($order)
            ->paginate($size,false,['page' => $page]);
        return $data;
    }
}