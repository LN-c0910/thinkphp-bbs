<?php

namespace app\api\controller;


use app\api\validate\PasswordValidate;
use app\common\ComConstant;
use think\Request;

class User extends BaseController
{
    function updatepwd(Request $request){
        //token验证
        $tokenGetter = tokenGetter($request);
        if (!$tokenGetter) return json(['msg'=>'token不存在'],400);
        $usr = checkToken($tokenGetter);
        if (!$usr['code'] == ComConstant::e_api_sign_success){
            return json($usr,401);
        }
        $uid = $usr['data']->uid;
        $input = $request->post();
        // 参数验证
        $validate = $this->validate($input, PasswordValidate::class);
        if (!$validate) return json(['msg'=>'密码最少6位'],400);
        //数据库密码验证
        $password = \app\common\model\User::updateUserPassword($uid, $input['upassword'], $input['newpassword']);
        if (!$password) return json(['msg'=>\app\common\model\User::getErrorInfo()],400);
        return json(['msg'=>'密码修改成功'],200);

    }
}
