<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/21
 * Time: 23:59
 */

namespace app\admin\controller;

use app\admin\validate\CategoryValidate;
use app\admin\validate\ShopTypeAttr;
use catetree\Catetree;

class Type extends Base
{
    private $model;
    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('type');
    }

    public function lst(){
        $typeData=$this->model->select();
        $this->assign([
            'tbData'=>$typeData,
        ]);
        return view('list');
    }

    public function add(){
        return view();
    }

    public function edit($id=0){
        if(intval($id) < 1){
            $this->error("参数不合法");
        }

        $editData = $this->model->find(['id'=>$id]);
        $this->assign([
            'editData' => $editData
        ]);
        return view();
    }

    public function save(){

        if(!request()->post()){
            $this->error("请求失败");
        }
        $validate = (new ShopTypeAttr())->goCheck('type');

        if(!$validate['type']){
            $this->result("",'0',$validate['msg']);
        }

        // 获取请求数据
        $data = input('post.');
        $is_exist_id = empty($data['id']);
        // 判断是否存在同名
        $is_unique = $this->is_unique($data['name'], $is_exist_id ? 0 : $data['id'],'name');
        if($is_unique){
            $this->result('','0','存在同名类型名');
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
        $result = db('type')->delete(intval($id));
        // 删除商品类型下面的商品属性
        db('property')->where(['type_id'=>$id])->delete();
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