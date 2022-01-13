<?php

declare(strict_types=1);

namespace app\login\controller;

use think\facade\View;
use think\facade\Request;
use think\facade\Db;
use think\facade\Cookie;


class Index
{
    //登录页
    public function index()
    {
        $user = Cookie::get('account');
        if ($user != null) {
            redirect('/admin')->send();
        } else {
            //获取颜色表
            $data = Db::table('blog_theme')->where('id', 2)->find();
            View::assign([
                'data' => $data
            ]);
            return View::fetch();
        }
    }
    //判断登录
    public function logins()
    {
        $test = Request::param();
        $find = Db::table('blog_user')->where($test)->find();
        if ($find) {
            Cookie::set('account', $test['account']);
            Cookie::set('name', $find['name']);
            echo json_encode(['code' => 0]);
        } else {
            echo json_encode(['code' => 1, 'data' => $find]);
        }
    }
    //注册页
    public function register()
    {
        //获取颜色表
        $data = Db::table('blog_theme')->where('id', 2)->find();
        View::assign([
            'data' => $data
        ]);
        return View::fetch();
    }
    //注册信息
    public function registers()
    {
        $test = Request::param();
        $isfind=Db::table('blog_user')->where("account",$test["account"])->find();
        if(!$isfind){
            $test['permission_id'] = 2;
            $test['addTime'] = date('Y-m-d H:i:s', time());
            $test['logourl'] = 'image/logo/null/logo.jpg';
            // echo json_encode(['code' => 1, 'datas' => $test]);
            $find = Db::table('blog_user')->insert($test);
            if ($find) {
                echo json_encode(['code' => 0, 'datas' => $find]);
            } else {
                echo json_encode(['code' => 1, 'datas' => $find]);
            }
        }else{
            echo json_encode(['code' => 2]);
        }
        
    }
}
