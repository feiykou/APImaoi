<?php
/**
 * Created by 七月.
 * Author: 七月
 * 微信公号：小楼昨夜又秋风
 * 知乎ID: 七月在夏天
 * Date: 2017/3/5
 * Time: 17:59
 */

namespace app\api\controller;


use app\api\service\Token;
use app\lib\exception\FailMessage;
use app\lib\exception\SuccessMessage;
use think\Controller;
use think\Exception;

class BaseController extends Controller
{
    protected function checkExclusiveScope()
    {
        Token::needExclusiveScope();
    }

    protected function checkPrimaryScope()
    {
        Token::needPrimaryScope();
    }

    protected function checkSuperScope()
    {
        Token::needSuperScope();
    }

    public function uploadImg(){
        if($_FILES['file']['tmp_name']){
            $file = request()->file('file');
            $info = $file->move('upload/API');
            if($info){
                $img_url = '/' . str_replace('\\','/',$info->getSaveName());
            }
        }

        if(empty($img_url)){
            throw new Exception('图片上传失败');
        }
        return [
            'img_url' => $img_url
        ];
    }

    public function delFile(){
        $delsrc = input('delsrc');
        $delsrc = DEL_FILE_URL . $delsrc;
        if(file_exists($delsrc)){
            if(@unlink($delsrc)){
               return json(new SuccessMessage(),201);
            }else{
                return json(new FailMessage(),201);
            }
        }else{
            return json(new FailMessage(),201);
        }
    }
}