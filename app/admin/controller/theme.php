<?php


namespace app\admin\controller;

use think\facade\Cookie;
use think\Facade\View;
use think\Facade\Db;
use think\facade\Request;

class theme{
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
     * 首页外观
     */
    public function indexTheme(){
        if(Request::method()=='POST'){
            $test=Request::param();
            //echo json_encode(['code'=>1,'data'=>$test]);
            $flag=Db::table('blog_theme')->where('id',1)->update($test);
            if($flag){
                echo json_encode(['code'=>0]);
            }else{
                echo json_encode(['code'=>1,'data'=>'请检查是否修改']);
            }
        }else{
            $data=Db::table('blog_theme')->where('id',1)->find();
            View::assign([
                'data'=>$data
            ]);
            return View::fetch();
        }   
    }
    /**
     * 注册页外观
     */
    public function loginTheme(){
        if(Request::method()=='POST'){
            $test=Request::param();
            //echo json_encode(['code'=>1,'data'=>$test]);
            $flag=Db::table('blog_theme')->where('id',2)->update($test);
            if($flag){
                echo json_encode(['code'=>0]);
            }else{
                echo json_encode(['code'=>1,'data'=>'请检查是否修改']);
            }
        }else{
            $data=Db::table('blog_theme')->where('id',2)->find();
            View::assign([
                'data'=>$data
            ]);
            return View::fetch();
        } 
    }
}
?>