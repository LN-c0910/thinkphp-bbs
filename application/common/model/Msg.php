<?php

namespace app\common\model;


use think\Exception;

class Msg extends BaseModel
{

    protected $pk = 'msgid';


    /**
     * 发送消息
     * @param $send_id int 真实发送者id
     * @param $accept_id int 真实接受者id
     * @param $msg string 内容
     * @param $msg_type int 类型
     * @return bool
     */
    public static function sendMessage($send_id,$accept_id,$msg,$msg_type,$send_type=null){
        //备份两条消息记录，从而允许单方面删除消息而不影响另一方阅读
        self::beginTrans(); //开启事务s
        try{
            if ($send_type==1){ //系统自动消息 无需备份
                self::create([
                    'userid'=>$send_id,
                    'friendid'=>$accept_id,
                    'sendid'=>$send_id,
                    'acceptid'=>$accept_id,
                    'msg'=>$msg,
                    'msgtype'=>$msg_type]);
                self::commitTrans(); //提交
            }else{
                self::create([
                    'userid'=>$send_id,
                    'friendid'=>$accept_id,
                    'sendid'=>$send_id,
                    'acceptid'=>$accept_id,
                    'msg'=>$msg,
                    'msgtype'=>$msg_type]);
                self::create([
                    'userid'=>$accept_id,
                    'friendid'=>$send_id,
                    'sendid'=>$send_id,
                    'acceptid'=>$accept_id,
                    'msg'=>$msg,
                    'msgtype'=>$msg_type]);
                self::commitTrans(); //提交
            }

            return true;
        }catch (\Exception $e){
            self::rollbackTrans(); //回滚事务
            return false;
        }

    }
}
