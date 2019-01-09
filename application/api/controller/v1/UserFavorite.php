<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/9
 * Time: 12:14
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\Favorite as FavoriteValidate;
use app\api\service\Token as TokenService;
use app\api\model\UserFavorite as UserFavoriteModel;
use app\api\model\User as UserModel;
use app\api\validate\PagingParameter;
use app\lib\exception\SuccessMessage;
use app\lib\exception\UserException;

class UserFavorite extends BaseController
{

    protected $beforeActionList = [
        'checkPrimaryScope' => ['only' => 'add,flist,delete'],
    ];

    public function add(){
        if(request()->isPost()){
            $uid = $this->_public();
            $product_id = input('post.')['product_id'];
            $isExist = UserFavoriteModel::checkIsExist($uid,$product_id);
            if($isExist){
                return json(new SuccessMessage(),201);
            }
            $result = UserModel::saveFavo($uid,['product_id' => $product_id]);
            if(!$result){
                throw new UserException([
                    'code'  =>  404,
                    'msg'   =>  '用户收藏失败',
                    'errorCode' =>  60003
                ]);
            }
            return json(new SuccessMessage(),201);
        }
    }

    public function flist($page=1,$size=10){
        (new PagingParameter())->goCheck();
        $uid = TokenService::getCurrentUid();
        if(!UserModel::isExistUser($uid)){
            throw new UserException();
        }
        $data = UserFavoriteModel::listFavo($uid,$page,$size);
        return $data;
    }

    public function delete(){
        $uid = $this->_public();
        $product_id = input('put.')['product_id'];
        $result = UserFavoriteModel::updateFavo($uid,$product_id,0);
        if(!$result){
            throw new UserException([
                'code'  =>  404,
                'msg'   =>  '用户收藏删除失败',
                'errorCode' =>  60004
            ]);
        }
        return json(new SuccessMessage(),201);
    }

    public function checkFavo(){
        $uid = $this->_public();
        $product_id = input('get.')['product_id'];
        $result = UserFavoriteModel::where([
            'user_id' => $uid,
            'product_id' => $product_id,
            'status' => 1
        ])->find();
        if($result){
            return json(new SuccessMessage(),201);
        }else{
            return json(new SuccessMessage([
                'msg' => 'error'
            ]),201);
        }
    }

    private function _public(){
        (new FavoriteValidate())->goCheck();
        $uid = TokenService::getCurrentUid();
        if(!UserModel::isExistUser($uid)){
            throw new UserException();
        }
        return $uid;
    }

}