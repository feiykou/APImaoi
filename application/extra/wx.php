<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/1
 * Time: 21:17
 */

return [
    // 小程序app_id
    'app_id'               =>      'wxec99ce1d7b61c46b',
    'app_secret'           =>      '3d50d71ebc7e07c36018ae6134fafc69',
    'login_url'            =>      'https://api.weixin.qq.com/sns/jscode2session?'.'appid=%s&secret=%s&js_code=%s&grant_type=authorization_code',
    'access_token_url'     =>      'https://api.weixin.qq.com/cgi-bin/token?'.'grant_type=client_credential&appid=%s&secret=%s'
];