<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/13 0013
 * Time: 下午 12:22
 */

namespace app\admin\model;


use think\Model;

class Product extends Model
{

    protected $field = true;
    protected static function init(){
        Product::beforeInsert(function($products){
            $products->product_code = Product::makeProductNo();
        });

        Product::afterInsert(function ($products){

            // 接收表单数据
            $productData = input('post.');

            $mpriceArr = $products->mp;
            $productId = $products->id;

            // 处理推荐位
            if(isset($productData['recpos'])){
                foreach ($productData['recpos'] as $k=>$v){
                    db('rec_item')->insert([
                        'recpos_id' => $v,
                        'value_id' => $productId,
                        'value_type' => 1,
                    ]);
                }
            }

            // 处理会员价格
            if($mpriceArr){
                foreach ($mpriceArr as $k => $v){

                    if(!trim($v)){
                       continue;
                    }else{
                        db('member_price')->insert([
                            'mlevel_id' => $k,
                            'mprice' => $v,
                            'product_id' =>$productId
                        ]);
                    }
                }
            }

            // 处理产品图片
            $img_url_arr = explode(';',$products->product_img_url);
            if(isset($img_url_arr[0]) && $img_url_arr[0]){
                foreach ($img_url_arr as $k=>$v){
                    $data[$k]['img_url'] = $v;
                    $data[$k]['product_id'] = $productId;
                }
                db('product_image')->insertAll($data);
            }


            // 处理产品属性
            $prop_i = 0;
            if(isset($productData['product_prop'])){
                foreach ($productData['product_prop'] as $k => $v){
                    if(is_array($v)){
                        // 添加单独属性
                        if(!empty($v)){
                            foreach ($v as $k1 => $v1){
                                if(empty($v1)){
                                    $prop_i++;
                                    continue;
                                }
                                $img_url = '';
                                if(isset($productData['propImage'][$k])){
                                    $img_url = $productData['propImage'][$k][$k1];
                                }

                                db('product_prop')->insert([
                                    'prop_id' => $k,
                                    'prop_value' => $v1,
                                    'prop_name' => $productData['prop_name'][$prop_i],
                                    'product_id' => $productId,
                                    'prop_price' => $productData['prop_price'][$prop_i],
                                    'img_url' => $img_url
                                ]);
                                $prop_i++;
                            }
                        }
                    }else{
                        // 添加唯一属性
                        if(!empty($v)) {
                            db('product_prop')->insert([
                                'prop_id' => $k,
                                'prop_value' => $v,
                                'product_id' => $productId
                            ]);
                        }
                    }
                }
            }
        });

        Product::beforeUpdate(function ($products){
            $productId = $products->id;
            // 获取新增商品
            $productData = input('post.');
            // 处理推荐位
            db('rec_item')->where([
                'value_id' => $productId,
                'value_type' => 1
            ])->delete();
            if(isset($productData['recpos'])){
                foreach ($productData['recpos'] as $k=>$v){
                    db('rec_item')->insert([
                        'recpos_id' => $v,
                        'value_id' => $productId,
                        'value_type' => 1,
                    ]);
                }
            }

            // 处理产品图片
            db('product_image')->where('product_id','=',$productId)->delete();
            $img_url_arr = explode(';',$products->product_img_url);
            if(isset($img_url_arr[0]) && $img_url_arr[0]){
                foreach ($img_url_arr as $k=>$v){
                    $data[$k]['img_url'] = $v;
                    $data[$k]['product_id'] = $productId;
                }
                db('product_image')->insertAll($data);
            }

            // 处理会员价格
            $mpriceArr = $products->mp;
            // 删除原有会员价格
            db('member_price')->where('product_id','=',$productId)->delete();
            if($mpriceArr){
                foreach ($mpriceArr as $k => $v){
                    if(!trim($v)){
                        continue;
                    }else{
                        db('member_price')->insert([
                            'mlevel_id' => $k,
                            'mprice' => $v,
                            'product_id' =>$productId
                        ]);
                    }
                }
            }

            // 处理产品新增属性
            if(isset($productData['product_prop'])){
                $prop_i = 0;
                foreach ($productData['product_prop'] as $k => $v){
                    if(is_array($v)){
                        // 添加单独属性
                        if(!empty($v)){
                            foreach ($v as $k1 => $v1){
                                if(empty($v1)){
                                    $prop_i++;
                                    continue;
                                }
                                $img_url = '';
                                if(isset($productData['propImage'][$k])){
                                    $img_url = $productData['propImage'][$k][$k1];
                                }
                                db('product_prop')->insert([
                                    'prop_id' => $k,
                                    'prop_value' => $v1,
                                    'product_id' => $productId,
                                    'prop_name' => $productData['prop_name'][$prop_i],
                                    'prop_price' => $productData['prop_price'][$prop_i],
                                    'img_url' => $img_url
                                ]);
                                $prop_i++;
                            }
                        }
                    }else{
                        // 添加唯一属性
                        if(!empty($v)) {
                            db('product_prop')->insert([
                                'prop_id' => $k,
                                'prop_value' => $v,
                                'product_id' => $productId
                            ]);
                        }
                    }
                }
            }

            // 处理产品更新属性
            if(isset($productData['old_product_prop'])){
                $prop_i = 0;
                $propPrice = $productData['old_prop_price'];
                $idsArr = array_keys($propPrice);
                $valuesArr = array_values($propPrice);

                foreach ($productData['old_product_prop'] as $k => $v){
                    if(is_array($v)){
                        // 添加单独属性
                        if(!empty($v)){
                            foreach ($v as $k1 => $v1){
                                if(empty($v1)){
                                    $prop_i++;
                                    continue;
                                }
                                $img_url = '';
                                if(isset($productData['old_propImage'][$k])){
                                    $img_url = $productData['old_propImage'][$k][$k1];
                                }
                                db('product_prop')->where('id','=',$idsArr[$prop_i])->update([
                                    'prop_value' => $v1,
                                    'prop_price' => $valuesArr[$prop_i],
                                    'img_url' => $img_url
                                ]);
                                $prop_i++;
                            }
                        }
                    }else{
                        // 添加唯一属性
                        if(!empty($v)) {
                            db('product_prop')->where('id','=',$idsArr[$prop_i])->update([
                                'prop_value' => $v,
                                'prop_price' => $valuesArr[$prop_i]
                            ]);
                            $prop_i++;
                        }
                    }
                }
            }
        });

        Product::beforeDelete(function($products){
            $productId = $products->id;

            // 删除内存中的主图
            if($products->main_img_url){
                if(file_exists($products->main_img_url)){
                    @unlink($products->main_img_url);
                }
            }

            // 删除关联的会员价格
            db('member_price')->where('product_id','=',$productId)
                ->delete();

            // 删除关联的商品属性
            db('product_prop')->where('product_id','=',$productId)
                ->delete();

            // 删除关联的商品相册
            $product_imgs = db('product_image')->where('product_id','=',$productId)
                ->select();

            if(!empty($product_imgs)){
                foreach ($product_imgs as $k => $v){
                    if(file_exists($v['img_url'])){
                        @unlink($v['img_url']);
                    }
                }
            }
            db('product_image')->where('product_id','=',$productId)
                ->delete();
        });
    }


    // 生成商品号
    public static function makeProductNo()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $productSn =
            $yCode[intval(date('Y')) - 2017] . time() .rand(11111,99999);
        return $productSn;
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