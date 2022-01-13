<?php


namespace app\admin\controller;

use think\facade\Cookie;
use think\Facade\View;
use think\Facade\Db;

class Index
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

    public function index()
    {
        $user = Cookie::get('account');
        //系统设置
        $system = Db::table('blog_system')->select();
        //头像菜单
        $logomenu = [
            ['title' => '首页', 'url' => '/index'],
            ['title' => '个人信息', 'url' => '/admin/user/userData'],
            ['title' => '修改密码', 'url' => '/admin/user/passwordUser'],
            ['title' => '退出登录', 'url' => '/admin/index/isCookie']
        ];
        //查找当前用户信息
        $find = Db::table('blog_user')->where('account', $user)->find();
        //判断管理权限
        if ($find['permission_id'] == 2) {
            //左侧菜单
            $menus = Db::table('blog_menu')->where('pid', 0)->where('permission_id', 2)->select();
            $menu = $menus->toArray();
            /**
             * 菜单数据
             */
            foreach ($menu as &$menu_v) {
                $menu_v['list'] = Db::table('blog_menu')->where('pid', $menu_v['id'])->where('permission_id', 2)->select();
            }
        } else {
            //左侧菜单
            $menus = Db::table('blog_menu')->where('pid', 0)->select();
            $menu = $menus->toArray();
            /**
             * 菜单数据
             */
            foreach ($menu as &$menu_v) {
                $menu_v['list'] = Db::table('blog_menu')->where('pid', $menu_v['id'])->select();
            }
        }
        View::assign([
            'user' => $find,
            'title' => '控制台',
            'titlemini' => '管理',
            'logomenus' => $logomenu,
            'menus' => $menu,
            'system' => $system
        ]);
        return View::fetch();
    }
    public function main()
    {
        //获取当前IP
        $ip = $_SERVER["REMOTE_ADDR"];
        $user = Cookie::get('account');
        //统计
        $statistical1 = Db::table('blog_user')->count();
        $statistical2 = Db::table('blog_article')->count();
        $statistical3 = Db::table('blog_comments')->count();
        //查找当前用户信息
        $find = Db::table('blog_user')->where('account', $user)->find();
        //近期发布文章
        $list = Db::table('blog_article')->alias('a')->join('blog_class b', 'a.class_id=b.class_id')->order('article_id', 'desc')->page(1, 4)->select();
        //近期评论
        $comments = Db::table('blog_comments')->alias('a')->join('blog_article b', 'a.article_id=b.article_id')->join('blog_user c', 'a.comments_author=c.name')->order('a.comments_time', 'desc')->page(1, '3')->select();
        View::assign([
            'article' => $list,
            'user' => $find,
            'comments' => $comments,
            'ip' => $ip,
            'statistical1' => $statistical1,
            'statistical2' => $statistical2,
            'statistical3' => $statistical3
        ]);
        //print_r($find);
        return View::fetch();
    }
    /**
     * 退出按钮
     */
    public function isCookie()
    {
        $user = Cookie::get('account');
        if ($user != null) {
            Cookie::delete('account');
            Cookie::delete('name');
            //重定向
            redirect('/login')->send();
        }
    }
}
