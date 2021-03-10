<?php

namespace app\api\controller;

use app\api\validate\ReplyValidate;
use app\common\ComConstant;
use think\Controller;
use think\Request;

class Reply extends BaseController
{


    /**
     * 分页获取评论列表
     * @param $tid
     * @param $page
     * @param int $rows
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index($tid,$page,$rows = 5)
    {

        if (is_numeric($tid) && is_numeric($page)){
            if (!\app\common\model\Topic::isExistTopic($tid)) {
                return json(['msg' => '暂无数据'], 404);
            }else{
                $reply = \app\common\model\Reply::listReplyByTid($tid, $page,$rows);
                if (!$reply){
                    return json(['msg'=>'服务端错误'],500);
                }else{
                    if (empty($reply)){
                        return json(['msg'=>'暂无数据'],404);
                    }
                    $total = \app\common\model\Reply::where([
                        'rtid'=>$tid
                    ])->count();
                    return json(['msg'=>'获取成功','data'=>$reply,'total'=>$total],200);
                }
            }

        }
        return json(['msg'=>'参数错误'],400);
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
     * 发表评论
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function save(Request $request)
    {
        $tokenGetter = tokenGetter($request);
        if (!$tokenGetter) return json(['msg'=>'签名不存在'],400);
        $usr = checkToken($tokenGetter);
        if ($usr['code']==ComConstant::e_api_sign_success){
            $uid = $usr['data']->uid;
            if (\app\common\model\User::checkUserState($uid)){
                return json(['msg'=>'您的账号已被封禁,操作失败'],403);
            }
            $input = $request->post();
            $validate = $this->validate($input, ReplyValidate::class);
            // 验证参数
            if (!$validate) return json(['msg'=>'参数错误'],400);
            //判断是否包括父回复贴id
            if (!$request->post('rrid') || $request->post('rrid') == 0){
                $rrid = 0;
            }else{
                $rrid = $request->post('rrid');
            }

            if (!\app\common\model\Topic::isExistTopic($input['rtid'])){
                return json(['msg'=>'主帖已被删除,发表失败'],404);
            }
            $addReply = \app\common\model\Reply::addReply(
                $input['rtid'], $uid, $input['rtarget'], $input['rcontents'],$rrid);
            if (!$addReply) return json(['msg'=>'服务器错误,回复失败'],500);
            return json(['msg'=>'发表成功','data'=>$addReply]);
        }else{
            return json($usr,401);
        }

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
     * 点赞跟帖
     * @param Request $request
     * @param int $status
     * @return \think\response\Json
     */
    public function updateLikeByRid(Request $request, int $status)
    {
        //
        if (empty($request->post("rid"))) return json(['msg'=>'参数错误'],400);

        $updateReplyLike = \app\common\model\Reply::updateReplyLike($request->post("rid"), $status);
        if (!$updateReplyLike){
            return json(['msg'=>'找不到数据'],404);
        }
        return json(['msg'=>'点赞成功','data'=>$updateReplyLike],200);
    }

    /**
     * 删除指定rid回复贴
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id,Request $request)
    {
        //获取user_token
        $token = $request->getInput();
        //签名验证
        if (!$token) return json(["msg"=>"请求错误,签名不存在"],400);
        $checkToken = checkToken($token);
        //是否通过验证
        if ($checkToken['code'] != ComConstant::e_api_sign_success)
            return json(["msg"=>$checkToken['res']],403);
        //解析token获取用户id
        $uid = $checkToken['data']->uid;
        if (\app\common\model\User::checkUserState($uid)){
            return json(['msg'=>'您的账号已被封禁,操作失败'],403);
        }
        $deleteReplyByRid = \app\common\model\Reply::deleteReplyByRid($id, $uid);
        if ($deleteReplyByRid){
            return json(["msg"=>"删除成功"],200);
        }

        return json(["msg"=>"删除失败,请刷新后重试"],403);
    }
}
