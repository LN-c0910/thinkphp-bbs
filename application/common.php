<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------

use \Firebase\JWT\JWT;
use app\common\ComConstant;
// 应用公共文件

//获取public路径
function get_public_path(){
    return dirname(__DIR__).DIRECTORY_SEPARATOR.'public';
}
//获取route路径
function get_route_path(){
    return dirname(__DIR__).DIRECTORY_SEPARATOR.'route';
}
//格式化json
function msg_result($code,$res){
    return json(["code"=>$code,"res"=>$res]);
}
//双层md5加密 用户密码加密
if (!function_exists('webmd5')){
    function webmd5($arg){
        return md5(md5($arg));
    }
}

//请求体获取token
if (!function_exists('tokenGetter')) {
    function tokenGetter(\think\Request $request)
    {
        if (empty($request->post("user_token"))) return false;
        $token = $request->post("user_token");
        return $token;
    }
}
//生成验证签名token验证
function signToken($uid,$uname){
    $key='*^%#*$9shit';         //密钥
    $token=array(
        "iss"=>$key,        //签发者
        "aud"=>'',          //面象的用户
        "iat"=>time(),      //签发时间
        "nbf"=>time()+3,    //在什么时候jwt开始生效
        "exp"=> time()+20000, //token 过期时间
        "data"=>[           //记录的user的信息
            'uid'=>$uid,
            'uname'=>$uname
        ]
    );
    //  print_r($token);
    $jwt = JWT::encode($token, $key, "HS256");
    return json(["token"=>$jwt]);
}


//验证token
function checkToken($token){
    $key='*^%#*$9shit';
    $status=array("code"=>2);
    try {
        JWT::$leeway = 60; //当前时间减去60,把时间留点余地
        $decoded = JWT::decode($token, $key, array('HS256')); //HS256方式，这里要和签发的时候对应
        $arr = (array)$decoded;
        $res['code']=ComConstant::e_api_sign_success;
        $res['data']=$arr['data'];
        return $res;

    } catch(\Firebase\JWT\SignatureInvalidException $e) { //签名不正确
        $status['res']="签名不正确";
        $status['code'] = ComConstant::e_api_sign_wrong;
        return $status;
    }catch(\Firebase\JWT\BeforeValidException $e) { // 签名在某个时间点之后才能用
        $status['res']="token失效";
        $status['code'] = ComConstant::e_api_sign_expired;
        return $status;
    }catch(\Firebase\JWT\ExpiredException $e) { // token过期
        $status['res']="token失效";
        $status['code'] = ComConstant::e_api_sign_expired;
        return $status;
    }catch(Exception $e) { //其他错误
        $status['res']="签名不存在";
        $status['code'] = ComConstant::e_api_sign_miss;
        return $status;
    }
}