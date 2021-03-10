<?php

namespace app\api\controller;

use app\api\validate\UserLoginValidate;
use app\common\ComConstant;
use app\common\model\User;
use think\Controller;
use think\Db;
use think\Request;


/**
 * 前台用户登录api
 * Class Login
 * @package app\api\controller
 */
class Login extends BaseController
{
    /**
     * 验证用户登录并颁发token
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    function userLogin(Request $request){
        if (!request()->ispost()) return msg_result(ComConstant::e_user_pass_miss,"请输入账号和密码");
        $input = $request->post();
        //转义特殊字符
        foreach ($input as $k=>$v){
            $input[$k] = htmlentities($v);
        }
        $validate = $this->validate($input, UserLoginValidate::class);
        if (true !== $validate){
            return msg_result(ComConstant::e_user_pass_miss,"请输入账号和密码");
        }
        //验证账号,密码,账号状态
        $checkLogin = User::checkLogin($input['username'], $input['password']);
        if (!$checkLogin){
            //验证错误 返回错误信息
            return msg_result(ComConstant::e_user_pass_wrong,User::getErrorInfo());
        }else{
            //验证成功 颁发token并更新最后登录时间 返回登录信息
            $access_token = signToken(User::$userInfo['uid'], User::$userInfo['uname']);
            User::where('uid',User::$userInfo['uid'])->update(['ulastlogin'=>Db::raw('NOW()')]);
            return msg_result(ComConstant::e_api_user_login_success,$access_token->getData());
        }
    }

//    function logout(){
//        $token =  tokenGetter();
//        $usr = checkToken($token);
//        if ($usr['code']==ComConstant::e_api_sign_success){
//            $userInfo = User::get($usr['data']->uid)->field('upassword',true)->find();
//            return json($userInfo);
//        }
//    }

}
