<?php

namespace app\api\controller;

use app\api\validate\TopicValidate;
use app\common\ComConstant;
use app\common\model\BaseModel;
use think\Db;
use think\Request;
use app\api\controller\BaseController;


/**
 * Class Topic 主贴api类
 * @package app\api\controller
 */

class Topic extends BaseController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
    }


    /**
     * 根据板块id查询主贴信息
     * @param $section_id
     * @param $page
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getTopicBySection($section_id,$page,Request $request){
        //是否查询精华帖子
        $good = [0,1];
        if ($request->get("good")==1){
            $good = [1];
        }
        if ($section_id == "index"){
            $topic_list = \app\common\model\Topic::getAllTopic($page,5,$good);
            $rows = \app\common\model\Topic::where([
                'good'=>$good,
                'tdeletetime'=>null
            ])->count();
            if (!sizeof($topic_list)) return json(['list'=>[],'totalPages'=>1]);
            return json(['list'=>$topic_list,'totalPages'=>ceil($rows/5)]);
        }
        if (is_numeric($section_id)){
            $topic_list =\app\common\model\Topic::getTopicBySid($section_id,$page,5,$good);
            if (!sizeof($topic_list)) return json(['list'=>[],'totalPages'=>1]);
            $rows = \app\common\model\Topic::where([
                'tsid'=>$section_id,
                'good'=>$good,
                'tdeletetime'=>null
            ])->count();
            return json(['list'=>$topic_list,'totalPages'=>ceil($rows/5)]);
        }
        return json(["msg"=>"无数据"],404);
    }


    /**
     * 点赞主帖
     * @param int $status
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function updatelike(int $status,Request $request){
        $tid = $request->post("tid");
        if (empty($tid)) return json(['msg'=>'请求参数错误'],400);
        if (!empty(\app\common\model\Topic::onlyTrashed()->find($tid))){
            return json(['msg'=>'暂无数据'],404);
        }
        $likecount = \app\common\model\Topic::updatelikeByTid($tid, $status);
        if ($likecount){
            return json($likecount);
        }
        return json(['msg'=>'服务器错误'],500);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建主题帖
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //签名验证
        if (!$request->post("user_token")) return json(["msg"=>"请求错误,签名不存在"],400);
        $input = $request->post();
        $tokenGetter = tokenGetter($request);
        $checkToken = checkToken($tokenGetter);
        if ($checkToken['code'] != ComConstant::e_api_sign_success)
            return json($checkToken,400);
        //解析token获取用户id
        $uid = $checkToken['data']->uid;
        if (\app\common\model\User::checkUserState($uid)){
            return json(['msg'=>'您的账号已被封禁,操作失败'],403);
        }
        //后台验证
        $validate = $this->validate($input, TopicValidate::class);
        if (!$validate) return json(['msg'=>'请求参数错误'],400);

        //开启事务
        BaseModel::beginTrans();
        try {
            //新建主帖添加到topic表
            \app\common\model\Topic::addTopicByUid($uid, $input['tsid'], $input['ttopic'], $input['tcontent']);
            // 更新该板块主帖数
            \app\common\model\Section::updateTopic($input['tsid'],1);
            //提交事务
            BaseModel::commitTrans();
        } catch (\Exception $e) {
            //回滚
            BaseModel::rollbackTrans();
            return json(['msg'=>'服务器错误'],500);
        }
        return json(['msg'=>'发表成功']);


    }

    /**
     * 根据tid获取帖子相关信息
     * @param int $id 帖子id
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function read($id)
    {
//        $model = \app\common\model\Topic::onlyTrashed()->find($id);
        if (!\app\common\model\Topic::isExistTopic($id)){
            return json(['msg'=>'无数据'],404);
        }
        $collection = Db::table('topic')
            ->alias('t')
            ->field('t.tid,t.top,t.good,t.ttopic,t.tcontents,t.tlikecount,t.ttime,
                    t.tmodifytime,t.collections,t.forcetop,s.sid,
                    s.sname,u.uname,u.upoint,u.uface,u.uid')
            ->join(['section' => 's'], 't.tsid=s.sid')
            ->join(['user' => 'u'], 't.tuid=u.uid')
            ->where(['t.tid'=>$id,
                't.tdeletetime'=>null])
            ->select();
        $countReplyByTid = \app\common\model\Reply::countReplyByTid($id);
        if (empty($collection) || empty($collection[0])){
            return json(['msg'=>'无数据'],404);
        }
        $collection[0]['treplycount'] = $countReplyByTid;
        return json($collection[0]);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除主题帖
     * @param $id
     * @param Request $request
     * @return \think\response\Json
     */
    public function delete($id,Request $request)
    {
        //获取user_token
        $token = $request->getInput();
        //签名验证
        if (!$token) return json(["msg"=>"请求错误,签名不存在"],400);
        $checkToken = checkToken($token);
        //是否通过验证
        if ($checkToken['code'] != ComConstant::e_api_sign_success)
            return json($checkToken,400);
        //解析token获取用户id
        $uid = $checkToken['data']->uid;
        if (\app\common\model\User::checkUserState($uid)){
            return json(['msg'=>'您的账号已被封禁,操作失败'],403);
        }
        $deleteTopicByTid = \app\common\model\Topic::deleteTopicByTid($uid, $id);
        if ($deleteTopicByTid){
            return json(['msg'=>'删除成功']);
        }else{
            return json(['msg'=>'删除失败',400]);
        }

    }


    /**
     * 关键词查询主题帖
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function searchTopicByKeyWord(Request $request){
        $keyword = $request->get('keyword');
        $page = $request->get('page');
        $searchTopic = \app\common\model\Topic::searchTopic($keyword,$page);

        if (empty($searchTopic)|| count($searchTopic) == 0){
            return json(['msg'=>'无数据'],404);
        }
        $totalrows = \app\common\model\Topic::where([
            'check'=>true,
            'tdeletetime'=>null
        ])
            ->where('ttopic|tcontents','like','%'.$keyword.'%')
            ->count();
        return json(['list'=>$searchTopic,'totalPage'=>ceil($totalrows/5)]);

    }

    /**
     * 获取用户主题帖
     * @param Request $request
     * @return \think\response\Json
     */
    public function getTopicsByUid(Request $request){
        if (!$request->post('user_token')) return json(['msg'=>'请求参数错误',400]);
        $tokenGetter = tokenGetter($request);
        $checkToken = checkToken($tokenGetter);
        if ($checkToken['code'] != ComConstant::e_api_sign_success)
            return json(['msg'=>$checkToken['res']],400);
        //解析token获取用户id
        $uid = $checkToken['data']->uid;
        if (\app\common\model\User::checkUserState($uid)){
            return json(['msg'=>'您的账号已被封禁,操作失败'],403);
        }
        $listTopicByUid = \app\common\model\Topic::listTopicByUid($uid);
        return json(['msg'=>$listTopicByUid]);
    }
}
