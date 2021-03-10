<?php

namespace app\common\model;


use app\common\ComConstant;
use think\Db;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\Exception;
use think\exception\DbException;
use think\exception\PDOException;
use think\model\concern\SoftDelete;
class Topic extends BaseModel
{
    //启用软删除
    use SoftDelete;
    protected $pk = 'tid';
    //软删除字段
    protected $deleteTime = 'tdeletetime';
    //时间戳格式
    protected $autoWriteTimestamp = 'datetime';
//    protected $dateFormat = 'Y-m-d H:i:s';
    /**
     * 分页获取所有主题
     * @param $page
     * @param int $row
     * @param array $good 是否加精 获取全部[0,1] 加精[1],不加精[0]
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getAllTopic($page,$row=5,$good=[0,1]){
        $topic_list = self::field([
            'tid', 'tsid', 'tuid', 'ttopic', 'good', 'top','forcetop','tlikecount',
            Db::raw("CONCAT(left(fnStripTags(tcontents),15),'...') as tcontents"),
            'ttime', 'tlikecount', 'tmodifytime',
            Db::raw('(select sname from section where topic.tsid = sid) as sname'),
            Db::raw('(select uname from user where uid = topic.tuid) as uname'),
            Db::raw('(select count(*) from reply where rtid = topic.tid) as replys'),
            Db::raw('(select max(rtime) from reply where rtid = tid) as tlastclick')])
            ->where([
                'good'=>$good,
                'check'=>true
            ])
            ->order(['forcetop'=>'desc','tlastclick'=>'desc','ttime'=>'desc'])
            ->page($page,$row)
            ->select();
        return $topic_list;
    }

    /**
     * 根据板块获取主题
     * @param $section_id
     * @param int $page  当前页码
     * @param int $row 当前页行数
     * @param array $good 是否加精 获取全部[0,1] 加精[1],不加精[0]
     * @return array||string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getTopicBySid($section_id,$page,$row=5,$good=[0,1]){
        $topic_list = self::where('tsid', $section_id)
            ->field(['tid', 'tsid', 'tuid', 'ttopic', 'good', 'top',
                Db::raw("CONCAT(left(fnStripTags(tcontents),15),'...') as tcontents"),
                'ttime', 'tlikecount', 'tlastclick', 'tmodifytime',
                Db::raw('(select uname from user where uid = topic.tuid) as uname'),
                Db::raw('(select count(*) from reply where rtid = topic.tid) as replys')])
            ->where([
                'good'=>$good,
                'check'=>true
            ])
            ->order(['top'=>'desc','tlastclick'=>'desc'])
            ->page($page,$row)
            ->select();
        return $topic_list;
    }

    /**
     * 添加主帖
     * @param int $tuid
     * @param int $tsid
     * @param string $ttopic
     * @param string $tcontents
     * @return mixed
     */
    public static function addTopicByUid(int $tuid,int $tsid,string $ttopic,string $tcontents){
        $topic = self::create([
            'tuid' => $tuid,
            'tsid' => $tsid,
            'ttopic' => $ttopic,
            'tcontents' => $tcontents,
            'check'=>false
        ]);
        return $topic->tid;
    }

    /**
     * 更新帖子点赞
     * @param int $tid
     * @param int $status
     * @return array|bool|\PDOStatement|string|\think\Model|null
     */
    public static function updatelikeByTid($tid,$status){

        try {
            $topic = self::get($tid);
            if ($status == 0) {
                $topic->tlikecount = Db::raw('tlikecount-1');
            } else if ($status == 1) {
                $topic->tlikecount = Db::raw('tlikecount+1');
            } else {
                return false;
            }
            $topic->save();
            return self::field('tlikecount')->where('tid',$tid)->find();
        } catch (\Exception $e) {
            return false;
        }

    }




    /***
     * 更新帖子状态
     * @param $tid
     * @param $status
     * @return bool|null
     */
    public static function updateTopicStatus($tid,$status){
        try{
            switch ((int)$status){
                case ComConstant::set_topic_force_top:
                    self::where('tid',$tid)
                        ->update(['forcetop'=>1, 'top'=>1]);
                    break;
                case ComConstant::no_set_topic_force_top:
                    self::where('tid',$tid)
                        ->update(['forcetop'=>0]);
                    break;
                case ComConstant::set_topic_top:
                    self::where('tid',$tid)
                        ->update(['top'=>1]);
                    break;
                case ComConstant::no_set_topic_top:
                    self::where('tid',$tid)
                        ->update(['top'=>0]);
                    break;
                case ComConstant::set_topic_good:
                    self::where('tid',$tid)
                        ->update(['good'=>1]);
                    break;
                case ComConstant::no_set_topic_good:
                    self::where('tid',$tid)
                        ->update(['good'=>0]);
                    break;
                default:
                    return false;
            }
        }catch (\Exception $e){
            return null;
        }

       return true;
    }


    /**
     * 统计主贴数量
     * @return \think\db\Query
     */


    public static function countTopic(){
        $query = self::where('tdeletetime',null)->count();
        return $query;
    }

    /***
     * 通过关键词查询主题帖
     * @param $keyword
     * @param $page
     * @param int $row
     * @return array|\PDOStatement|string|\think\Collection|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function searchTopic($keyword,$page,$row=5){
        if (trim($keyword)=="") {
            return null;
        }
        $select = self::field([
            'tid', 'tsid', 'tuid', 'ttopic', 'good', 'top','forcetop',
            Db::raw("CONCAT(left(fnStripTags(tcontents),15),'...') as tcontents"),
            'ttime', 'tlikecount', 'tmodifytime',
            Db::raw('(select uname from user where uid = topic.tuid) as uname'),
            Db::raw('(select count(*) from reply where rtid = topic.tid) as replys'),
            Db::raw('(select max(rtime) from reply where rtid = tid) as tlastclick')])
            ->where([
                'check'=>true
            ])
            ->where('ttopic|tcontents','like','%'.$keyword.'%')
            ->page($page,$row)
            ->select();
        return $select;
    }




    /**
     * 删除主题帖
     * @param $uid int 操作者id
     * @param $tid int 主题id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function deleteTopicByTid($uid,$tid){
        $role = User::where('uid',$uid)->field('roleid')->select();
        $owner = self::field('tuid,ttopic')->where('tid', $tid)->select();
        //管理员删除主题帖
        if ($role[0]->roleid == 1 && $owner[0]->tuid!=$uid){
            self::beginTrans();
            $msg = "您发布的 ".$owner[0]->ttopic." 主帖因违反论坛管理条例已被管理员删除";
            try{
                self::destroy($tid);
                //发送系统消息通知用户主帖被删除
                Msg::sendMessage($uid,$owner[0]->tuid,$msg,1);
                self::commitTrans();
                return true;
            }catch (\Exception $e){
                self::rollbackTrans();
                return false;
            }
        }
        // 用户自己删除主题帖
        if ($owner[0]->tuid==$uid){
            self::destroy($tid);
            return true;
        }
        //无权删除
        return false;
    }



    /**
     * 判断是否存在该主帖，存在true,不存在false
     * @param $tid
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function isExistTopic($tid){
        if (self::withTrashed()->where('tid',$tid)->count() == 0){
            return false;
        }
        $models = self::withTrashed()->whereRaw("(tdeletetime is not null or topic.check=0) and tid=:tid",['tid'=>$tid])
                ->select();
        return count($models)==0;
    }


    /**
     * 查询用户主题帖
     * @param $uid
     * @return mixed
     * @throws \think\db\exception\BindParamException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public static function listTopicByUid($uid){
          self::withTrashed()->where('tuid', $uid)->select();
        $topics = self::query(
            "select tid,
       (select sname from section where section.sid = topic.tsid) as sname,
       treplycount,ttopic,good,top,ttime,tlastclick,tdeletetime,
       if(topic.tdeletetime is null,if(topic.`check`=1,'已发布','审核中'),'已删除') as 'check' 
        from topic where tuid = ?",[$uid]);

        return $topics;

    }

    /**
     * 修改帖子板块
     * @param $tid
     * @param $tsid
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public static function updateTopicSection($tid,$tsid){
        try {
            self::where('tid', $tid)->update(['tsid' => $tsid]);
            return true;
        } catch (PDOException $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }
    }


    /**
     * 已/未审核帖子列表
     * @param $page int 页码
     * @param $check int[0,1] 审核标志
     * @param int $row 行数
     * @return array
     */
    public static function listApprovedTopic($page,$check,$row=5){
        try {
            $topics = self::where('topic.check', $check)->page($page, $row)->select();
            $count = self::where('topic.check', $check)->count();
        } catch (DataNotFoundException $e) {
            return ['code'=>ComConstant::data_not_found,'data'=>null];
        } catch (ModelNotFoundException $e) {
            return ['code'=>ComConstant::model_not_found,'data'=>null];

        } catch (DbException $e) {
            return ['code'=>ComConstant::database_error,'data'=>null];
        }
        return [
            'code'=>ComConstant::data_select_success,
            'data'=>$topics,
            'total'=>$count,
            'pages'=>ceil($count/$row)
        ];
    }

    /**
     * 查询被删除主题
     * @param $page int 页码
     * @param int $row 行数
     * @return array
     */
    public static function listTrashTopic($page,$row=5){
        try {
            $trashTopic = self::onlyTrashed()->join(['user'=>'u'],'topic.tuid=u.uid')
                ->page($page, $row)->all();
            $count = self::onlyTrashed()->count();
            return [
                'code'=>ComConstant::data_select_success,
                'data'=>$trashTopic,
                'total'=>$count,
                'pages'=>ceil($count/$row)
            ];
        } catch (DbException $e) {
            return ['code'=>ComConstant::database_error,'data'=>null];
        }

    }

    /**
     * 审核主题
     * @param $tid
     * @return bool
     */

    public static function approveTopic($tid){
        try {
            self::where('tid', $tid)->update(['check' => true]);
            return true;
        } catch (PDOException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 物理删除主帖
     * @param $tid
     * @return bool
     */
    public static function destroyTopicByTid($tid)
    {
        self::beginTrans();
        try {
            //真实删除主帖及其下所有回复帖
            $topic = self::onlyTrashed()->find($tid);
            $reply = Reply::withTrashed()->where('rtid', $tid);
            $reply->delete(true);
            $topic->delete(true);
            self::commitTrans();
            return true;
        } catch (\Exception $e) {
            self::rollbackTrans();
            return false;
        }

    }

    /**
     * 恢复被删除主帖
     * @param $tid
     * @return bool
     */
    public static function restoreTopicByTid($tid)
    {

        try {
            self::onlyTrashed()->find($tid)->restore();
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
}
