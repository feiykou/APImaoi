<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/27
 * Time: 23:56
 */

namespace app\admin\validate;


class BannerValidate extends BaseValidate
{

    protected $rule = [
        ['name','require', '名称必须添加！|商品名称不能重复'],
        ['img_url','require','轮播图必须添加！'],
        ['key_word','number','跳转id必须是数字'],
        ['url_type','require','轮播图类型必须添加！'],
        ['type','require','跳转类型必须添加！'],
    ];

    protected $scene = [
        'bannerItem' => ['name','img_url','key_word','url_type','type']
    ];
}