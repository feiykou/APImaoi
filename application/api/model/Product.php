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
        $arr = explode(';',$value);
        foreach ($arr as &$val){
            $val = $this->prefixImgUrl($val, $data);
        }
        return $arr;
    }

    public function getWxCodeAttr($value,$data){
        return $this->prefixWxCodeUrl($value, $data);
    }

    /**
     * 获取搜索结果
     */
    public static function getSearchResult($params,$size=10,$page=1){
        $data = [
            'on_sale' => 1,
            'name' => ['like','%'.$params['q'].'%']
        ];
        $order = [
            'sort' => 'desc',
            'create_time' => 'desc'
        ];


        if(isset($params['cateid']) && intval($params['cateid']) != 0){
            $data['category_id'] = intval($params['cateid']);
        }
        if(isset($params['price']) && strlen($params['price']) > 1){
            $priceArr = explode('-',$params['price']);
            if(count($priceArr) == 1){
                $val = $priceArr[0];
                $priceArr[0] = 0;
                $priceArr[1] = $val;
            }
            $data['price'] = ['between',[intval($priceArr[0]),intval($priceArr[1])]];
        }


        $data = self::where($data)
            ->order($order)
            ->paginate($size,false,['page'=>$page]);
        return $data;
    }

    /*
     * 获取分类下的产品
     */
    public static function getProductsByCateID($cateIdArr=[]){
        $data = [
            'category_id' => ['in',$cateIdArr],
            'on_sale' => 1
        ];
        $productData = self::where($data)
            ->order('create_time desc')
            ->select();
        return $productData;
    }

    /*
     * 获取首页推荐产品
     */
    public static function getIndex($rescid, $count)
    {
        // 获取首页推荐产品id
        $_recoIndexIds = db('rec_item')->where([
            'value_type' => 1,
            'recpos_id'  => $rescid
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
            ->order(['sort'=>'desc','create_time'=>'desc'])
            ->select();
        return $products;
    }

    /**
     * 分类筛选
     * @url
     * @http
     * @param $data
     * @param int $size
     * @param int $page
     * @return \think\Paginator
     */
    public static function filterCate($data,$size=10,$page=1){
        $result = self::where($data)
            ->where('on_sale',1)
            ->paginate($size, true, ['page' => $page]);
        return $result;
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

    public static function getRecIndexCate($recposId, $cateId){
        $data = [
            'recpos_id' => $recposId,
            'value_type' => 1
        ];
        $_cateRes = db('rec_item')->where($data)
            ->select();
        $cateIdArr = [];
        foreach ($_cateRes as $k => $v){
            array_push($cateIdArr,$v['value_id']);
        }
        $data = self::where([
            'id'=>['in',$cateIdArr],
            'category_id' => $cateId,
            'on_sale'=> 1])
            ->select();
        return $data;
    }

    public static function updateWxCode($id,$url){
        $result = self::where('id','=',$id)
            ->update(['wx_code'=>$url]);
        return $result;
    }
}