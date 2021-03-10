<?php

namespace app\admin\controller;

use app\admin\validate\FeedBackValidate;
use app\common\model\Feedback;
use think\Controller;
use think\Request;

class Report extends Controller
{
    //处理举报
    function feedback($id,Request $request){
        if (!$request->isPost()){
            return json(['msg'=>'请求错误'],403);
        }
        $input = $request->post();
        $validate = $this->validate($input, FeedBackValidate::class);
        if (!$validate){
            return json(['msg'=>'请求错误'],403);
        }
        $feedBackReport = \app\common\model\Report::feedBackReport(
            $input['feedback'],
            $input['rid'],
            $input['uid'],
            $input['reporter'],
            $id);
        if ($feedBackReport){
            return json(['msg'=>'处理成功'],200);
        }
        return json(['msg'=>'处理失败'],500);
    }
}
