<?php

namespace app\common\model;


use think\Db;
use think\Exception;
use think\exception\DbException;
use think\model\concern\SoftDelete;

class Reply extends BaseModel
{
    //
    use SoftDelete;

    protected $pk = 'rid';
    protected $deleteTime = 'rdeletetime';
    protected $autoWriteTimestamp = 'datetime';

    public function scopeRtid($query,$rtid)
    {
        if ($rtid == null||!is_numeric($rtid)){
           return;
        }else{
            $query->where('rtid','=',$rtid);

        }

    }

    /**
     * @param int $rtid 主题编号
     * @param int $ruid 回复人
     * @param int $rtarget 被回复人
     * @param string $rcontents 内容
     * @param int $fatherReply 父回复贴
     * @return bool|mixed
     */
    public static function addReply(
        int $rtid,int $ruid,int $rtarget,string $rcontents,int $fatherReply=null){
        if (!$fatherReply){
            $fatherReply=0;
        }
        try {
            self::beginTrans();
            $reply = self::create([
                'rtid' => $rtid,
                'ruid' => $ruid,
                'rtarget' => $rtarget,
                'rcontents' => $rcontents,
                'rrid' => $fatherReply
            ]);
            Topic::where('tid',$rtid)->inc('treplycount')->update();
            self::commitTrans();
            return $reply->rid;
        } catch (\Exception $e) {
            self::rollbackTrans();
            return false;
        }

    }

    /**
     * 根据tid获取帖子回复列表
     * @param $tid
     * @param $page
     * @param int $row
     * @return array|bool|\PDOStatement|string|\think\Collection
     */
    public static function listReplyByTid($tid,$page,$row=5){

        try {
            $reply = self::field([
                'rid', 'ruid', 'rtarget', 'rcontents', 'rlikecount', 'rrid', 'rtime',
                Db::raw('(select uname from user where uid = reply.ruid) as uname'),
                Db::raw('(select uface from user where uid = reply.ruid) as uface'),
                Db::raw('(select uname from user where uid = reply.rtarget) as targetname'),
                Db::raw('(if(rrid=0,
                null,(select left(fnStripTags(t.rcontents),15) 
                from reply t where t.rid=reply.rrid and rdeletetime is NULL))) as fatherContent')
            ])->where([
                'rtid'=>$tid,
                'rdeletetime'=>null
                ])
                ->order(['rtime' => 'desc'])
                ->page($page, $row)
                ->select();
        } catch (DbException $e) {
            return false;
        }
        return $reply;

    }

    public static function updateReplyLike($rid,$status,$num=1){
        try{
            $reply = self::get($rid);
            if ($status == 1){
                $reply->rlikecount	= ['inc', $num];
            }else{
                $reply->rlikecount	= ['dec', $num];
            }
            $reply->save();
        }catch (\Exception $e){
            return false;
        }
        return self::field('rlikecount')->get($rid);

    }

    public static function countReplyByTid(int $tid){
        $count = self::where(['rtid'=> $tid])->count();
        return $count;
    }

    /**
     * 统计评论数量
     * @return \think\db\Query
     */
    public static function countReply(){
        $query = self::count();
        return $query;
    }

    /**
     * 删除回复帖
     * @param $rid
     * @param $uid
     * @return bool
     */
    public static function deleteReplyByRid($rid,$uid){
        //管理员可删除
        try {
            $role = User::where('uid', $uid)->field('roleid')->select();
            if ($role[0]->roleid == 1){
                self::destroy($rid);
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }

        //主题帖所有者可以删除
        //判断是否为主帖所有者
        try {
            $fatherTid = self::field('rtid')->where('rid', $rid)->select();
            $topic_owner = Topic::field('tuid')->where('tid', $fatherTid[0]->rtid)->select();
            if ($topic_owner[0]->tuid==$uid){
                self::destroy($rid);
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }

        //回复帖所有者可以删除
        try {
            $owner = self::field('ruid')->where('rid', $rid)->select();
            if ($owner[0]->ruid==$uid){
                self::destroy($rid);
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * 恢复回复帖
     * @param $rid int 回复id
     * @param $uid int 操作者id
     * @return bool
     */
    public static function restoreReplyByRid($rid,$uid){
        //管理员可恢复
        try {
            $role = User::where('uid', $uid)->field('roleid')->select();
            if ($role[0]->roleid == 1){
                self::onlyTrashed()->find($rid)->restore();
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }
        return false;
    }


    /**查询所有指定状态的回复贴
     * @param $page int 页码
     * @param $status int 状态 [0,1,2] 0全部，1已删除，2已发布
     * @param int $row int 行数
     * @param int $tid int 主帖id
     * @return array|bool
     */
    public static function getAllReply($page,$status,$tid=null,$row=10){
        try {
            switch ((int)$status){
                case 0:
                    $replys = self::withTrashed()->alias('r')
                        ->field([
                            't.tid',
                            't.ttopic',
                            'r.rcontents',
                            'r.rid',
                            'r.rrid',
                            Db::raw('(select r1.rcontents from reply r1 where r.rrid=r1.rid) as fatherContent'),
                            'u.uname',
                            Db::raw('(select u1.uname from user u1 where r.rtarget=u1.uid) as targetName'),
                            'r.rtime',
                            Db::raw("if(r.rdeletetime is null,'已发布','已删除') as status")])
                        ->join(['topic' => 't'], 'r.rtid = t.tid')
                        ->join(['user' => 'u'], 'r.ruid = u.uid')
                        ->rtid($tid)
                        ->order('r.rtime','desc')
                        ->page($page, $row)
                        ->select();
                    $count = Reply::withTrashed()->rtid($tid)->count('*');
                    break;
                case 1:
                    $replys = self::onlyTrashed()->alias('r')
                        ->field([
                            't.tid',
                            't.ttopic',
                            'r.rcontents',
                            'r.rid',
                            'r.rrid',
                            Db::raw('(select r1.rcontents from reply r1 where r.rrid=r1.rid) as fatherContent'),
                            'u.uname',
                            Db::raw('(select u1.uname from user u1 where r.rtarget=u1.uid) as targetName'),
                            'r.rtime',
                            Db::raw("if(r.rdeletetime is null,'已发布','已删除') as status")])
                        ->join(['topic' => 't'], 'r.rtid = t.tid')
                        ->join(['user' => 'u'], 'r.ruid = u.uid')
                        ->rtid($tid)
                        ->order('r.rtime','desc')
                        ->page($page, $row)
                        ->select();
                    $count = Reply::onlyTrashed()->rtid($tid)->count('*');
                    break;
                case 2:
                    $replys = self::alias('r')
                        ->field([
                            't.tid',
                            't.ttopic',
                            'r.rcontents',
                            'r.rid',
                            'r.rrid',
                            Db::raw('(select r1.rcontents from reply r1 where r.rrid=r1.rid) as fatherContent'),
                            'u.uname',
                            Db::raw('(select u1.uname from user u1 where r.rtarget=u1.uid) as targetName'),
                            'r.rtime',
                            Db::raw("if(r.rdeletetime is null,'已发布','已删除') as status")])
                        ->join(['topic' => 't'], 'r.rtid = t.tid')
                        ->join(['user' => 'u'], 'r.ruid = u.uid')
                        ->rtid($tid)
                        ->order('r.rtime','desc')
                        ->page($page, $row)
                        ->select();
                    $count = Reply::rtid($tid)->count('*');
                    break;
                default:
                    $replys = self::withTrashed()->alias('r')
                        ->field([
                            't.tid',
                            't.ttopic',
                            'r.rcontents',
                            'r.rid',
                            'r.rrid',
                            Db::raw('(select r1.rcontents from reply r1 where r.rrid=r1.rid) as fatherContent'),
                            'u.uname',
                            Db::raw('(select u1.uname from user u1 where r.rtarget=u1.uid) as targetName'),
                            'r.rtime',
                            Db::raw("if(r.rdeletetime is null,'已发布','已删除') as status")])
                        ->join(['topic' => 't'], 'r.rtid = t.tid')
                        ->join(['user' => 'u'], 'r.ruid = u.uid')
                        ->rtid($tid)
                        ->order('r.rtime','desc')
                        ->page($page, $row)
                        ->select();
                    $count = Reply::withTrashed()->rtid($tid)->count('*');
            }

            $pages = ceil($count/$row);
            return [
                'data'=>$replys,
                'pages'=>$pages,
                'total'=>$count
            ];
        } catch (\Exception $e) {
            return false;
        }
    }
}
