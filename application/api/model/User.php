<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/1
 * Time: 21:58
 */

namespace app\api\model;


class User extends BaseModel
{
//    protected $autoWriteTimestamp = true;
    /*
     * 用户和地址是一对多的关系
     */
    public function address(){
        return $this->hasMany('UserAddress','user_id','id');
    }

    public function favorite(){
        return $this->hasMany('user_favorite','user_id','id');
    }

    public function comments(){
        return $this->hasMany('user_comment','user_id','id');
    }

    public function getAvatarImgAttr($value,$data){
        return $this->prefixAPIUrl($value, $data);
    }


    /**
     * 获取用户信息
     * @url
     * @http
     * @param $openid
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public static function getUserData($uid){
        $result = User::field('nickName')->find($uid);
        return $result;
    }

    /*
     * 判断用户是否存在
     * 存在返回uid，不存在返回0
     */
    public static function getByOpenID($openid){
        $user = User::where('openid','=',$openid)
                    ->find();
        return $user;
    }

    /**
     * 判断用户是否存在 uid
     */
    public static function isExistUser($uid){
        $result = User::get($uid);
        if($result){
            return true;
        }
        return false;
    }



    // 添加收藏
    public static function saveFavo($uid, $data=[]){
        $result = User::get($uid)
            ->favorite()
            ->save($data);
        return $result;
    }

    /**
     *
     * @url
     * @http
     * @param $uid
     * @param array $data
     * @return int
     */
    public static function saveComment($uid, $data =[]){
        $result = User::get($uid)
            ->comments()
            ->saveAll($data);
        return $result;
    }



}