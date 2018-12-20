<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/21
 * Time: 23:59
 */

namespace app\admin\controller;

use app\admin\validate\CategoryValidate;
use catetree\Catetree;

class Category extends Base
{
    private $model;
    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('category');
    }

    public function lst(){
        $category=new Catetree();
        if(request()->isPost()){
            $data=input('post.');
            $category->cateSort($data['sort'],$this->model);
            $this->success('排序成功！',url('lst'),'',1);
        }
        $categoryRes=$this->model->order('sort DESC')->select();

        $categoryRes=$category->catetree($categoryRes);
        $this->assign([
            'tbData'=>$categoryRes,
        ]);
        return view('list');
    }

    public function add(){
        // 分类
        $category = new Catetree();
        $categoryRes = $this->model->order('sort DESC')->select();
        $categoryRes = $category->catetree($categoryRes);

        // 商品推荐位
        $productRecposRes = db('recpos')->where('type','=',2)->select();
        $this->assign([
            'CategoryRes' => $categoryRes,
            'productRecposRes' => $productRecposRes
        ]);
        return view();
    }

    public function edit($id=0){
        if(intval($id) < 1){
            $this->error("参数不合法");
        }
        $catetree = new Catetree();
        $categoryRes = $this->model->order('sort DESC')->select();
        $categoryRes = $catetree->catetree($categoryRes);
        $sonids = $catetree->childrenids($id,$this->model);
        $sonids[] = intval($id);
        $editData = $this->model->find(['id'=>$id]);

        // 产品推荐位
        $categoryRecposRes = db('recpos')->where('type','=',2)->select();
        // 当前产品相关推荐位
        $_curCategoryRecposRes = db('rec_item')->where([
            'value_id' => intval($id),
            'value_type' => 2
        ])->select();
        $curCategoryRecposRes = [];
        foreach ($_curCategoryRecposRes as $k=>$v){
            $curCategoryRecposRes[] = $v['recpos_id'];
        }
        $this->assign([
            'editData' => $editData,
            'CategoryRes' => $categoryRes,
            'sonids' => $sonids,
            'curCategoryRecposRes' => $curCategoryRecposRes,
            'categoryRecposRes' => $categoryRecposRes
        ]);
        return view();
    }

    public function save(){

        if(!request()->post()){
            $this->error("请求失败");
        }
        $validate = (new CategoryValidate())->goCheck('save');

        if(!$validate['type']){
            $this->result("",'0',$validate['msg']);
        }

        // 获取请求数据
        $data = input('post.');
        $is_exist_id = empty($data['id']);
        // 判断是否存在同名
        $is_unique = $this->is_unique($data['cate_name'], $is_exist_id ? 0 : $data['id'],'cate_name');
        if($is_unique){
            $this->result('','0','存在同名分类名');
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
            $this->result('','0','更新失败');
        }
    }

    public function del($id){
        $catetree = new Catetree();
        $sonids = $catetree->childrenids($id,$this->model);
        $sonids[] = intval($id);
        $result = db('category')->delete($sonids);
        // 返回状态码
        if($result){
            $this->result($_SERVER['HTTP_REFERER'], 1, '删除完成');
        }else{
            $this->result($_SERVER['HTTP_REFERER'], 0, '删除失败');
        }
    }
//
//
//    //删除
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