<?php

namespace app\admin\controller;

use app\admin\validate\AdminLoginValidate;
use app\common\ComConstant;
use app\common\model\Feedback;
use app\common\model\Reply;
use app\common\model\Report;
use app\common\model\Reporttype;
use app\common\model\User;
use think\Controller;
use think\Db;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\Request;

class Index extends Controller
{
    //后台登录页
    function index(){
        return view('admin@index/index');
    }
    //后台登录控制
    function login(Request $request){
        if (!request()->isPost()) return $this->error('请登陆!',$url=url('admin/index/index'));
        $input = $request->post();
        //转义特殊字符
        foreach ($input as $k=>$v){
            $input[$k] = htmlentities($v);
        }
        $validate = $this->validate($input, AdminLoginValidate::class);
        if (true !== $validate){
           return $this->error($validate,$url=url('admin/index/index'));
        }
        $checkLogin = User::checkLogin($input['username'], $input['password']);
        if ($checkLogin){
            return redirect(url('admin/index/main'));
        }else{
            return $this->error(User::getErrorInfo(),$url=url('admin/index/index'),$wait='2');
        }

    }

    /**
     * 管理员头像图片路径
     */
    public function picUrl(){
        $picUrl = '/static/uface/';
        $this->assign('picUrl',$picUrl);
    }
    //后台管理首页
    function main(){
        $this->picUrl();
        $user_num = User::countRegister();
        $section_num=\app\common\model\Section::countSection();
        $topic_num = \app\common\model\Topic::countTopic();
        $reply_num=Reply::countReply();
        $this->assign([
            'user_num'=>$user_num,
            'topic_num'=>$topic_num,
            'section_num'=>$section_num,
            'reply_num'=>$reply_num
        ]);
        return view('admin@index/main');
    }

    //板块管理
    function sectionManager(){
        $this->picUrl();
        $listSection = \app\common\model\Section::listSection();
        $this->assign('sections',$listSection);
        return view('admin@section/section-manage');
    }
    //板块添加
    function section_add(){
        return view('admin@section/section-add');
    }
    //板块修改
    function section_edit($id){
        $section = \app\common\model\Section::get($id);
        $this->assign('section',$section);
        return view('admin@section/section-edit');
    }
    //主帖列表管理
    function topicManager(Request $request){
        $page = $request->get('page');
        if ($page==null){
            $page = 1;
        }
        $row = 10;
        $this->picUrl();
        $allTopic = \app\common\model\Topic::getAllTopic($page,$row);
        $count = \app\common\model\Topic::where('check', '1')->count();
        $sections = \app\common\model\Section::all();
        $this->assign([
            'allTopic'=>$allTopic
            ,'TopicNum'=>$count
            ,'sections'=>$sections
            ,'pages'=>ceil($count/$row)
            ,'api'=>ComConstant::api_address]);
        return view('admin@topic/topic-manage');
    }
    //主帖审核管理
    function checkTopicManager(Request $request){
        $page = $request->get('page');
        $pages = 1;
        $count = 0;
        if ($page==null || !is_numeric($page)){
            $page = 1;
        }
        $this->picUrl();
        $collection = \app\common\model\Topic::listApprovedTopic($page,0);
        switch ($collection['code']){
            case ComConstant::data_not_found:
                $msg = '无数据';
                $collection = null;
                break;
            case ComConstant::model_not_found:
                $msg = '查询出错'.ComConstant::model_not_found;
                $collection = null;
                break;
            case ComConstant::database_error:
                $msg = '查询出错'.ComConstant::database_error;
                $collection = null;
                break;
            case ComConstant::data_select_success:
                $pages = $collection['pages'];
                $count = $collection['total'];
                $collection = $collection['data'];
                break;
            default:
                $msg = '查询出错';
                $pages = 1;
                $collection = null;
        }
        $sections = \app\common\model\Section::all();
        $this->assign([
            'checkTopic'=>$collection,
            'sections'=>$sections,
            'pages'=>$pages,
            'count'=>$count]);
        return view('admin@topic/check-topic');
    }

    //主帖回收站页面
    public function trashTopicManager(Request $request){
        $page = $request->get('page'); //页码
        $pages = 1; //页数
        $count = 0; //记录数
        if ($page==null || !is_numeric($page)){
            $page = 1;
        }
        $this->picUrl();
        $collection = \app\common\model\Topic::listTrashTopic($page);
        switch ($collection['code']){
            case ComConstant::database_error:
                $msg = '查询出错'.ComConstant::database_error;
                $collection = null;
                break;
            case ComConstant::data_select_success:
                $pages = $collection['pages'];
                $count = $collection['total'];
                $collection = $collection['data'];
                break;
            default:
                $msg = '查询出错';
                $pages = 1;
                $collection = null;
        }
        $sections = \app\common\model\Section::all();
        $this->assign([
            'checkTopic'=>$collection,
            'sections'=>$sections,
            'pages'=>$pages,
            'count'=>$count]);
        return view('admin@topic/trash-topic');
    }


    /**
     * 主帖添加页面/帖子查看页面
     * @param Request $request
     * @return \think\response\View
     * @throws \think\Exception\DbException
     */
    function topic_add(Request $request){
        $tid = $request->get('tid');
        $mark = $request->get('mark');
        $sections = \app\common\model\Section::all();
        if ($tid!=null){
            if ($mark == 'trash'){
                $topic = \app\common\model\Topic::onlyTrashed()->get($tid);
            }else{
                $topic = \app\common\model\Topic::get($tid);
            }
            $this->assign('topic',$topic);
        }else{
            $this->assign('topic',null);
        }

        $this->assign('sections',$sections);
        return view('admin@topic/topic-add');
    }

    /**
     * 评论管理
     * @param Request $request
     * @return \think\response\View
     */
    public function replyManager(Request $request){
        $this->picUrl();
        $page = $request->get('page'); //页码
        $status =$request->get('status'); //状态
        $rtid = $request->get('tid');
        $pages = 1; //页数
        $data = [];
        $count = 0; //记录数
        if ($page==null || !is_numeric($page)){
            $page = 1;
        }
        if ($status==null || !is_numeric($status)){
            $status = 0; //默认查询全部
        }
        $allReply = Reply::getAllReply($page,$status,$rtid);
        if ($allReply){
            $pages = $allReply['pages'];
            $count = $allReply['total'];
            $data = $allReply['data'];
        }
        $this->assign([
            'allReply'=>$data,
            'pages'=>$pages,
            'count'=>$count,
            'status'=>$status,
            'api'=>ComConstant::api_address]);
        return view('admin@reply/reply-manage');
    }

    //查看内容窗口
    public function viewReply($id){
        try {
            $reply = Reply::withTrashed()->field('rcontents')->find($id);
        } catch (\Exception $e) {
            $reply = null;
        }
        $this->assign('reply',$reply);
        return view('admin@reply/view_reply');
    }

    /**
     * 举报记录管理
     * @return \think\response\View
     */
    public function reportManager(Request $request){
        $this->picUrl();
        $status =$request->get('status'); //状态
        $page = $request->get('page'); //页码
        if ($status==null || !is_numeric($status)){
            $status = 0; //默认查询全部
        }
        if ($page==null || !is_numeric($page)){
            $page = 1; //默认查询全部
        }
        $listReports = Report::listReports($page,$status);
        $feedback = Feedback::all();
        $type = Reporttype::all();
        $this->assign([
            'status'=>$status,
            'api'=>ComConstant::api_address,
            'count'=>$listReports['total'],
            'allReport'=>$listReports['data'],
            'pages'=>$listReports['pages'],
            'feedback'=>$feedback,
            'type'=>$type
        ]);
        return view('admin@reply/report-manage');
    }

    /** 用户列表
     * @return \think\response\Json|\think\response\Redirect|\think\response\View
     * @throws DbException
     */
    function userList(Request $request){
        $this->picUrl();
        $page = $request->get('page'); //页码
        if ($page==null || !is_numeric($page)){
            $page = 1;
        }
        if (!session('admin.userInfo')){
            return redirect('admin/index/index');
        }
        $listUser = User::listUser($page);
        if (!$listUser){
            return json(['msg'=>'无数据'],404);
        }
        $count = User::count('*');
        $this->assign([
            'userlist'=>$listUser,
            'pages'=>ceil($count/20),
            'count'=>$count
        ]);
        return view("admin@user/member-list");
    }

    /**
     * 查看单个用户
     * @param $id
     * @return \think\response\View
     */
    function userView($id){
        $user = User::get($id);
        $this->assign('user',$user);
        return view("admin@user/member-show");
    }
    //添加用户
    function userAdd(){
        return view("admin@user/member-add");
    }
    //退出登录
    function logout(){
        session(null);
        return redirect('admin/index/index');
    }

}
