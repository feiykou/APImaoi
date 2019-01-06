<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/2
 * Time: 13:12
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\WxCode;
use app\api\service\WxCode as WxCodeService;
use app\api\model\Product as ProductModel;

class WxController extends BaseController
{
//    protected $beforeActionList = [
//        'checkSuperScope' => ['only' => 'wxcode']
//    ];

    public function wxcode(){
        if(request()->isPut()){
            $validate = new WxCode();
            $validate->goCheck();

            // 获取数据
            $data = input('put.');
            $WxCodeService = new WxCodeService();
            $result = $WxCodeService->getCode($data);
            if($result){
                ProductModel::updateWxCode($data['product_id'],$result['savePath']);
                $result = config('APISetting.img_prefix').$result['path'];
                return $result;
            }
        }
    }
}