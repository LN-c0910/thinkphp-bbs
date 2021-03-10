<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;


class User extends Controller
{

    /**
     * 启用账号
     * @param $id
     * @return \think\response\Json
     */
    function startUser($id){
        if (\app\common\model\User::openUpUser($id)){
            return json(['msg'=>'解锁成功']);
        }
        return json(['msg'=>'解锁失败'],404);
    }

    /**停用账号
     * @param $id
     * @return \think\response\Json
     */
    function stopUser($id){
        if (\app\common\model\User::lockDownUser($id)){
            return json(['msg'=>'锁定成功']);
        }
        return json(['msg'=>'锁定失败'],404);
    }
}
