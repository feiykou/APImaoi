<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/27
 * Time: 12:00
 */

namespace app\admin\model;


use app\api\model\BaseModel;

class Order extends BaseModel
{

    public function user(){
        return $this->belongsTo('User');
    }

    public static function getSummaryByPage($status=-1, $page=1, $size=20){
        $data = [];
        if($status != -1){
            $data = [
                'status' => $status
            ];
        }

        $pagingData = self::order('create_time desc')
            ->where($data)
            ->paginate($size, '', ['page' => $page, 'query' => ['status'=>$status]]);
        return $pagingData;
    }
}