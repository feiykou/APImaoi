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

class Property extends Base
{
    private $model;
    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('property');
    }

    public function lst(){
        $typeId = input('id');
        if($typeId){
            $map['type_id'] = ['=',$typeId];
        }else{
            $map = 1;
        }

        $propertyData = db('property')->alias('p')
            ->field('p.*,t.name as type_name')
            ->join('type t','p.type_id = t.id')
            ->where($map)
            ->order('p.id DESC')
            ->paginate(6);
//        var_dump($propertyData);
        $this->assign([
            'tbData'=>$propertyData,
        ]);
        return view('list');
    }

    public function add(){
        $categoryRes = db('type')->select();
        $this->assign([
            'CategoryRes' => $categoryRes,
        ]);
        return view();
    }

    public function edit($id=0){
        if(intval($id) < 1){
            $this->error("参数不合法");
        }
        $categoryRes = db('type')->select();
        $editData = $this->model->find(intval($id));
        $this->assign([
            'editData' => $editData,
            'categoryRes' => $categoryRes
        ]);
        return view();
    }

    public function save(){

        if(!request()->post()){
            $this->error("请求失败");
        }
        $validate = (new ShopTypeAttr())->goCheck('property');

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
        $data['values'] = str_replace('，',',',$data['values']);
        $is_exist_id = empty($data['id']);
        // 判断是否存在同名
        $is_unique = $this->is_unique($data['name'], $is_exist_id ? 0 : $data['id'],'name');
        $type_is_unique = $this->is_unique($data['type_id'], $is_exist_id ? 0 : $data['id'],'type_id');

        if($is_unique && $type_is_unique){
            $this->result('','0','存在同类型属性名');
        }

        // 更新数据
        if(!$is_exist_id){
            return $update = $this->update($data);
        }

        // 添加数据
        $result = $this->model->save($data);
        if($result){
            $this->result(url('lst',['id'=>$data['type_id']]),'1','添加成功');
        }else{
            $this->result('','0','添加失败');
        }
    }

    public function update($data){
        $result = $this->model->save($data,['id' => intval($data['id'])]);
        if($result){
            $this->result(url('lst',['id'=>$data['type_id']]),'1','更新成功');
        }else{
            $this->result('','0','更新失败');
        }
    }

    public function del($id){
        $result = db('property')->delete(intval($id));
        // 返回状态码
        if($result){
            $this->result($_SERVER['HTTP_REFERER'], 1, '删除完成');
        }else{
            $this->result($_SERVER['HTTP_REFERER'], 0, '删除失败');
        }
    }

    // 异步获取指定属性下的属性
    public function ajaxGetAttr(){
        $typeId = input('type_id');
        $attrRes = $this->model->where(['type_id'=>$typeId])->select();
        return $attrRes;
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