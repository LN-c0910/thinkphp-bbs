<?php

namespace app\admin\validate;

use think\Validate;

/**
 * Class AdminLoginValidate 后台登录验证
 * @package app\admin\validate
 */
class AdminLoginValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
	    'username'=>'require',
        'password'=>'require',
        'vcode'=>'require|captcha|token'
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'username.require'=>'用户名不能为空',
        'password.require'=>'密码不能为空',
        'vcode.require'=>'请输入验证码',
        'vcode.captcha'=>'验证码错误',
        'vcode.token'=>'非法访问'
    ];

}
