<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/27
 * Time: 23:56
 */

namespace app\admin\validate;


class ThemeValidate extends BaseValidate
{

    protected $rule = [
        ['name','require|unique:theme', '主题名称不能为空！|主题名称不能重复'],
        ['main_img_url','require','主题封面图不能为空！'],
        ['content','require','主题内容不能为空！']
    ];
}