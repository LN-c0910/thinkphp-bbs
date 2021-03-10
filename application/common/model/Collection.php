<?php

namespace app\common\model;
use think\Db;
use think\model\concern\SoftDelete;
use Exception;
use app\common\ComConstant;
class Collection extends BaseModel
{
    use SoftDelete;
    protected $pk = 'cid';
    protected $deleteTime = 'cdeletetime';
    protected $autoWriteTimestamp = 'datetime';


    /**
     * 新增收藏
     * @param int $uid 用户id
     * @param int $tid 主帖id
     * @return bool|mixed
     */

    public static function addCollection($uid,$tid){
        try {

            if (!Topic::isExistTopic($tid)) return ComConstant::topic_not_exist;
            $count = self::where(['cuid' => $uid, 'ctid' => $tid])->count();
            if ($count == 0){
                self::beginTrans();
                $collection = self::create(['cuid' => $uid, 'ctid' => $tid]);
                Topic::where('tid',$tid)->inc('collections')->update();
                //返回收藏id
                self::commitTrans();
                return $collection->cid;
            }
            return ComConstant::collection_already_exist;
        } catch (\Exception $e) {
            self::rollbackTrans();
            return false;
        }

    }


    /**
     * 删除收藏
     * @param $uid int 用户id
     * @param $cid int 收藏id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function deleteCollection($uid,$cid){
        $Collection = self::get($cid);
        if ($Collection == null){
            return ComConstant::collection_not_exist;
        }
        if ($Collection->cuid != $uid){
            return ComConstant::collection_no_permission;
        }
        try {
            $Collection->delete();
            return ComConstant::collection_delete_success;
        } catch (Exception $e) {
            return false;
        }

    }


    /**
     * 显示用户收藏
     * @param $uid int 用户id
     * @return bool|int|mixed
     */
    public static function listCollectionByUid($uid){
        try {
            if (self::where('cuid',$uid)->count() == 0){
                return ComConstant::user_collection_empty;
            }
            $query = Db::query(
            "select topic.ttopic as title,
            topic.tid as tid,
            collection.cid as cid,    
           (select uname from user where topic.tuid = user.uid) as author,
           topic.tlastclick as lastReplyTime,
           if(topic.tdeletetime is null,'正常','已删除') as status 
            from collection join topic 
            where collection.ctid = topic.tid and collection.cdeletetime is null and collection.cuid=?", [$uid]);
            return $query;
        } catch (Exception $e) {
            return false;
        }

    }
}
