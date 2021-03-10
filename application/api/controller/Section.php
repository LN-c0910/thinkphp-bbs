<?php

namespace app\api\controller;
use think\Request;
class Section extends BaseController
{
    //根据section_id 增加板块点击数
    function addclick(Request $request){
        if ($request->ispost()){
            $sid = $request->post("section_id");
            $count = $request->post("count");
            if (!($sid&&$count)) return json(["msg"=>"请求错误"],400);
            $addClickBySid = \app\common\model\Section::addClickBySid($sid, $count);
            if ($addClickBySid){
                return json(["msg"=>"success"],200);
            }
            return json(["msg"=>"请求错误"],400);
        }
        return json(["msg"=>"请求错误"],400);
    }

}
