<?php

namespace app\common\model;

use app\common\ComConstant;
use think\Db;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\Exception;
use think\exception\DbException;
use think\Model;
use think\model\concern\SoftDelete;

class Report extends BaseModel
{
    //
    use SoftDelete;
    protected $pk = 'reid';
    protected $deleteTime = 'feedback_time';
    protected $autoWriteTimestamp = 'datetime';

    /**
     * 创建举报信息
     * @param $rid int 帖子id
     * @param $uid int 举报人id
     * @param $report_type int 举报类型
     * @param $report_reason string 举报理由
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function addReport($rid, $uid, $report_type, $report_reason){
        if(Reply::onlyTrashed()->find($rid)!=null){
            return ComConstant::reply_not_exist;
        }
        $exist_report = self::where([
            'rid' => $rid,
            'uid' => $uid])->find();
        if ($exist_report == null){
            try{
                self::create([
                    'rid'=>$rid,
                    'uid'=>$uid,
                    'type'=>$report_type,
                    'reason'=>$report_reason]);
                return ComConstant::create_report_success;
            }catch (\Exception $exception){
                return false;
            }
        }else{
            return ComConstant::report_already_exist;
        }

    }

    /**
     * 查询指定状态的举报记录
     * @param $page int 页码
     * @param int $status int[0,1] 处理状态
     * @param int $row int 行数
     * @return array
     */
    public static function listReports($page,$status=0,$row=10){
        try {
            $count = self::withTrashed()->where('status',$status)->count('*');
            $reports = [];
            if ($count != 0){
                $reports = self::withTrashed()->alias('re')->field([
                    're.reid',
                    'r.rtid',
                    'r.rid',
                    'r.ruid',
                    Db::raw('(select uname from user u1 where r.ruid=u1.uid) as replyer'),
                    'r.rcontents',
                    're.uid as reporter_id',
                    'u.uname as reporter',
                    'ret.type',
                    're.reason',
                    're.status',
                    're.result as result_id',
                    'f.result',
                    're.report_time',
                    're.feedback_time'
                ])
                    ->join(['reply' => 'r'], 'r.rid=re.rid')
                    ->join(['user' => 'u'], 're.uid=u.uid')
                    ->join(['feedback' => 'f'], 're.result=f.fid')
                    ->join(['reporttype' => 'ret'], 're.type=ret.typeid')
                    ->where('re.status', (int)$status)
                    ->order(['r.rid'=>'asc','re.report_time'=> 'desc'])
                    ->page($page, $row)
                    ->select();
            }
            return [
                'data'=>$reports,
                'total'=>$count,
                'pages'=>ceil($count/$row)
            ];
        } catch (\Exception $e) {
            return [
                'data'=>[],
                'total'=>0,
                'pages'=>1
            ];
        }

    }

    /**
     * 处理举报
     * @param $feedback int 处理类型
     * @param $rid int 帖子id
     * @param $uid int 帖子用户id
     * @param $reporter int 举报人id
     * @param $reid int 举报ID
     * @return bool
     */
    public static function feedBackReport($feedback,$rid,$uid,$reporter,$reid){
        if ($feedback == ComConstant::no_feedback){
            return false;
        }
        self::beginTrans();
        try {
            self::where('reid', $reid)->update([
                'status' => 1,
                'result' => $feedback
            ]);
            self::destroy($reid);
            switch ($feedback) {
                case ComConstant::report_rejected_feedback:
                    $msg = "举报失败,您所举报的情况经核实不存在";
                    if(!Msg::sendMessage(1, $reporter, $msg, 1, 1)){
                        self::rollbackTrans();
                    }
                    self::commitTrans();
                    break;
                case ComConstant::delete_reply_feedback:
                    $msg = "举报成功,您所举报的帖子已被删除";
                    if(!Msg::sendMessage(1, $reporter, $msg, 1, 1)){
                        self::rollbackTrans();
                    }
                    Reply::destroy($rid);
                    self::commitTrans();
                    break;
                case ComConstant::Ban_user_and_delete_reply_feedback:
                    $msg = "举报成功,您所举报的帖子已被删除,且该用户已被封禁";
                    if(!Msg::sendMessage(1, $reporter, $msg, 1, 1)){
                        self::rollbackTrans();
                    }
                    Reply::destroy($rid);
                    User::lockDownUser($uid);
                    self::commitTrans();
                    break;
            }
            return true;
        } catch (\Exception $e) {
            self::rollbackTrans();
            return false;
        }


    }
}
