<?php


namespace app\common;


/**
 * Class ComConstant 常用常量定义
 * @package app\common
 */
class ComConstant
{
    // 手机号正则
    const phone_reg_expression = "^(13[0-9]|15[0-9]|17[0-9]|18[0-9]|14[57])[0-9]{8}$";

    //前台地址
    const api_address = 'http://192.168.137.1:8080/';


    //101-199，接口相关
    const e_api_sign_miss = 10101;//'签名参数缺失'
    const e_api_sign_wrong = 10102; //'接口签名验证失败'
    const e_api_sign_expired = 10103;//'签名失效'
    const e_api_sign_success = 10104;//'签名有效

    //301-399 ，用户相关
    const e_user_disabled = 10301;//'用户已被禁用'
    const e_user_miss = 10302;//'用户不存在'
    const e_user_pass_wrong = 10303;//'用户密码错误'
    const e_user_pass_miss = 10304;//'用户或密码缺少'
    const e_api_user_token_miss = 10305;//'用户访问令牌缺失'
    const e_api_user_token_expire = 10306;//'用户访问令牌过期'
    const e_api_user_login_success = 10307;//'用户登录成功'
    const e_api_user_register_success = 10308;//'用户注册成功'
    const e_api_user_register_failed = 10309;//'用户注册失败'

    //主题帖状态 401-499
    const set_topic_top = 10401;  //板块内置顶
    const no_set_topic_top = 10402; //板块内不置顶
    const set_topic_good = 10403;//加精
    const no_set_topic_good = 10404;//不加精
    const set_topic_force_top = 10405;//首页置顶
    const no_set_topic_force_top = 10406;//首页不置顶
    const topic_not_exist = 10407; // 主帖不存在

    //收藏状态 501-599
    const collection_already_exist = 10501; //收藏已存在
    const collection_create_success = 10502; // 收藏创建成功
    const collection_not_exist = 10503; //收藏不存在
    const collection_delete_success = 10504; //收藏删除成功
    const collection_no_permission = 10505; //无效访问收藏
    const user_collection_empty = 10506; //用户收藏为空

    //举报相关 601-699
    const create_report_success = 10601;//创建举报成功
    const report_already_exist = 10602; //举报已存在

    //回复帖状态 701-799
    const reply_not_exist = 10701;

    //数据库错误 101-199
    const data_not_found = 20101; //找不到数据
    const model_not_found = 20102; //找不到模型
    const database_error = 20103; //其他数据库错误

    //数据库成功状态 201-299
    const data_select_success = 20201; //查询成功
    const data_insert_success = 20202; //创建成功
    const data_update_success = 20203; //更新成功
    const data_delete_success_true = 20204; //物理删除成功
    const data_delete_success= 20205; //软删除成功

    //举报处理
    const no_feedback = 1; //无处理
    const report_rejected_feedback = 2; //驳回举报
    const delete_reply_feedback = 3; //删除评论
    const Ban_user_and_delete_reply_feedback = 4; //封号删评

}