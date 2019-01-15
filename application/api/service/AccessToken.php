<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/27
 * Time: 9:00
 */

namespace app\api\service;


use think\Exception;

class AccessToken
{
    private $tokenUrl;
    const TOKEN_CACHED_KEY = 'access';
    const TOKEN_EXPIRE_IN = 7000;

    public function __construct()
    {
        $url = config('wx.access_token_url');
        $url = sprintf($url,config('wx.app_id'),config('wx.app_secret'));
        $this->tokenUrl = $url;
    }

    public function get(){
        $token = $this->getFromCache();
        if(!$token){
            return $this->getFromWxService();
        }else{
            return $token;
        }
    }

    private function getFromWxService(){
        $token = curl_get($this->tokenUrl);
        $token = json_decode($token,true);
        if(!$token){
            throw new Exception('获取AccessToken异常');
        }
        if(!empty($token['errcode'])){
            throw new Exception($token['errmsg']);
        }
        $this->saveToCache($token['access_token']);
        return $token['access_token'];
    }

    private function getFromCache(){
        $token = cache(self::TOKEN_CACHED_KEY);
        if(!$token){
            return null;
        }
        return $token;
    }

    public function saveToCache($token){
        cache(self::TOKEN_CACHED_KEY, $token,self::TOKEN_EXPIRE_IN);
    }
}