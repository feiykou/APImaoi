<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/30
 * Time: 10:11
 */

namespace app\api\model;


class Product extends BaseModel
{
    protected $hidden = [
        'update_time', 'create_time', 'on_sale', 'category_id', 'theme_id','product_imgs'
    ];

    protected function getContentAttr($value, $data){
        //<img src="\upload\20181112\ebb863e07c6db47d197de19ac6ad1972.png" alt="图片">
        //<img src="\upload\20181112\bab4fc172cc6e6c1bceef8e92fe22b34.png" alt="图片">

        $pattern = '/src="(.*?)"/';
        $index = preg_match_all($pattern,$value, $result);
        $imgArr = $result[1];
        if($index != 0){
            foreach ($imgArr as &$val){
                $val = str_replace('\\','/',$val);
                $val = config('APISetting.img_prefix').$val;
            }
        }
        return $imgArr;
    }

    // 产品和图片一对多关系
    public function productImage(){
        return $this->hasMany('product_image','product_id','id');
    }

    public function productProp(){
        return $this->belongsToMany('property','product_prop','prop_id','product_id');
    }

    public function productStock(){
        return $this->hasMany('product_stock','product_id','id');
    }

    public function getMainImgUrlAttr($value,$data){
        return $this->prefixImgUrl($value, $data);
    }

    /*
     * 获取首页推荐产品
     */
    public static function getIndex($count)
    {
        // 获取首页推荐产品id
        $_recoIndexIds = db('rec_item')->where([
            'value_type' => 1,
            'recpos_id'  => 6
        ])->field('value_id')->select();
        $recoIndexIds = [];
        foreach ($_recoIndexIds as $k=>$v){
            $recoIndexIds[] = $v['value_id'];
        }
        $data = [
            'on_sale' => 1,
            'id'      => ['in',$recoIndexIds]
        ];
        $products = self::limit($count)
            ->where($data)
            ->order('create_time desc')
            ->select();
        return $products;
    }

    /*
     * 获取产品详情
     */
    public static function getProductDetail($id){
        $data = [
            'id'      => $id
        ];
        $products = self::where($data)
            ->with(['productImage'=>function($query){
                    $query->order('sort desc');
                }
            ])
            ->find();
        return $products;
    }



    public static function getProductOrProStock($ids){
        $result = self::with('productStock')
            ->select($ids);
        return $result;
    }

    /*
    * 获取产品单选属性和库存
    */
    public static function getProductProp($pid){
        $stock_prop = db("product_stock")
            ->where('product_id','=',$pid)
            ->select();
        $_propData = Product::get($pid)
            ->productProp()
            ->where('type','=',1)
            ->select();
        foreach ($_propData as $v){
            $pivot = $v['pivot']->hidden(['prop_id','prop_price','product_id']);
            if($pivot['img_url']){
                $pivot['img_url'] = config('APISetting.img_prefix').IMG_URL.$pivot['img_url'];
            }
            $propData[$pivot['id']] = $pivot;
        }

        $stockArr = [];
        foreach ($stock_prop as $v){
            $stock['stock'] = $v['stock_num'];
            $stock['price'] = $v['price'];
            $stock['market_price'] = $v['market_price'];
            $stock['pids'] = $v['product_prop'];
            $stock['attrVal'] = self::getRelStockProp($propData, $v['product_prop']);
            array_push($stockArr, $stock);
        }
        return $stockArr;
    }

    private static function getRelStockProp($_propData, $propIds=''){
        $propData = [];
        $propIdArr = explode(',',$propIds);

        foreach ($propIdArr as $v){
            if(isset($_propData[$v])){
                $propData[] = $_propData[$v];
            }
        }
        return $propData;
    }
}