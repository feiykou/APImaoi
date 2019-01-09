<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/7
 * Time: 20:26
 */

namespace app\api\service;


use think\Exception;

class Card
{
    private $ticket_url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=wx_card";
    const TICKET_CACHED_KEY = 'ticket';
    const TICKET_EXPIRE_IN = 7000;


    public function __construct()
    {
        $accessToken = new AccessToken();
        $token = $accessToken->get();
        $this->ticket_url = sprintf($this->ticket_url,$token);
    }

    public function getTicket(){
        $ticket = $this->getTicketFromCache();
        if(!$ticket){
            $this->getFromWxService();
        }
        return $ticket;
    }

    public function getFromWxService(){
        $ticket = curl_get($this->ticket_url);
        if(!$ticket){
            throw new Exception('ticket获取异常');
        }
        if($ticket['errcode'] != 0){
            throw new Exception($ticket['errmsg']);
        }

    }

    public function getTicketFromCache(){
        $ticket = cache(self::TICKET_CACHED_KEY);
        if(!$ticket){
            return null;
        }
        return $ticket;
    }

    public function saveToCache($val){
        cache(self::TICKET_CACHED_KEY, $val, self::TICKET_EXPIRE_IN);
    }

}