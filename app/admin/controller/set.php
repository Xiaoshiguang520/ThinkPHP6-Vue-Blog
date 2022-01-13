<?php


namespace app\admin\controller;

use think\facade\Cookie;
use think\Facade\View;
use think\Facade\Db;
use think\facade\Request;

class set{
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
     * 常规设置
     */
    public function systemSet(){
        $system=Db::table('blog_system')->find(1);
        View::assign([
            'system'=>$system
        ]);
        return View::fetch();
    }
    /**
     * 关于页设置
     */
    public function aboutSet(){
        if (Request::method() == 'POST') {
            $data = Request::param();
            $list = Db::table('blog_page')->where('id',(int)$data['id'])->update($data);
            echo json_encode(['code' => 0]);
        } else if (Request::method() == 'GET') {
            $blog_page = Db::table('blog_page')->where('id',1)->find();
            View::assign([
                'blog_page' => $blog_page,
            ]);
            return View::fetch();
        }
    }
    /**
     * 修改常规设置
     */
    public function updateSystem(){
        $data = Request::param();
        //echo json_encode(['code'=>1,'data' => $data]);
        $flag=Db::table('blog_system')->where('id',(int)$data['id'])->update($data);
        if($flag){
            echo json_encode(['code' => 0]);
        }else{
            echo json_encode(['code' => 1]);
        }
    }
    /**
     * 修改常规设置
     */
    public function updateLogo(){
        $file = request()->file('image');
        $savename = \think\facade\Filesystem::disk('public')->putFileAs( 'logo', $file,'logo.jpg');
        Db::table('blog_system')->where('id',1)->update(['sys_logo'=>$savename]);
        echo json_encode(['data' => $savename]);
    }
}
