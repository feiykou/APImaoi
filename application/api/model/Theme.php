<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-06-13
 * Time: 20:59
 */

namespace app\api\model;


class Theme extends BaseModel
{
    protected $hidden = [
        'update_time', 'create_time', 'on_sale'
    ];

    public function getMainImgUrlAttr($value,$data){
        $arr = explode(';',$value);
        foreach ($arr as &$val){
            $val = $this->prefixImgUrl($val, $data);
        }
        return $arr;
    }

    public function product(){
        return $this->belongsToMany('product','theme_product','product_id','theme_id')->field('id,name,description,main_img_url');
    }

    /*
     * 获取首页推荐主题
     */
    public static function getIndex($rescid, $count)
    {
        // 获取首页推荐产品id
        $_recoIndexIds = db('rec_item')->where([
            'value_type' => 3,
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
        $result = self::limit($count)
            ->where($data)
            ->with('product')
            ->order(['sort'=>'desc','create_time'=>'desc'])
            ->select();
        return $result;
    }
}