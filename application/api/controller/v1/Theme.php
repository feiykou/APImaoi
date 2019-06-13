<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-06-13
 * Time: 17:46
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\ProductRescCount;
use app\api\model\Theme as ThemeModel;
use app\lib\exception\ThemeException;

class Theme extends BaseController
{
    /**
     * 获取首页推荐主题
     * @url     /theme/recoIndex?count=:count
     * @http    get
     * @param   int $count
     * @return  false|\PDOStatement|string|\think\Collection
     * @throws  ThemeException
     */
    public function getRecoIndex($rescid= 10,$count = 4)
    {
        (new ProductRescCount())->goCheck();
        $themes = ThemeModel::getIndex($rescid,$count);
        if($themes->isEmpty()){
            throw new ThemeException();
        }
        $themes = $themes->hidden([
            'content', 'head_img_url', 'product.pivot'
        ]);
        return $themes;
    }
}