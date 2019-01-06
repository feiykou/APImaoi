<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/27
 * Time: 9:57
 */

namespace app\api\service;


use think\Exception;

class WxMessage
{
    private $sendUrl = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=%s";

    protected $tplID;
    protected $page;
    protected $formID;
    protected $data;
    protected $emphasisKeyword;

    public function __construct()
    {
        $accessToken = new AccessToken();
        $token = $accessToken->get();
        $this->sendUrl = sprintf($this->sendUrl,$token);
    }

    protected function sendMessage($openID){
        $data = [
            'touser' => $openID,
            'template_id' => $this->tplID,
            'page' => $this->page,
            'form_id' => $this->formID,
            'data' => $this->data,
            'emphasis_keyword' => $this->emphasisKeyword
        ];
        $result = curl_post($this->sendUrl,$data);
        $result = json_decode($result, true);
        if($result['errcode'] == 0){
            return true;
        }else{
            throw new Exception('模板消息发送失败,' . $result['errmsg']);
        }
    }
}