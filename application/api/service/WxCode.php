<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/2
 * Time: 12:47
 */

namespace app\api\service;


use think\Exception;

class WxCode
{
    private $reqUrl = "https://api.weixin.qq.com/wxa/getwxacode?access_token=%s";

    private $default = ['width','auto_color','line_color','is_hyaline','path'];

    public function __construct()
    {
        $accessToken = new AccessToken();
        $token = $accessToken->get();
        $this->reqUrl = sprintf($this->reqUrl,$token);
    }

    public function getCode($params){
        $data = [];
        foreach ($this->default as $val){
            if(array_key_exists($val,$params)){
                $data[$val] = $params[$val];
            }
        }
        $result = curl_post($this->reqUrl,$data);
        if(strpos($result,'errcode')){
            $result = json_decode($result,true);
            throw new Exception('二维码生成失败,' . $result['errmsg']);
        }else{
            $result = $this->CreateImgByCode($result);
            return $result;
        }
    }

    private function CreateImgByCode($stream){
        $data = 'image/png;base64,'. base64_encode($stream);
        $imageName = rand(1111,9999).'.png';
        if(strstr($data,',')){
            $image = explode(',', $data);
            $image = $image[1];
        }
        $date = date("Ymd",time());
        // 返回客户端路劲
        $path = 'upload/wxcode/'.$date;
        // 保存数据库路径
        $savePath = $date . '/' . $imageName;
        if(!is_dir($path)){
            mkdir($path, 0777, true);
        }
        $imageSrc = $path . '/' . $imageName; // 图片路径
        $res = file_put_contents(ROOT_PATH.'public/'.$imageSrc,base64_decode($image));
        if(!$res){
            throw new Exception('图片生成失败');
        }else{
            return [
                'path' => '/'.$imageSrc,
                'savePath' => $savePath
            ];
        }
    }
}