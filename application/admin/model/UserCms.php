<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/23
 * Time: 21:31
 */

namespace app\admin\model;


use app\api\model\BaseModel;

class UserCms extends BaseModel
{


    public static function getLoginStatus($loginData){
        $data = [
            'name' => $loginData['ac'],
            'status' => 1
        ];
        $userData = self::getUser($data);
        if($userData){
            if($userData['pwd'] == md5($loginData['se'])){
                session('uid',$userData['id']);
                session('uname',$userData['name']);
                return 2; // 登录成功
            }else{
                return 3; // 登录密码错误
            }
        }else{
            return 1; // 用户名不存在
        }
    }

    public static function getUser($data){
        $userData = self::where($data)->find();
        return $userData;
    }
}