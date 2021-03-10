<?php

namespace app\common\model;

use think\Db;
use think\Exception;
use think\exception\PDOException;

class Section extends BaseModel
{
    protected $pk = 'sid';

    /**
     * 根据sid更新板块点击数
     * @param $sid
     * @param $count
     * @return bool
     */
    public static function addClickBySid($sid,$count){
        try {
            $se = self::get($sid);
            $se->sclickcount = Db::raw('sclickcount+' . $count);
            $se->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 更新板块回复数
     * @param $sid
     * @param $topic
     * @return bool
     */
    public static function updateTopic($sid,$topic){
        try {
            self::where('sid',$sid)->inc('stopiccount')->update();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 统计板块数量
     * @return \think\db\Query
     */
    public static function countSection(){
        $query = self::count();
        return $query;
    }

    /**
     * 板块列表
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function listSection(){
       return self::field(
           's.sid,
           s.sname,
           s.sstatement,
           s.simg,
           s.sclickcount,
           s.stopiccount,
           s.stime,
           u.uname')
           ->alias('s')
           ->join(['user'=>'u'],'s.smasterid=u.uid')
           ->select();
    }

    /**
     * 新建板块
     * @param $sname
     * @param $sstatement
     * @param $smasterid
     * @param $simg
     * @return bool
     */
    public static function addSection($sname,$sstatement,$smasterid,$simg){
        try{
            self::create([
                'sname'=>$sname,
                'sstatement'=>$sstatement,
                'smasterid'=>$smasterid,
                'simg'=>$simg
            ]);
            return true;
        }catch (\Exception $exception){
            return false;
        }

    }

    /**
     * 根据板块id 删除板块 并删除所有板块内的主题帖
     * @param $sid
     * @return bool
     */
    public static function deleteSectionBySid($sid){
        self::beginTrans();
        try{
            self::destroy($sid);
            Topic::where('tsid',$sid)->delete();
            self::commitTrans();
            return true;
        }catch (\Exception $exception){
            self::rollbackTrans();
            return false;
        }

    }


    /**
     * 修改板块
     * @param $sid
     * @param $sname
     * @param $sstatement
     * @param $simg
     * @return bool
     */
    public static function updateSectionBySid($sid,$sname,$sstatement,$simg){

        try {
            $model = self::field('simg')->where('sid', $sid)->find();
            $origin_img = $model->simg;
            if ($simg==null){
                self::where('sid', $sid)->update([
                    'sname' => $sname,
                    'sstatement' => $sstatement,
                ]);
                return $origin_img;
            }
            self::where('sid', $sid)->update([
                'sname' => $sname,
                'sstatement' => $sstatement,
                'simg' => $simg
            ]);
            return $origin_img;
        } catch (PDOException $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

}
