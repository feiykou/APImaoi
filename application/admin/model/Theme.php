<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/13 0013
 * Time: 下午 12:22
 */

namespace app\admin\model;


use think\Model;

class Theme extends Model
{
    protected $field = true;


    public function recpos(){
        return $this->belongsToMany('recpos','rec_item','recpos_id','value_id')->field('id,name');
    }

    public function product(){
        return $this->belongsToMany('product','theme_product','product_id','theme_id')->field('id,name');
    }

    public function getAllProduct(){
        return db('product')->select();
    }

    public function getRescData($theme_id){
        $themeData = self::get($theme_id);
        $rescData = $themeData->recpos;
        return $rescData;
    }

    public function getSelProduct($theme_id){
        $themeData = self::get($theme_id);
        $productData = $themeData->product;
        return $productData;
    }

    public function getAllTheme(){
        $order = [
            'sort' => 'desc',
            'id'   => 'desc'
        ];
        $result = $this->order($order)->select();
        foreach ($result as &$v){
            $v['recpos'] = self::get($v['id'])->recpos;
        }
        return $result;
    }

    protected static function init(){
        Theme::afterInsert(function ($themes){
            // 接收表单数据
            $themeData = input('post.');
            $themeId = $themes->id;
            // 处理推荐位
            if(isset($themeData['recpos'])){
                foreach ($themeData['recpos'] as $k=>$v){
                    db('rec_item')->insert([
                        'recpos_id' => $v,
                        'value_id' => $themeId,
                        'value_type' => 2,
                    ]);
                }
            }

            // 处理主题和产品关联
            if(isset($themeData['product_id'])){
                foreach ($themeData['product_id'] as $k=>$v){
                    $position = str_replace('，',',',$themeData['position'][$k]);
                    db('theme_product')->insert([
                        'product_id' => $v,
                        'theme_id'   => $themeId,
                        'sort'       => $themeData['sort'][$k],
                        'position'   => $position
                    ]);
                };
            }

        });

        Theme::beforeUpdate(function ($themes){
            // 接收表单数据
            $themeData = input('post.');
            $themeId = $themes->id;
            if(!isset($themeData['req_type']) || $themeData['req_type'] != 'lst'){
                // 处理推荐位
                db('rec_item')->where([
                    'value_id' => $themeId,
                    'value_type' => 2
                ])->delete();
                if(isset($themeData['recpos'])){
                    foreach ($themeData['recpos'] as $k=>$v){
                        db('rec_item')->insert([
                            'recpos_id' => $v,
                            'value_id' => $themeId,
                            'value_type' => 2,
                        ]);
                    }
                }
            }
            if(isset($themeData['product_id'])){
                // 处理产品
                db('theme_product')->where([
                    'theme_id' => $themeId,
                ])->delete();
                foreach ($themeData['product_id'] as $k=>$v){
                    $position = str_replace('，',',',$themeData['position'][$k]);
                    db('theme_product')->insert([
                        'product_id' => $v,
                        'theme_id'   => $themeId,
                        'sort'       => $themeData['sort'][$k],
                        'position'   => $position
                    ]);
                };
            }
        });

        Theme::beforeDelete(function($theme){
            $themeId = $theme->id;

            // 删除内存中的主图
            if($theme->main_img_url){
                $delsrc = DEL_FILE_URL . '/upload/images/' . $theme->main_img_url;
                if(file_exists($delsrc)){
                    @unlink($delsrc);
                }
            }

            if($theme->head_img_url){
                $delurl = DEL_FILE_URL . '/upload/images/' . $theme->head_img_url;
                if(file_exists($delurl)){
                    @unlink($delurl);
                }
            }
            // 处理推荐位
            db('rec_item')->where([
                'value_id' => $themeId,
                'value_type' => 2
            ])->delete();

            // 处理产品
            db('theme_product')->where([
                'theme_id' => $themeId,
            ])->delete();
        });
    }


    /*
     * 获取产品属性数组
     */
    public function getProductPropArr($id){
        $_radioAttrRes = db('product_prop')->alias('pp')
            ->field('pp.id,pp.prop_id,pp.prop_value,p.name as prop_name')
            ->join('property p','pp.prop_id = p.id')
            ->where([
                'pp.product_id' => $id,
                'p.type' => 1
            ])->select();
        $radioAttrRes = [];
        foreach ($_radioAttrRes as $k => $v){
            $radioAttrRes[$v['prop_name']][] = $v;
        }
        return $radioAttrRes;
    }
}