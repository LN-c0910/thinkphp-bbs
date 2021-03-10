<?php

namespace app\admin\controller;

use app\admin\validate\UpdateTopicStatusValidate;
use think\Controller;
use think\Request;

class Topic extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 更新帖子状态
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function updateStatusByTid($tid,Request $request)
    {
        //
        if (!$request->isPost()){
            return json(['msg'=>'请求错误'],400);
        }
        $input = $request->post();
        $validate = $this->validate($input, UpdateTopicStatusValidate::class);
        if (!$validate){
            return json(['msg'=>'请求参数错误'],400);
        }
        if(\app\common\model\Topic::updateTopicStatus($tid,$request->post('status_code'))){
            return json(['msg'=>'更新成功'],200);
        }
    }

    /**
     * 更新帖子板块
     * @param $tid
     * @param $tsid
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function updateTopicSection($tid,$tsid){
        $updateTopicSection = \app\common\model\Topic::updateTopicSection($tid, $tsid);
        if ($updateTopicSection){
            return json(['msg'=>'更新成功'],200);
        }
        return json(['msg'=>'更新失败'],500);
    }




    /**
     * 管理员软删除指定帖子
     * @param $id
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        $uid = session('admin.userInfo')['uid'];
        if (!$uid){
            return json(['msg'=>'非法访问'],403);
        }
        $deleteTopicByTid = \app\common\model\Topic::deleteTopicByTid($uid, $id);
        if ($deleteTopicByTid){
            return json(['msg'=>'删除成功']);
        }
        return json(['msg'=>'删除失败,请刷新后重试'],404);
    }

    /**
     * 管理员硬删除指定帖子
     * @param $tid
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function destroyTopic($tid)
    {
        $uid = session('admin.userInfo')['uid'];
        if (!$uid){
            return json(['msg'=>'非法访问'],403);
        }
        $destroyTopicByTid = \app\common\model\Topic::destroyTopicByTid($tid);
        if ($destroyTopicByTid){
            return json(['msg'=>'删除成功']);
        }
        return json(['msg'=>'删除失败,请刷新后重试'],404);
    }

    /**
     * 管理员恢复指定帖子
     * @param $tid
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function restoreTopic($tid)
    {
        $uid = session('admin.userInfo')['uid'];
        if (!$uid){
            return json(['msg'=>'非法访问'],403);
        }
        $restoreTopicByTid = \app\common\model\Topic::restoreTopicByTid($tid);
        if ($restoreTopicByTid){
            return json(['msg'=>'恢复成功']);
        }
        return json(['msg'=>'操作失败,请刷新后重试'],404);
    }

    /**
     * 审核主帖
     * @param $tid int 主帖id
     * @return \think\response\Json
     */
    public function approveTopic($tid){
        if(\app\common\model\Topic::approveTopic($tid)){
            return json(['msg'=>'审核成功'],200);
        }
        return json(['msg'=>'操作失败'],500);
    }
}
