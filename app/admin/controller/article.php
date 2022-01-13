<?php


namespace app\admin\controller;

use think\facade\Cookie;
use think\Facade\View;
use think\Facade\Db;
use think\facade\Request;

class article
{
    public function __construct(){
        $user=Cookie::get('account');
        $usercookie = Db::table('blog_user')->where('account',$user)->find();
        if (!$usercookie) {
            //没有登录状态
            Cookie::delete('account');
            Cookie::delete('name');
            redirect('/login')->send();
        }
    } 

    /**
     * 文章列表
     */
    public function articleLists()
    {
        $param = Request::param();
        $p=isset($param['p']) ? $param['p'] : 1;
        $count=Db::table('blog_article')->count();
        $list = Db::table('blog_article')->alias('a')->join('blog_class b', 'a.class_id=b.class_id')->order('article_id', 'desc')->page((int)$p,10)->select();
        View::assign([
            'article' => $list,
            'count' => $count,
            'p'=>$p
        ]);
        return View::fetch();
    }
    /**
     * 写文章
     */
    public function addList()
    {
        if (Request::method() == 'POST') {
            $data = Request::param();
            $data['author'] = Cookie::get('name');
            $data['time'] = date('Y-m-d H:i:s', time());
            $list = Db::table('blog_article')->insert($data);
            echo json_encode(['code' => 0]);
        } else if (Request::method() == 'GET') {
            $class = Db::table('blog_class')->select();
            View::assign([
                'class' => $class,
            ]);
            return View::fetch();
        }
    }
    /**
     * 修改文章
     */
    public function updateList()
    {
        if (Request::method() == 'POST') {
            $data = Request::param();
            $data['article_id']=(int)$data['article_id'];
            $data['author'] = Cookie::get('name');
            $list = Db::table('blog_article')->where('article_id',$data['article_id'])->update($data);
            echo json_encode(['code' => 0, 'data' => $list]);
        } else if (Request::method() == 'GET') {
            $id=Request::param('id');
            $list = Db::table('blog_article')->where('article_id',$id)->find();
            $class = Db::table('blog_class')->select();
            View::assign([
                'article' => $list,
                'class' => $class,
            ]);
            return View::fetch();
        }
    }
    /**
     * 分类
     */
    public function classification()
    {
        $class = Db::table('blog_class')->select();
        View::assign([
            'class' => $class,
        ]);
        return View::fetch();
    }
    /**
     * 添加分类
     */
    public function addclass(){
        $test=Request::param();
        //echo json_encode(['code' => 1,'data'=>$test]);
        $data=Db::table('blog_class')->insert($test);
        if($data){
            echo json_encode(['code' => 0]);
        }else{
            echo json_encode(['code' => 1]);
        }
    }
    /**
     * 修改分类
     */
    public function updateclass(){
        $test=Request::param();
        $test['class_id']=(int)$test['class_id'];
        $data=Db::table('blog_class')->where('class_id',$test['class_id'])->update($test);
        if($data){
            echo json_encode(['code' => 0]);
        }else{
            echo json_encode(['code' => 1,'data'=>$test]);
        }
    }
    /**
     * 删除分类
     */
    public function delclass(){
        $test=Request::param();
        $test['class_id']=(int)$test['class_id'];
        $article=Db::table('blog_article')->where('class_id',$test['class_id'])->count();
        if($article==0){
            $data=Db::table('blog_class')->where('class_id',$test['class_id'])->delete();
        if($data){
            echo json_encode(['code' => 0]);
        }else{
            echo json_encode(['code' => 1]);
        }
        }else{
            echo json_encode(['code' => 2]);
        }
    }
    /**
     * 删除文章
     */
    public function delList(){
        $test=Request::param();
        $test['article_id']=(int)$test['article_id'];
        //删除文章
        $data=Db::table('blog_article')->where('article_id',$test['article_id'])->delete();
        //删除评论
        Db::table('blog_comments')->where('article_id',$test['article_id'])->delete();
        if($data){
            echo json_encode(['code' => 0]);
        }else{
            echo json_encode(['code' => 1,'data'=>$test]);
        }
    }
}
