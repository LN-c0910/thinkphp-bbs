<?php

namespace app\api\controller;

use app\common\ComConstant;
use think\Request;

//收藏夹控制器
class Collection extends BaseController
{
    /**
     * 根据用户id显示收藏列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {

        //签名验证
        if (!$request->post("user_token"))
            return json(["msg"=>"请求参数无效"],400);
        $tokenGetter = tokenGetter($request);
        $checkToken = checkToken($tokenGetter);
        if ($checkToken['code'] != ComConstant::e_api_sign_success)
            return json(["msg"=>$checkToken['res']],400);
        //解析token获取用户id
        $uid = $checkToken['data']->uid;
        if (\app\common\model\User::checkUserState($uid)){
            return json(['msg'=>'您的账号已被封禁,操作失败'],403);
        }
        $collectionByUid = \app\common\model\Collection::listCollectionByUid($uid);

        if ($collectionByUid){
            if ($collectionByUid==ComConstant::user_collection_empty)
                return json(['msg'=>[]]);
            return json(['msg'=>$collectionByUid]);
        }
        return json(['msg'=>'查询失败,请重新登录'],404);

    }

    /**
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 新建用户收藏
     *
     * @param \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //签名验证
        if (!$request->post("user_token")) return json(["msg" => "请求错误,签名不存在"], 400);
        if (!$request->post("tid")) return json(["msg" => "请求参数错误"], 400);
        $tokenGetter = tokenGetter($request);
        $checkToken = checkToken($tokenGetter);
        if ($checkToken['code'] != ComConstant::e_api_sign_success)
            return json(["msg" => $checkToken['res']], 400);
        //解析token获取用户id
        $uid = $checkToken['data']->uid;
        if (\app\common\model\User::checkUserState($uid)){
            return json(['msg'=>'您的账号已被封禁,操作失败'],403);
        }
        //执行新增操作
        $collection = \app\common\model\Collection::addCollection($uid, $request->post("tid"));
        if ($collection) {
            switch ($collection) {
                case ComConstant::collection_already_exist:
                    return json(["msg" => "收藏已存在", 'code' => $collection], 200);
                    break;
                case ComConstant::topic_not_exist:
                    return json(["msg" => "添加收藏失败,该主帖不存在", 'code' => $collection], 403);
                    break;
                default:
                    return json([
                        "msg" => "添加收藏成功",
                        'code' => ComConstant::collection_create_success,
                        "cid" => $collection
                    ], 200);
            }
        }
        return json(["msg" => "添加收藏失败"], 404);
    }

    /**
     * 显示指定的资源
     *
     * @param int $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param \think\Request $request
     * @param int $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }


    /**
     * 根据收藏id删除收藏
     * @param $id int 收藏id
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function delete($id, Request $request)
    {
        //获取user_token
        $token = $request->getInput();
        //签名验证
        if (!$token) return json(["msg" => "请求错误,签名不存在"], 400);
        $checkToken = checkToken($token);
        //是否通过验证
        if ($checkToken['code'] != ComConstant::e_api_sign_success)
            return json(["msg" => $checkToken['res']], 403);
        //解析token获取用户id
        $uid = $checkToken['data']->uid;
        if (\app\common\model\User::checkUserState($uid)){
            return json(['msg'=>'您的账号已被封禁,操作失败'],403);
        }
        $deleteCollection = \app\common\model\Collection::deleteCollection($uid, $id);
        if ($deleteCollection) {
            switch ($deleteCollection) {
                case ComConstant::collection_not_exist:
                    return json(["msg" => "该收藏已被删除"], 200);
                    break;
                case ComConstant::collection_delete_success:
                    return json(["msg" => "删除成功"], 200);
                    break;
                case ComConstant::collection_no_permission:
                    return json(["msg" => "无权操作"], 403);
                default:
                    return json(["msg" => "删除成功"], 200);
            }

        }
        return json(["msg" => "删除失败,请刷新后重试"], 403);
    }
}
