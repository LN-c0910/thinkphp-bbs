<?php

namespace app\http\middleware;

class CheckLogin
{
    public function handle($request, \Closure $next,$role)
    {
        //判断用户是否登录
        if ($role==1){
            if (!session('admin.userInfo')){
                return redirect(url('admin/index/index'),401)->with('admin.error','暂无权限,拒绝访问');
            }
        }else{
            if (!session('usr.userInfo')){
                return null;
            }
        }
        return $next($request);
    }
}
