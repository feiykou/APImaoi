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

    /*
     * 判断用户是否存在
     * 存在返回uid，不存在返回0
     */
    public static function getByOpenID($openid){
        $user = User::where('openid','=',$openid)
                    ->find();
        return $user;
    }
}