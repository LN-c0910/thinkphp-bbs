<?php

namespace app\admin\validate;

use think\Validate;

class FeedBackValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
	    'feedback'=>'require|number',
        'rid'=>'require|number',
        'uid'=>'number',
        'reporter'=>'require|number'
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [];
}
