<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

include get_route_path().'/admin/route.php';
include get_route_path().'/api/route.php';

//引入Route
use think\facade\Route;

//帖子图片上传
Route::post('/api/uploadImg','@common/UploadImg/uploadImg')
    ->name('common/UploadImg/uploadImg')
    ->allowCrossDomain();

//用户头像图片上传
Route::post('/api/uploadFace','@common/UploadImg/uploadFace')
    ->name('common/UploadImg/uploadFace')
    ->allowCrossDomain();

Route::get('/','@index/index/index');

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});

Route::get('hello/:name', '@index/index/hello');

