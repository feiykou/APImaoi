<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/23
 * Time: 18:06
 */

namespace app\api\service;


use app\api\model\ThirdApp as ThirdAppModel;
use app\lib\exception\TokenException;

class AppToken extends Token
{
    public function get($ac, $se){
        // 查询数据库，用户名和密码是否存在
        $app = ThirdAppModel::check($ac, $se);
        if(!$app){
            throw new TokenException([
                'msg' => '授权失败',
                'errorCode' => '10004'
            ]);
        }else{
            $scope = $app->scope;
            $uid = $app->id;
            $values = [
                'scope' => $scope,
                'uid' => $uid
            ];
            $token = $this->saveToCache($values);
            return $token;
        }
    }

    public function saveToCache($values){
        $token = self::generateToken();
        $expire_in = config('APISetting.token_expire_in');
        $result = cache($token,json_encode($values),$expire_in);
        if(!$result){
            throw new TokenException([
                'msg' => '服务器缓存异常',
                'errorCode' => 10005
            ]);
        }
        return $token;
    }
}