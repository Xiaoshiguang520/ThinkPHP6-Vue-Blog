<?php

declare(strict_types=1);

namespace app\index\controller;

use think\facade\Cookie;
use think\facade\Request;
use think\facade\View;
use think\facade\Db;

class Index
{
    public function __construct()
    {
        $system = Db::table('blog_system')->find(1);
        $data = Db::table('blog_theme')->where('id', 1)->find();
        View::assign([
            'system' => $system,
            'data' => $data
        ]);
    }
    /**
     * 首页
     */
    public function index()
    {
        $param = Request::param();
        $p = isset($param['p']) ? $param['p'] : 1;
        $count = Db::table('blog_article')->count();
        $list = Db::table('blog_article')->alias('a')->join('blog_class b', 'a.class_id=b.class_id')->order('article_id', 'desc')->page((int)$p, 5)->select();
        View::assign([
            'article' => $list,
            'count' => $count,
            'p' => $p
        ]);
        return View::fetch();
    }
    /**
     * 模板
     */
    public function main()
    {
        return View::fetch();
    }
    /**
     * 分类
     */
    public function class()
    {
        //$year=Db::query('select DISTINCT(YEAR(time)) from blog_article');
        //年份查询
        $test = Db::table('getTimes')->order('year', 'desc')->select();
        $left = $test->toArray();
        //年份文章查询
        foreach ($left  as &$left_v) {
            $left_v['list'] = Db::table('blog_article')->where('time', 'like', ($left_v['year'] . '%'))->order('time', 'desc')->select();
        }
        View::assign([
            'test' => $left,
        ]);
        //print_r($left);
        return View::fetch();
    }
    /**
     * 关于
     */
    public function about()
    {
        $blog_page = Db::table('blog_page')->where('id', 1)->find();
        View::assign([
            'blog_page' => $blog_page,
        ]);
        return View::fetch();
    }
    /**
     * 文章详细
     */
    public function archives()
    {
        //用户获取
        $user = Cookie::get('name');
        //文章获取
        $id = Request::param('id');
        $data = Db::table('blog_article')->alias('a')->join('blog_class b', 'a.class_id=b.class_id')->where('article_id', $id)->order('article_id', 'desc')->find();
        //评论分页
        $param = Request::param();
        $reply = isset($param['reply']) ? $param['reply'] : 1;
        $count = Db::table('blog_comments')->where('article_id', $id)->where('comments_ids', null)->count();
        //评论获取
        $comments = Db::table('blog_comments')->alias('a')->join('blog_user b', 'a.comments_author=b.name')->where('a.article_id', $id)->where('a.comments_ids', null)->order('comments_time', 'desc')->page((int)$reply, 4)->select();
        $lcomments = $comments->toArray();
        //回复查询
        foreach ($lcomments  as &$lcomments_v) {
            $lcomments_v['list'] = Db::table('blog_comments')->alias('a')->join('blog_user b', 'a.comments_author=b.name')->where('article_id', $id)->where('comments_ids', $lcomments_v['comments_id'])->order('comments_time', 'desc')->select();
        }
        View::assign([
            'article' => $data,
            'comments' => $lcomments,
            'id' => $id,
            'reply' => $reply,
            'count' => $count,
            'user' => $user,
        ]);
        //print_r($reply);
        return View::fetch();
    }
    /**
     * 添加评论
     */
    public function addReply()
    {
        if (Cookie::get('name') != null) {
            $test = Request::param();
            $test['comments_author'] = Cookie::get("name");
            $test['comments_time'] = date('Y-m-d H:i:s', time());
            //echo json_encode(['code' => 1,'test'=>$test]);
            $comments = Db::table('blog_comments')->insert($test);
            if ($comments) {
                echo json_encode(['code' => 0]);
            } else {
                echo json_encode(['code' => 1]);
            }
        }else{
            echo json_encode(['code' => 1]);
        }
    }
}
