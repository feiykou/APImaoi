<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/21
 * Time: 23:59
 */

namespace app\admin\controller;

use app\admin\validate\ThemeValidate;
use catetree\Catetree;

class Theme extends Base
{
    private $model;
    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('theme');
    }

    public function lst(){
        $tree=new Catetree();
        if(request()->isPost()){
            $data=input('post.');
            $tree->cateSort($data['sort'],$this->model);
            $this->success('排序成功！',url('lst'),'',1);
        }
        $themeData = $this->model->getAllTheme();
        $this->assign([
            'themeData' => $themeData
        ]);
        return view('list');
    }

    public function add(){
        // 商品推荐位  2:代表主题
        $rescBitArr = config('RescBit');
        if(isset($rescBitArr[2])){
            $RecposRes = db('recpos')->where('type','=',2)->select();
        }else{
            $RecposRes = [];
        }
        // 全部产品
        $productDatas = $this->model->getAllProduct();
        foreach($productDatas as &$products){
            $products['main_img_url'] = str_replace("\\","/",$products['main_img_url']);
        }
        $this->assign([
            'productDatas' => $productDatas,
            'recposRes'    => $RecposRes
        ]);
        return view();
    }

    public function edit($id=0){
        if(intval($id) < 1){
            $this->error("参数不合法");
        }
        $theme_id = intval($id);
        // 获取当前产品基本信息
        $themeData = $this->model->find($theme_id);

        // 获取选中的推进位
        $selRescData = $this->model->getRescData($theme_id);
        $selRescIds = [];
        foreach ($selRescData as $v){
            $selRescIds[] = $v['id'];
        }

        // 商品推荐位  2:代表主题
        $rescBitArr = config('RescBit');
        if(isset($rescBitArr[2])){
            $RecposRes = db('recpos')->where('type','=',2)->select();
        }else{
            $RecposRes = [];
        }
        // 全部产品
        $productDatas = $this->model->getAllProduct();
        foreach($productDatas as &$products){
            $products['main_img_url'] = str_replace("\\","/",$products['main_img_url']);
        }

        // 获取选中产品
        $selProduct = $this->model->getSelProduct($theme_id);
        $selProductData = [];
        foreach ($selProduct as $v){
            $selProductData[$v['id']] = $v->pivot;
        }


        $this->assign([
            'themeData'      => $themeData,
            'selRescIds'     => $selRescIds,
            'selProductData' => $selProductData,
            'productDatas'   => $productDatas,
            'recposRes'      => $RecposRes
        ]);
        return view();
    }

    public function save(){
        if(!request()->post()){
            $this->error("请求失败");
        }
        $validate = (new ThemeValidate())->goCheck();

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
        $result = model('theme')->destroy($id);
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

            foreach ($stock_num as $k=>$v){

                $strArr = [];
                foreach ($productProp as $k1=>$v1){
                    if(intval($v1[$k]) <= 0){
                        continue 2;
                    }
                    $strArr[] = $v1[$k];
                }

                sort($strArr);
                $strArr = implode(',',$strArr);

                $stock->insert([
                    'product_id' => $id,
                    'stock_num' => $v,
                    'product_prop' => $strArr
                ]);
            }
            $this->result(url('lst'),'1','添加成功');
        }

        // 获取产品对应的属性
        $radioAttrRes = $this->model->getProductPropArr($id);
        // 获取商品的库存信息
        $stockDatas = db('product_stock')->where('product_id','=',$id)->select();
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