<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/30
 * Time: 17:27
 */

namespace app\api\model;


class Property extends BaseModel
{
    protected $hidden = [
        'update_time', 'create_time', 'values', 'type_id', 'type','id'
    ];

}