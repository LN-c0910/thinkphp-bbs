<?php

namespace app\common\controller;




use app\api\controller\BaseController;
use app\common\ComConstant;
use app\common\model\User;
use think\Request;


/**
 * 图片上传类
 * Class UploadImg
 * @package app\common\controller
 */

class UploadImg extends BaseController
{
    //上传帖子图片
    function uploadImg(Request $request){
        if (!$request->isPost()) return json(["msg"=>"请求错误"],400);
        if (!$request->post("user_token")) return json(["msg"=>"请求错误"],400);
        if (checkToken(tokenGetter($request))['code'] != ComConstant::e_api_sign_success)
            return json(checkToken(tokenGetter($request)),400);
        $img = $request->file('img');
        // 移动到public/static/t_images/ 目录下
        $info = $img->move(get_public_path().'/static/t_images');
        if($info){
          return json(["code"=>200,"url"=>$info->getSaveName()]);
        }else{
            // 上传失败获取错误信息
            return $img->getError();
        }
//        return "upload";
    }

    //上传用户头像
    function uploadFace(Request $request){
        if (!$request->isPost()) return json(["msg"=>"请求错误"],400);
        if (!$request->post("user_token")) return json(["msg"=>"请求错误"],400);
        if (checkToken(tokenGetter($request))['code'] != ComConstant::e_api_sign_success)
            return json(checkToken(tokenGetter($request)),400);
        $uid = checkToken(tokenGetter($request))['data']->uid;
        $img = $request->file('file');
        // 移动到public/static/t_images/ 目录下
        $info = $img->move(get_public_path().'/static/uface');
        if($info){
            $usr = User::get($uid);
            $usr->uface=$info->getSaveName();
            $usr->save();
            return json(["code"=>200,"url"=>$info->getSaveName()]);
        }else{
            // 上传失败获取错误信息
            return $img->getError();
        }
//        return "upload";
    }


}
