<?php

namespace app\api\controller;

use app\common\ComConstant;
use app\common\model\Collection;
use app\common\model\Msg;
use app\common\model\Reply;
use app\common\model\Section;
use app\common\model\Topic;
use app\common\model\User;
use think\Controller;
use think\Db;
use think\Request;


class Index extends BaseController
{
    /**
     * 根据token获取用户信息
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserInfo(Request $request)
    {
        $token =  tokenGetter($request);

        $usr = checkToken($token);
        if ($usr['code']==ComConstant::e_api_sign_success){
            $uid = $usr['data']->uid;
            if (User::checkUserState($uid)){
                return null;
            }
            $userInfo = User::field('upassword',true)->where('uid',$usr['data']->uid)->find();
            $userInfo['utopic'] = Topic::where('tuid',$uid)->count();
            $userInfo['ureply'] = Reply::where('ruid',$uid)->count();
            $userInfo['collections'] = Collection::where('cuid',$uid)->count();
            $userInfo['unewmsg'] = Msg::where('status',1)
                                        ->where('acceptid',$uid)
                                        ->count();
            $userInfo['unewreply'] = Reply::where('rread','=',0)
                                            ->where('rtarget',$uid)
                                            ->count();
            return json($userInfo);
        }
        return null;


    }

    /**
     * 加载论坛首页信息
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(){
        $content = [];
        //板块名称
        $content['sectionInfo'] = Section::field(
            ['sid'=>'section_id',
                'sname'=>'section_name',
                'simg'=>'section_img',
                'sclickcount'=>'section_click',
                Db::raw('(select count(*) from topic 
                where 
                topic.tsid = sid and topic.check=1 and topic.tdeletetime is null) 
                AS section_topic')
            ])->group('sid')->select();
        //总主题数
        $content['totalTopic'] = Topic::where(['check'=>true])->count();
        return json($content);

    }

    /**
     *  获取新回复数
     * @param $uid
     * @param Request $request
     * @return \think\response\Json
     */
    public function getMsgAndReplyNum($uid,Request $request){
        if (!$request->isPost()) return json(["msg"=>"请求错误"],400);
        if (!$request->post("user_token")) return json(["msg"=>"请求错误"],400);
        $tokenGetter = tokenGetter($request);
        $usr = checkToken($tokenGetter);
        if ($usr['code']==ComConstant::e_api_sign_success){
            $uid = $usr['data']->uid;
            $userInfo['unewmsg'] = Msg::where('status','=',1)
                ->where('acceptid','=',$uid)
                ->count();
            $userInfo['unewreply'] = Reply::where('rread','=',0)
                ->where('rtarget','=',$uid)
                ->count();
            return json($userInfo);
        }
        return json($usr);
    }
}
