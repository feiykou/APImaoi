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

    public static function getSummaryByPage($page=1, $size=20){
        $pagingData = self::order('create_time desc')
            ->paginate($size, '', ['page' => $page]);
        return $pagingData;
    }
}