<?php
namespace app\index\controller;
use think\Controller;
use think\Db;

/**
 * Class Login 登录
 * @package app\index\controller
 */
class Login extends Controller{
    public function index(){
        //初始化admin账户的密码
        //echo password_hash(config('database.auth_key').'123456',PASSWORD_BCRYPT);
        return $this->fetch();
    }

    /**
     * loginAction  登录请求方法
     */
    public function loginAction(){
        try {
            if (request()->isPost()){
                $params = request()->post();
                $field = Db::name('admin')->where(['user_name'=>$params['user_name']])->find();
                if (!$field) return json(['code'=>-1,'msg'=>'用户名不存在']);
                if (!password_verify(config('database.auth_key').$params['user_pass'],$field['user_pass'])) return json(['code'=>-1,'msg'=>'密码错误']);

                session('user',$field);
                return json(['code'=>0,'msg'=>'登录成功']);
            }
            return json(['code'=>-1,'msg'=>'请求错误']);
        }catch (\Exception $e){
            return json(['code'=>-1,'msg'=>$e->getMessage()]);
        }

    }
}