<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/19
 * Time: 10:29
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\Token as TokenService;
use app\api\validate\User as UserValidate;
use app\api\model\User as UserModel;
use app\lib\exception\SuccessMessage;
use app\lib\exception\UserException;

class User extends BaseController
{
    public function updateUser(){
        if(request()->isPost()){
            $validate = new UserValidate();
            $validate->goCheck();

            $uid = TokenService::getCurrentUid();
            $user = UserModel::get($uid);
            if(!$user){
                throw new UserException([
                    'code'  =>  404,
                    'msg'   =>  '用户创建失败',
                    'errorCode' =>  60003
                ]);
            }

            $data = input('post.');
            $dataArray = $validate->getDataByRule($data);
            $result = $user->update($dataArray,['id' => $uid]);
            if(!$result){
                throw new UserException([
                    'code'  =>  404,
                    'msg'   =>  '用户创建失败',
                    'errorCode' =>  60003
                ]);
            }
            return json(new SuccessMessage(),201);
        }


    }
}