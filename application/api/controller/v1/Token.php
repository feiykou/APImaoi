<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/1
 * Time: 20:54
 */

namespace app\api\controller\v1;


use app\api\service\AppToken;
use app\api\service\UserToken;
use app\api\service\Token as TokenService;
use app\api\validate\AppTokenGet;
use app\api\validate\TokenGet;
use app\lib\exception\ParameterException;
use app\lib\exception\TokenException;


class Token
{
    /**
     * 获取token
     * @url     /token/user
     * @http    post
     * @param   string $code
     * @return  array
     */
    public function getToken($code='')
    {
        (new TokenGet())->gocheck();
        $wx = new UserToken($code);
        $token = $wx->get();
        return [
            'token' => $token
        ];
    }

    /**
     * 验证token是否存在
     * @url     /token/verify
     * @http    post
     * @param   string $token
     * @return  array
     * @throws  ParameterException
     */
    public function verifyToken($token=''){
        if(!$token){
            throw new ParameterException([
                'token不允许为空'
            ]);
        }
        $valid = TokenService::verifyToken($token);
        return [
            'isValid' => $valid
        ];
    }


    /**
     * 第三方应用获取令牌
     * @url   /app_token?
     * $POST  ac=:ac  se=:secret
     */
    public function getAppToken($ac='', $se=''){
        (new AppTokenGet())->goCheck();
        // 获取令牌
        $app = new AppToken();
        $token = $app->get($ac,$se);
        return [
            'token' => $token
        ];
    }
}