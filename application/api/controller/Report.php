<?php

namespace app\api\controller;

use app\api\validate\ReportValidate;
use app\common\ComConstant;
use function Sodium\add;
use think\Controller;
use think\Request;

/**
 * Class Report 举报记录控制器
 * @package app\api\controller
 */
class Report extends BaseController
{
    /**
     * 创建举报
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public  function save(Request $request){
        //签名验证
        $input = $request->post();
        $validate = $this->validate($input, ReportValidate::class);
        if (!$validate) return json(['msg'=>'请求参数错误'],400);
        $tokenGetter = tokenGetter($request);
        $checkToken = checkToken($tokenGetter);
        if ($checkToken['code'] != ComConstant::e_api_sign_success)
            return json(["msg" => $checkToken['res']], 400);
        //解析token获取用户id
        $uid = $checkToken['data']->uid;
        if (\app\common\model\User::checkUserState($uid)){
            return json(['msg'=>'您的账号已被封禁,操作失败'],403);
        }
        $rid = $request->post('rid'); //获取回复帖id
        $type = $request->post('report_type'); //获取举报类型
        $reason = $request->post('report_reason'); //举报理由 可为空
        $addReport = \app\common\model\Report::addReport($rid, $uid, $type, $reason);
        if ($addReport){
            switch ($addReport){
                case ComConstant::create_report_success:
                    return json(["msg"=>"举报成功","code"=>ComConstant::create_report_success]);
                    break;
                case ComConstant::report_already_exist:
                    return json(["msg"=>"请勿重复举报","code"=>ComConstant::report_already_exist],403);
                    break;
                case ComConstant::reply_not_exist:
                    return json(["msg"=>"举报失败,该回复帖已被删除","code"=>ComConstant::reply_not_exist],404);
                    break;
                default:
                    return json(["msg"=>"举报成功","code"=>ComConstant::create_report_success]);
            }
        }
        return json(["msg"=>"举报失败"],500);
    }

}
