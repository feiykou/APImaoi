<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/23
 * Time: 15:18
 */

namespace app\admin\controller;


class AppLogin extends Base
{
    public function index(){
        return $this->fetch();
    }
}