<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/21
 * Time: 23:59
 */

namespace app\admin\controller;

use app\admin\validate\CategoryValidate;
use app\admin\validate\ProductValidate;
use app\admin\validate\ShopTypeAttr;
use catetree\Catetree;

class Product extends Base
{
    private $model;
    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('product');
    }

    public function lst(){
        $join = [
            ['category c','p.category_id=c.id','LEFT'],
            ['type t', 'p.type_id=t.id','LEFT']
        ];
        $productRes = db('product')->alias('p')
            ->field('p.*,c.cate_name,t.name as type_name')
            ->join($join)
            ->group('p.id')
            ->order('p.id DESC')
            ->paginate(6);
        $this->assign([
            'productRes'=>$productRes,
        ]);
        return view('list');
    }

    public function add(){
        // 会员级别数据
        $mlRes = db('member_level')->field('id,name')->select();
        // 获取类型
        $typeRes=db('type')->select();
        // 商品推荐位
        $productRecposRes = db('recpos')->where('type','=',1)->select();
        // 商品分类
        $Category=new Catetree();
        $CategoryObj=db('Category');
        $CategoryRes=$CategoryObj->order('sort DESC')->select();
        $CategoryRes=$Category->Catetree($CategoryRes);
        $this->assign([
            'mlRes' => $mlRes,
            'typeRes'=>$typeRes,
            'CategoryRes'=>$CategoryRes,
            'productRecposRes' => $productRecposRes
        ]);
        return view();
    }

    public function edit($id=0){
        if(intval($id) < 1){
            $this->error("参数不合法");
        }
        $product_id = intval($id);
        // 获取当前产品基本信息
        $productData = $this->model->find($product_id);
        // 获取相册
        $productImgData = db('product_image')->where('product_id','=',$product_id)->select();
        // 产品推荐位
        $productRecposRes = db('recpos')->where('type','=',1)->select();
        // 当前产品相关推荐位
        $_curProductRecposRes = db('rec_item')->where([
            'value_id' => $product_id,
            'value_type' => 1
        ])->select();
        $curProductRecposRes = [];
        foreach ($_curProductRecposRes as $k=>$v){
            $curProductRecposRes[] = $v['recpos_id'];
        }
        // 会员级别数据
        $mlRes = db('member_level')->field('id,name')->select();
        // 获取产品类型
        $typeRes = db('type')->select();
        // 会员价格
        $_mbRes = db('member_price')->where('product_id','=',$product_id)->select();
        $mbArr = [];
        foreach ($_mbRes as $k=>$v){
            $mbArr[$v['mlevel_id']] = $v;
        }

        // 查询当前产品类型所有的属性信息
        $propRes = db('property')->where('type_id','=',$productData['type_id'])->select();
        // 查询当前产品拥有的产品属性product_prop
        $_ppropRes = db('product_prop')->where('product_id','=',$productData['id'])->select();
        $ppropRes = [];

        foreach ($_ppropRes as $k => $v){
            $ppropRes[$v['prop_id']][] = $v;
        }
        // 商品分类
        $Category=new Catetree();
        $CategoryObj=db('Category');
        $CategoryRes=$CategoryObj->order('sort DESC')->select();
        $CategoryRes=$Category->Catetree($CategoryRes);
        $this->assign([
            'productData' => $productData,
            'productImgData' => $productImgData,
            'typeRes' => $typeRes,
            'mlRes' => $mlRes,
            'mbArr' => $mbArr,
            'propRes' => $propRes,
            'ppropRes' => $ppropRes,
            'CategoryRes'=>$CategoryRes,
            'productRecposRes' => $productRecposRes,
            'curProductRecposRes' => $curProductRecposRes
        ]);
        return view();
    }

    public function save(){
        if(!request()->post()){
            $this->error("请求失败");
        }
        $validate = (new ProductValidate())->goCheck();

        if(!$validate['type']){
            $msg = '';
            if(is_array($validate['msg'])){
                foreach ($validate['msg'] as $key=>$value){
                    $msg.=$value;
                }
            }else{
                $msg = $validate['msg'];
            }

            $this->result("",'0',$msg);
        }

        // 获取请求数据
        $data = input('post.');
        $is_exist_id = empty($data['id']);
        // 判断是否存在同名
        $is_unique = $this->is_unique($data['name'], $is_exist_id ? 0 : $data['id'],'name');
        if($is_unique){
            $this->result('','0','存在同名产品名');
        }

        // 更新数据
        if(!$is_exist_id){
            return $update = $this->update($data);
        }
        // 添加数据
        $result = $this->model->save($data);
        if($result){
            $this->result(url('lst'),'1','添加成功');
        }else{
            $this->result('','0','添加失败');
        }
    }

    public function update($data){
        $result = $this->model->save($data,['id' => intval($data['id'])]);
        if($result){
            $this->result(url('lst'),'1','更新成功');
        }else{
            $this->result(url('lst'),'0','无更新');
        }
    }

    public function del($id){
        $result = model('product')->destroy($id);
        // 返回状态码
        if($result){
            $this->result($_SERVER['HTTP_REFERER'], 1, '删除完成');
        }else{
            $this->result($_SERVER['HTTP_REFERER'], 0, '删除失败');
        }
    }

    public function edituploadImg(){
        if($_FILES['file']['tmp_name']){
            $file = request()->file('file');
            $info = $file->move('upload');
            if($info){
                $img_url = DS . 'upload' . DS . $info->getSaveName();
            }
        }
        if(!empty($img_url)){
            $json = [
                "code"=> 0, //0表示成功，其它失败
                "msg" => "上传成功", //提示信息 //一般上传失败后返回
                "data" =>[
                   "src"=>$img_url,
                    "title" => '图片'
                ]
            ];
            return $json;
        }else{
            $json = [
                "code"=> 1, //0表示成功，其它失败
                "msg" => "上传失败", //提示信息 //一般上传失败后返回
                "data" =>[
                    "src"=> ''
                ]
            ];
            return $json;
        }
    }

    /**
     *
     * 功能重点：
     * 1、提交前端页面name=name名称[属性id][] 的方式添加多个属性id下的多个值
     * 2、对于只要有一个属性没有值，则相对应的数据无效，不会添加到数据库
     * 3、库存对应的属性会以属性id逗号分隔的状态存进数据库
     *
     *
     * @param $id
     * @return \think\response\View|void
     *
     */
    public function stock($id){
        if(request()->isPost()){
            $stock = db('product_stock');
            $stock->where('product_id','=',$id)->delete();
            $data = input('post.');

            $productProp = isset($data['product_prop']) ? $data['product_prop'] : [];
            $stock_num = $data['stock_num'];
            $stock_total = 0;

            foreach ($stock_num as $k=>$v){

                $strArr = [];
                foreach ($productProp as $k1=>$v1){
                    if(intval($v1[$k]) <= 0){
                        continue 2;
                    }
                    $strArr[] = $v1[$k];
                }
                $stock_total += $v;
                sort($strArr);
                $strArr = implode(',',$strArr);
                $stock->insert([
                    'product_id' => $id,
                    'stock_num' => $v,
                    'price' => $data['price'][$k],
                    'market_price' => $data['market_price'][$k],
                    'product_prop' => $strArr
                ]);
            }
            db('product')->where('id','=',$id)->update(['stock_total'=>$stock_total]);
            $this->result(url('lst'),'1','添加成功');
        }

        // 获取产品对应的属性
        $radioAttrRes = $this->model->getProductPropArr($id);

        // 获取商品的库存信息
        $stockDatas = db('product_stock')->where('product_id','=',$id)->select()->toArray();
        $this->assign([
            'radioAttrRes' => $radioAttrRes,
            'stockDatas' => $stockDatas,
            'product_id' => $id
        ]);
        return view();
    }


    /* 删除图片 */
    public function ajaxDelPic($id){
        $productImage = db('product_image');
        $product_img = $productImage->find($id);
        @unlink($product_img.img_url);
        $del = $productImage->delete($id);
    }


//    // 删除
//    public function del($id=-1){
//        if(request()->isPost()){
//            $id = request()->post()['idsArr'];
//            if($id == []){
//                $this->error("无选中的数据！");
//            }
//        }else{
//            if(intval($id)<1){
//                $this->error("参数不合法");
//            }
//        }
//
//        if(!is_array($id)){
//            $id = [$id];
//        }
//        $result = db('brand')->delete($id);
//        // 返回状态码
//        if($result){
//            $this->result($_SERVER['HTTP_REFERER'], 1, '删除完成');
//        }else{
//            $this->result($_SERVER['HTTP_REFERER'], 0, '删除失败');
//        }
//    }
}