<?php

namespace app\admin\controller;

use think\facade\Cookie;
use think\Facade\View;
use think\Facade\Db;
use think\Facade\Request;

class comments{
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
     * 所有评论
     */
    public function allComments(){
        $param = Request::param();
        $p=isset($param['p']) ? $param['p'] : 1;
        $count=Db::table('blog_comments')->count();
        $test=Db::table('blog_comments')->alias('a')->join('blog_article b', 'a.article_id=b.article_id')->join('blog_user c', 'a.comments_author=c.name')->order('a.comments_time','desc')->page($p,'10')->select();
        View::assign([
            'test'=>$test,
            'count' => $count,
            'p'=>$p
        ]);
        //print_r($test);
        return View::fetch();
    }
    /**
     * 我的评论
     */
    public function iComments(){
        //先查询当前用户
        $user=Cookie::get('name');
        $param = Request::param();
        $p=isset($param['p']) ? $param['p'] : 1;
        $count=Db::table('blog_comments')->count();
        $test=Db::table('blog_comments')->alias('a')->join('blog_article b', 'a.article_id=b.article_id')->where('a.comments_author',$user)->order('a.comments_time','desc')->page($p,'10')->select();
        View::assign([
            'test'=>$test,
            'count' => $count,
            'p'=>$p
        ]);
        return View::fetch();
    }
    /**
     * 删除评论
     */
    public function delComments(){
        $id=Request::param('id');
        //查询有没有回复
        $se=Db::table('blog_comments')->where('comments_ids',(int)$id)->select();
        if(count($se)!=0){
            foreach($se  as &$se2){
                Db::table('blog_comments')->where('comments_id',$se2['comments_id'])->delete();
            }
        }
        $data=Db::table('blog_comments')->where('comments_id',(int)$id)->delete();
        if($data){
            echo json_encode(['code' => 0]);
        }else{
            echo json_encode(['code' => 1,'data'=>$data]);
        }
    }
    /**
     * 添加评论
     */
    public function addReply(){
        $test=Request::param();
        $test['comments_author']=Cookie::get("name");
        $test['comments_time'] = date('Y-m-d H:i:s', time());
            //echo json_encode(['code' => 1,'test'=>$test]);
            $comments=Db::table('blog_comments')->insert($test);
            if($comments){
                echo json_encode(['code' => 0]);
            }else{
                echo json_encode(['code' => 1]);
            }
        
    }
}
?>