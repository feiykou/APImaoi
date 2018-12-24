<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/23
 * Time: 21:20
 */

namespace app\admin\controller;


use app\admin\model\UserCms;
use app\admin\validate\LoginValidate;
use think\Controller;

class Login extends Controller
{
    public function index(){
        if(session('uid') && session('uname')){
            return $this->redirect('/');
        }
        return view();
    }

    public function submitLogin(){
        if(request()->isPost()){
            (new LoginValidate())->goCheck();
            $data = input('post.');
            $status = UserCms::getLoginStatus($data);
            if($status == 1){
                return json('用户不存在！@','401');
            }else if($status == 3){
                return json('密码错误！','401');
            }else{
                return json(true,'201');
            }
        }
    }

    public function loginOut(){
        session(null);
        $this->redirect('/login');
    }
}