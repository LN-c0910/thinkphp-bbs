<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Section extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {

            if (!$request->isPost()) return json(["msg"=>"请求错误"],400);
            $img = $request->file('simg');
            $sname = $request->post('sname');
            $sstatement = $request->post('sstatement');
            $smasterid = session('admin.userInfo')['uid'];
            if (!$smasterid){
                 return json('暂无权限,请重新登录',403);
            }
            $info = $img->move(get_public_path().'/static/img/section');
            if($info){
                $path = $info->getSaveName();
                $addSection = \app\common\model\Section::addSection(
                    $sname, $sstatement, $smasterid, '/static/img/section/' . $path);
                if (!$addSection){
                    return json('添加失败',500);
                }
                echo '<script language="javascript">';
                echo 'parent.location.reload();';
                echo '</script>';
            }else{
                // 上传失败获取错误信息
                return json($img->getError(),500);
            }

    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {

        $deleteSectionBySid = \app\common\model\Section::deleteSectionBySid($id);
        if ($deleteSectionBySid){
            return json(["msg"=>"删除成功"],200);
        }
        return json(["msg"=>"删除失败"],403);
    }
    /**
     * 修改板块信息
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function section_edit($id,Request $request)
    {

        if (!$request->isPost()) return json(["msg"=>"请求错误"],400);
        $img = null;
        if($request->file()){
            $img = $request->file('simg');
        }
        $sname = $request->post('sname');
        $sstatement = $request->post('sstatement');
        $smasterid = session('admin.userInfo')['uid'];

        if (!$smasterid){
            return json('暂无权限,请重新登录',403);
        }
        if ($img){
            $info = $img->move(get_public_path().'/static/img/section');
            if($info) {
                $path = $info->getSaveName();
                $updateSection = \app\common\model\Section::updateSectionBySid(
                    $id, $sname, $sstatement, '/static/img/section/' . $path);
                if (!$updateSection) {
                    return json('添加失败', 500);
                }
                try {
                    unlink('./'.$updateSection);
                    echo '<script language="javascript">';
                    echo 'parent.location.reload();';
                    echo '</script>';
                } catch (\Exception $e) {
                    return json("删除错误");
                }
            }else{
                // 上传失败获取错误信息
                return json($img->getError(),500);
            }
        }else{
                 \app\common\model\Section::updateSectionBySid(
                    $id,$sname, $sstatement,null);
            echo '<script language="javascript">';
            echo 'parent.location.reload();';
            echo '</script>';
        }



    }
    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }


}
