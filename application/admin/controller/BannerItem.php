<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/21
 * Time: 23:59
 */

namespace app\admin\controller;

use app\admin\validate\BannerValidate;
use catetree\Catetree;

class BannerItem extends Base
{
    private $model;
    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('banner_item');
    }

    public function lst(){

        /*
         * 获取轮播图位和id
         * */
        $bannerBitData = model('banner')->select();
        $banner_default_id = isset($bannerBitData[0]) ? $bannerBitData[0]['id'] : 0;
        // 获取轮播图位id
        $banner_id = input('param.banner_id',$banner_default_id,'intval');
        $bannerBitArr = [];
        foreach ($bannerBitData as $val){
            if($banner_id == $val['id']){
                $bannerBitArr = $val;
            }
        }
        // 如果没有轮播位，则$bannerId默认为0
        if(!isset($banner_id)){
            $banner_id = 0;
        };

        /*
         * 获取跳转类型数组
         */
        $typeArr = config('BannerItem.type');

        if(request()->isPost()){
            $category=new Catetree();
            $data=input('post.');
            $category->cateSort($data['sort'],$this->model);
            $this->success('排序成功！',url('lst',['banner_id'=>$data['banner_id']]),'',1);
        }
        /*
         * 获取轮播图数据
         */
        $bannerItemData = $this->model->getBannerData($banner_id);
        return view('list',[
            'bannerBitDatas' => $bannerBitData,
            'bannerBitArr'  => $bannerBitArr,
            'bannerItemData' => $bannerItemData,
            'typeArr'        => $typeArr
        ]);
    }

    public function add(){
        $typeArr = config('BannerItem.type');
        $bannerBit = db('banner')->select();

        $this->assign([
            'typeArr' => $typeArr,
            'bannerBit' => $bannerBit
        ]);
        return view();
    }

    public function edit($id=0){
        if(intval($id) < 1){
            $this->error("参数不合法");
        }
        $editData = $this->model->find(['id'=>$id]);
        $typeArr = config('BannerItem.type');
        $bannerBit = db('banner')->select();

        $this->assign([
            'editData' => $editData,
            'typeArr' => $typeArr,
            'bannerBit' => $bannerBit
        ]);
        return view();
    }

    public function save(){

        if(!request()->post()){
            $this->error("请求失败");
        }
        $validate = (new BannerValidate())->goCheck('save');

        if(!$validate['type']){
            $this->result("",'0',$validate['msg']);
        }

        // 获取请求数据
        $data = input('post.');
        $is_exist_id = empty($data['id']);
        // 判断是否存在同名
        $is_unique = $this->is_unique($data['name'], $is_exist_id ? 0 : $data['id'],'name');
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
        $result = $this->model->where('id','=',$id)->delete();
        // 返回状态码
        if($result){
            $this->result($_SERVER['HTTP_REFERER'], 1, '删除完成');
        }else{
            $this->result($_SERVER['HTTP_REFERER'], 0, '删除失败');
        }
    }


    public function uploadVideo()
    {
        if($_FILES['file']['tmp_name']){
            $file = request()->file('file');
            $info = $file->move('upload/videos');
            if($info){
                $video_url = '/' . str_replace('\\','/',$info->getSaveName());
            }
        }
        if(!empty($video_url)){
            return $this->result($video_url,'1','上传成功','json');
        }else{
            return $this->result('','2','上传失败','json');
        }

//        return [
//            "code" => 0
//            , "msg" => ""
//            , "data" => $_FILES
//        ];

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