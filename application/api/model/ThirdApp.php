<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/23
 * Time: 18:16
 */

namespace app\api\model;


class ThirdApp extends BaseModel
{
    public static function check($ac, $se){
        $data = [
            'app_id' => $ac,
            'app_secret' => md5($se)
        ];

        $app = self::where($data)->find();
        return $app;
    }
}