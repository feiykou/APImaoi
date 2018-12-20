<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/19
 * Time: 22:06
 */

namespace app\admin\model;


use think\Model;

class Recpos extends Model
{
    protected $field = true;

    public function is_unique($name="",$id=0,$type=0){
        $data = [
            'id'         => ['neq',$id],
            'name' => $name,
            'type' => $type
        ];
        $model = request()->controller();
        $result = model($model)->where($data)->find();
        return $result;
    }
}