<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/11
 * Time: 15:12
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\Comment;
use app\api\service\Token as TokenService;
use app\api\model\User as UserMode;
use app\api\validate\Common;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\SuccessMessage;
use app\lib\exception\UserException;
use app\api\model\UserComment as UserCommentModel;
use app\api\model\Order as OrderModel;
use think\Db;
use think\Exception;

class UserComment extends BaseController
{

    protected $beforeActionList = [
        "checkPrimaryScope" => ['only'=> 'addComment,productComments,UserComments']
    ];


    /**
     * 用户添加评论
     * @url   user/commentAdd
     * @http  POST
     * @return \think\response\Json
     * @throws UserException
     */
    public function addComment(){
        if(request()->isPost()){
            $validate = new Comment();
            $validate->goCheck();
            $dataArray = $validate->getDataByRule(input('post.'));

            // 获取uid
            $uid = TokenService::getCurrentUid();

            Db::startTrans();
            try{
                $orderid = $dataArray['comments'][0]['order_id'];
                UserMode::saveComment($uid, $dataArray['comments']);
                OrderModel::changeOrderStatus($uid,$orderid,OrderStatusEnum::COMMENT);
                Db::commit();
            } catch (Exception $ex){
                Db::rollback();
                throw new UserException([
                    'code'  =>  404,
                    'msg'   =>  '评论添加失败',
                    'errorCode' =>  60006
                ]);
            }

            return json(new SuccessMessage(),201);
        }
    }

    /**
     * 产品评论
     * @url   product/comment
     * @http  GET
     * @param $id  产品id
     * @return array|false|\PDOStatement|string|\think\Collection
     */
    public function productComments($id,$size=10, $page=1){
        (new Common())->goCheck('pageId');
        $data = UserCommentModel::getProductComment($id,$size, $page);
        return $data;
    }

    /**
     * 用户评价
     * @url  user/comment
     * @http  GET
     * @return array|false|\PDOStatement|string|\think\Collection
     */
    public function UserComments($id, $size=10, $page=1){
        (new Common())->goCheck('pageId');
        $uid = TokenService::getCurrentUid();
        $data = UserCommentModel::getUserComment($uid, $id,$size, $page);
        return $data;
    }
}