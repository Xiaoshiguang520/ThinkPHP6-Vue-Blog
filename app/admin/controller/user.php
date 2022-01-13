<?php


namespace app\admin\controller;

use think\facade\Cookie;
use think\Facade\View;
use think\Facade\Db;
use think\Facade\Request;

class user
{
    public function __construct()
    {
        $user = Cookie::get('account');
        $usercookie = Db::table('blog_user')->where('account', $user)->find();
        if (!$usercookie) {
            //没有登录状态
            Cookie::delete('account');
            Cookie::delete('name');
            redirect('/login')->send();
        }
    }
    /**
     * 用户管理
     */
    public function userController()
    {
        $param = Request::param();
        if (isset($param['finduser'])) {
            //查找用户
            $class = $param['class'];
            $test = $param['test'];
            $p = isset($param['p']) ? $param['p'] : 1;
            $count = Db::table('blog_user')->count();
            $uesr = Db::table('blog_user')->where($class, $test)->select();
            View::assign([
                'user' => $uesr,
                'count' => $count,
                'p' => $p,
                'find' => true,
                'class' => $class,
                'test' => $test
            ]);
            return View::fetch();
        } else {
            $p = isset($param['p']) ? $param['p'] : 1;
            $count = Db::table('blog_user')->count();
            $uesr = Db::table('blog_user')->page($p, '10')->select();
            View::assign([
                'user' => $uesr,
                'count' => $count,
                'p' => $p,
                'find' => false,
                'class' => '',
                'test' => ''
            ]);
            return View::fetch();
        }
    }
    /**
     * 个人资料
     */
    public function userData()
    {
        $user = Cookie::get('account');
        $data = Db::table('blog_user')->where('account', $user)->find();
        View::assign([
            'user' => $data,
        ]);
        return View::fetch();
    }
    /**
     * 密码修改
     */
    public function passwordUser()
    {
        $user = Cookie::get('account');
        View::assign([
            'user' => $user
        ]);
        return View::fetch();
    }
    /**
     * 用户修改
     */
    public function updateUser()
    {
        $data = Request::param();
        $flag = Db::table('blog_user')->where('id', (int)$data['id'])->update($data);
        Cookie::set('name', $data['name']);
        //echo json_encode(['code' => 1,'data'=>$data]);
        if ($flag) {
            echo json_encode(['code' => 0]);
        } else {
            echo json_encode(['code' => 1]);
        }
    }
    /**
     * 用户删除
     */
    public function delUser()
    {
        $data = Request::param();
        //删除评论
        $testt = Db::table('blog_user')->where('id', (int)$data['id'])->find();
        Db::table('blog_comments')->where('comments_author', $testt['name'])->delete();
        //删除头像以及文件
        if ($testt['logourl'] != 'image/logo/null/logo.jpg') {
            unlink("./storage/image/logo/" . $testt['name'] . "/logo.jpg");
            rmdir("./storage/image/logo/" . $testt['name']);
        }
        //删除用户
        $flag = Db::table('blog_user')->where('id', (int)$data['id'])->delete();
        if ($flag) {
            echo json_encode(['code' => 0]);
        } else {
            echo json_encode(['code' => 1, 'message' => '检查当前密码是否正确']);
        }
    }
    /**
     * 密码修改
     */
    public function updatePassword()
    {
        $data = Request::param();
        //echo json_encode(['code' => 1,'data'=>$data]);
        $flag = Db::table('blog_user')->where('password', $data['oldPassword'])->update(['password' => $data['newPassword']]);
        if ($flag) {
            echo json_encode(['code' => 0]);
        } else {
            echo json_encode(['code' => 1, 'message' => '检查当前密码是否正确']);
        }
    }
    /**
     * 头像修改
     */
    public function logoUpdate()
    {
        $file = request()->file('image');
        $user = Cookie::get('account');
        $savename = \think\facade\Filesystem::disk('public')->putFileAs('image/logo/' . $user, $file, 'logo.jpg');
        Db::table('blog_user')->where('account', $user)->update(['logourl' => $savename]);
        echo json_encode(['data' => $savename]);
    }
}
