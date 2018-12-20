<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/12 0012
 * Time: 下午 16:48
 */

namespace app\admin\model;


use think\Model;

class Category extends Model
{
    protected $field = true;
    protected static function init()
    {
        self::afterInsert(function ($category){
            // 接收表单数据
            $categoryData = input('post.');
            // 商品id
            $categoryId = $category->id;
            // 处理商品推荐位
            // 处理推荐位
            if(isset($categoryData['recpos'])){
                foreach ($categoryData['recpos'] as $k=>$v){
                    db('rec_item')->insert([
                        'recpos_id' => $v,
                        'value_id' => $categoryId,
                        'value_type' => 2,
                    ]);
                }
            }
        });

        self::beforeUpdate(function ($category){
            // 接收表单数据
            $categoryData = input('post.');
            // 商品id
            $categoryId = $category->id;
            // 处理推荐位
            db('rec_item')->where([
                'value_id' => $categoryId,
                'value_type' => 2
            ])->delete();
            if(isset($categoryData['recpos'])){
                foreach ($categoryData['recpos'] as $k=>$v){
                    db('rec_item')->insert([
                        'recpos_id' => $v,
                        'value_id' => $categoryId,
                        'value_type' => 2
                    ]);
                }
            }
        });

    }
}