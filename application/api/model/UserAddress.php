<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/2
 * Time: 20:20
 */

namespace app\api\model;



class UserAddress extends BaseModel
{
    protected $hidden = ['delete_time','create_time','update_time','user_id'];
}