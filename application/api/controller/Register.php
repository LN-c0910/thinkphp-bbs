<?php

namespace app\api\controller;


use app\api\validate\UserLoginValidate;
use app\common\ComConstant;
use app\common\model\User;
use think\Controller;
use think\Request;


/**
 * Class Register 前台用户注册类
 * @package app\api\controller
 */
class Register extends BaseController
{
    /**
     * 前台用户注册api
     * @param Request $request
     * @return \think\response\Json
     */
    function userRegister(Request $request){
        if (!request()->ispost()) return msg_result(ComConstant::e_user_pass_miss,"请输入账号和密码");
        $input = $request->post();
        //转义特殊字符
        foreach ($input as $k=>$v){
            $input[$k] = htmlentities($v);
        }
        if ($input['password'] != $input['password_sec']){
            return msg_result(ComConstant::e_api_user_register_failed,"两次密码不一致");
        }
        $validate = $this->validate($input, UserLoginValidate::class);
        if (true !== $validate){
            return msg_result(ComConstant::e_user_pass_miss,"用户名最多9位,密码最少6位");
        }
        $checkRegister = User::checkRegister($input['username'],$input['password']);
        if (!$checkRegister){
            return msg_result(ComConstant::e_api_user_register_failed,User::getErrorInfo());
        }else{
            return msg_result(ComConstant::e_api_user_register_success,"注册成功");
        }
    }
}
