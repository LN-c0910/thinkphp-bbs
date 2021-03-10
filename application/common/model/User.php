<?php

namespace app\common\model;


use think\Exception;
use think\exception\PDOException;

class User extends BaseModel
{
    protected $pk = 'uid';
    public static $userInfo = null;

    /**登录验证
     * @param $uname
     * @param $upassword
     * @return bool
     */
    public static function checkLogin($uname,$upassword){
        $usrInfo = self::get(compact('uname'));
        if (!$usrInfo) return self::setErrorInfo('账号或密码错误，请重新输入');
        if($usrInfo['ustate']) return self::setErrorInfo('该账号已被封禁!请联系管理员');
        if($usrInfo['upassword'] != webmd5($upassword)) return self::setErrorInfo('账号或密码错误，请重新输入');
        self::setLoginInfo($usrInfo);
        return true;
    }


    /**
     * 设置session登录信息
     * @param $user
     */
    public static function setLoginInfo($user){
        self::$userInfo =$user->toArray();
        if ($user['roleid']==1){
            session('admin.uid',$user['uid']);
            session('admin.uname',$user['uname']);
            session('admin.userInfo',$user->toArray());
        }else{
            session('usr.uid',$user['uid']);
            session('usr.uname',$user['uname']);
            session('usr.userInfo',$user->toArray());
        }

    }

    /**
     * 用户注册
     * @param $uname
     * @param $password
     * @param int $roleid
     * @return bool
     */
    public static function checkRegister($uname,$password,$roleid=2){
        $usrInfo = self::get(compact('uname'));
        if ($usrInfo) return self::setErrorInfo("用户名已存在,请重试");
        self::create([
            "uname"=>$uname,
            "upassword"=>webmd5($password),
            "roleid"=>$roleid
        ]);
        return true;
    }

    /**
     * 修改密码
     * @param $uid
     * @param $upassword
     * @param $newpassword
     * @return bool
     */
    public static function updateUserPassword($uid,$upassword,$newpassword){
        $usr = self::get($uid);
        if (empty($usr)) return self::setErrorInfo("用户不存在");
        if ($usr['ustate'] == 1){
            return self::setErrorInfo("该用户已被封禁,请联系管理员");
        }
        if ($usr['upassword'] != webmd5($upassword)){
            return self::setErrorInfo("用户密码不正确");
        }

        $usr->upassword = webmd5($newpassword);
        $usr->save();
        return true;

    }

    /**
     * 统计注册用户数量
     * @return float|string
     */
      public static function countRegister(){
            $roleid = 2;
            $count_user = self::where([
              'roleid' => $roleid
            ])->count();
            return $count_user;
      }


    /**
     * 查询用户账号状态 true 锁定状态，false 正常
     * @param $uid
     * @return mixed
     * @throws \think\Exception\DbException
     */
      public static function checkUserState($uid){
          $state = self::field('ustate')->get($uid);
          return $state['ustate'];
      }

    /**锁定账号
     * @param $uid
     * @return bool
     */
      public static function lockDownUser($uid){
          try {
              self::where('uid', $uid)->update([
                  'ustate' => 1
              ]);
              return true;
          }catch (\Exception $e) {
              return false;
          }
      }

    /**
     * 解锁账号
     * @param $uid
     * @return bool
     */
      public static function openUpUser($uid){
          try {
              self::where('uid', $uid)->update([
                  'ustate' => 0
              ]);
              return true;
          } catch (\Exception $e) {
              return false;
          }
      }

    /**用户列表
     * @param $page
     * @param int $row
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
      public static function listUser($page,$row=20){
          $users = self::alias('u')
              ->field([
                  'uid',
                  'uname',
                  'uface',
                  'ulastlogin','uregdate','ustate','upoint','rolename'])
              ->join(['role' => 'r'], 'u.roleid=r.roleid')->page($page,$row)
              ->select();
          return $users;
      }
}
