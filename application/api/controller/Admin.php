<?php

namespace app\api\controller;


use app\common\ComConstant;
use app\common\model\User;
use think\Request;
use BaseController;

/**
 * Class Admin 管理员操作api
 * @package app\api\controller
 */
class Admin extends \app\api\controller\BaseController
{

    /**
     * 管理员更新帖子状态
     * @param $status
     * @param $tid
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    function updateStatusByTid($status,$tid,Request $request){
        if (empty($status) || empty($tid)) return json(['msg'=>'参数错误'],400);
        $tokenGetter = tokenGetter($request);
        if (!$tokenGetter) return json(['msg'=>'签名不存在','code'=>ComConstant::e_api_sign_miss],400);
        $user = checkToken($tokenGetter);
        if ($user['code']==ComConstant::e_api_sign_success){
            $uid = $user['data']->uid;
            $roleid = User::field('roleid')->get($uid);
            if ($roleid['roleid'] != 1) return json(['msg'=>'权限不足'],401);
            $updateTopicStatus = \app\common\model\Topic::updateTopicStatus($tid, (int)$status);
            if (!$updateTopicStatus) return json(['msg'=>'参数或服务器错误'],500);
            return json(['msg'=>'操作成功'],200);
        }else{
            return json($user,401);
        }

    }
}
