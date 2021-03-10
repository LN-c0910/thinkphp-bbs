<?php

namespace app\admin\controller;

use think\Controller;

/**
 * Class Reply 后台评论管理控制器
 * @package app\admin\controller
 */
class Reply extends Controller
{
    //删除回复
    function delete($id){
        $uid = session('admin.userInfo')['uid'];
        if (!$uid){
            return json(['msg'=>'非法访问'],403);
        }
        if(\app\common\model\Reply::deleteReplyByRid($id,$uid)){
            return json(['msg'=>'删除成功']);
        }
        return json(['msg'=>'操作失败'],404);
    }
    //恢复回复
    function restore($id){
        $uid = session('admin.userInfo')['uid'];
        if (!session('admin.userInfo')||!$uid){
            return json(['msg'=>'非法访问'],403);
        }
        if (\app\common\model\Reply::restoreReplyByRid($id,$uid)) {
            return json(['msg'=>'恢复成功']);
        }
        return json(['msg'=>'操作失败'],404);
    }
}
