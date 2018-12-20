<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/2
 * Time: 18:17
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\User as UserModel;
use app\api\model\UserAddress;
use app\api\validate\AddressNew;
use app\api\service\Token as TokenService;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\SuccessMessage;
use app\lib\exception\UserException;
use think\Db;

class Address extends BaseController
{
    protected $beforeActionList = [
        'checkPrimaryScope' =>  ['only' => 'getuseraddress,createorupdateaddress,editaddress,delete']
    ];

    public function getDefaultAddress(){
        $uid = TokenService::getCurrentUid();
        $userAddress = UserAddress::where(['user_id' => $uid, 'status' => 1])
            ->order('update_time desc')
            ->whereOr(['is_default' => 1])
            ->find();
        return $userAddress;
    }

    /**
     * 获取全部的用户地址
     * @url     /addressAll
     * @http    GET
     * @return false|\PDOStatement|string|\think\Collection
     * @throws UserException
     */
    public function getUserAddress()
    {
        $uid = TokenService::getCurrentUid();
        $userAddress = UserAddress::where([
            'user_id' => $uid,
            'status' => 1
        ])->order([
                'is_default' => 'desc',
                'update_time' => 'desc'
            ])
            ->select();
        if(!$userAddress){
            throw new UserException([
                'msg'   => '用户地址不存在',
                'errorCode' =>  60001
            ]);
        }
        return $userAddress;
    }

    /**
     *
     * @url     /address
     * @http    post
     * @return  SuccessMessage
     * @throws  UserException
     */
    public function createOrUpdateAddress()
    {
        $validate = new AddressNew();
        $validate->goCheck();

        // 根据Token获取uid
        // 根据uid来查找用户数据，判断用户是否存在，如果不存在抛出异常
        // 获取用户从客户端提交的地址信息
        // 根据用户地址信息是否存在，从而判断是添加地址还是更新地址

        $uid = TokenService::getCurrentUid();
        $user = UserModel::get($uid);
        if(!$user){
            throw new UserException();
        }
        if(request()->isPost()){
            $data = input('post.');
        }else{
            $data = input('put.');
        }

        $is_exist_id = !empty($data['id']);
        $dataArray = $validate->getDataByRule($data);

        Db::startTrans();
        try{
            if($data['is_default'] == 1){
                $addressRes = $user->address()->select();
                foreach ($addressRes as $v){
                    if($v['is_default'] == 1){
                        UserAddress::where([ 'id'=> $v['id'], 'status' => 1])->update( ['is_default' => 0]);
                    }
                }
            }

            // 更新数据
            if($is_exist_id){
                $update = $this->update($data['id'], $dataArray);
                Db::commit();
                return $update;
            }

            $user->address()->save($dataArray);
            Db::commit();
            return json(new SuccessMessage(),201);
        }catch (Exception $ex){
            Db::rollback();
            throw $ex;
        }
    }


    public function editAddress($id){
        (new IDMustBePositiveInt())->goCheck();
        $uid = TokenService::getCurrentUid();
        $user = UserModel::get($uid);
        if(!$user){
            throw new UserException();
        }
        $addressData = $user->address;
        $currentAddress = [];
        foreach ($addressData as $k=>$v){
            if($v['id'] == $id){
                $currentAddress = $v;
            }
        }
        if(empty($currentAddress)){
            throw new UserException([
                'code'  =>  404,
                'msg'   =>  '用户收货地址不存在',
                'errorCode' =>  60001
            ]);
        }
        return $currentAddress;
    }

    public function update($id, $dataArray){
        $address = new UserAddress();
        $result = $address->save($dataArray, ['id'=>intval($id)]);
        return $result;
    }

    public function delete($id){
        (new IDMustBePositiveInt())->goCheck();
        $uid = TokenService::getCurrentUid();
        $user = UserModel::get($uid);
        if(!$user){
            throw new UserException();
        }
        $address = new UserAddress();
        $result = $address->where(['id' => $id, 'user_id'=> $uid])->update(['status'=>0]);
        if(!$result){
            throw new UserException([
                'code'  =>  404,
                'msg'   =>  '用户收货更新失败',
                'errorCode' =>  60002
            ]);
        }

        return json(new SuccessMessage(),201);

    }
}