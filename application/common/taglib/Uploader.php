<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/30 0030
 * Time: 下午 17:37
 */

namespace app\common\taglib;


use think\template\TagLib;

class Uploader extends TagLib
{
    protected $tags = [
        'webuploadercss' => ['attr' => '', 'close' => 0],
        'webuploaderjs'  => ['attr' => '', 'close' => 0],
        'webuploader'     => ['attr' => 'btnVal,uploadId,filePickerId'],
    ];

    public function tagWebuploadercss(){
        $parseStr = '<link href="__STATIC__/admin/plugins/webuploader/webuploader.css" rel="stylesheet">';
        return $parseStr;
    }

    /**
     *  wrap:  图片最外层盒子dom对象
        filePicker: 添加图片按钮,
        uploadId: wrap盒子id,
        server: 上传服务器地址,
        addImgVal: 添加图片名字,
        width: 图片缩略图宽度,
        height: 图片缩略图高度,
        fileNumLimit: 限制上传图片数量,
        multiple: 是否多图上传
     * @return string
     */
    public function tagWebuploaderjs($tag,$content){
        $parseStr = '<script type="text/javascript" src="__STATIC__/admin/plugins/webuploader/webuploader.js"></script>';
        $parseStr .= '<script type="text/javascript" src="__STATIC__/admin/plugins/webuploader/feiy_upload.js"></script>';
        return $parseStr;
    }
    public function tagWebuploader($tag,$content){
        $btnVal = isset($tag['btnVal'])?$tag['btnVal']:'开始上传';
        $uploadId = isset($tag['uploadId'])?$tag['uploadId']:'uploader';

        $parseStr = <<<php
            <div id="$uploadId" class="uploader-item">
                <div class="uploader_btns">
                    <div class="filePicker"></div><div class="uploadBtn">$btnVal</div>
                </div>
                <!--用来存放item-->
                <div class="queueList">$content</div>
            </div>
php;

        return $parseStr;
    }
}