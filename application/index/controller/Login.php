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

    public function register(){
        //初始化admin账户的密码
        //echo password_hash(config('database.auth_key').'123456',PASSWORD_BCRYPT);
        $region = Db::name('region')->where(['region_status'=>0])->order('id desc')->select();
        $this->assign('region',$region);
        return $this->fetch();
    }

    /**
     * loginAction  注册请求方法
     */
    public function registerAction(){
        try {
            if (request()->isPost()){
                $params = array_filter(request()->post());
                $field = Db::name('admin')->where(['user_name'=>$params['user_name']])->find();
                if ($field){
                    return json(['code'=>-1,'msg'=>'用户名已存在']);
                }
                $region = Db::name('region')->where(['id'=>$params['region_id']])->find();
                $params['group_id'] = $region['group_id'];
                $params['user_pass'] = password_hash(config('database.auth_key').$params['user_pass'],PASSWORD_BCRYPT);
                $params['create_time'] = date('Y-m-d H:i:s');
                if (Db::name('admin')->insert($params)){
                    $user = Db::name('admin')->where(['user_name'=>$params['user_name']])->find();
                    $insert = [
                        'uid' => $user['id'],
                        'role_id' => $params['group_id']
                    ];
                    Db::name('group_rules')->insert($insert);
                    return json(['code'=>0,'msg'=>'注册成功']);
                }else{
                    return json(['code'=>-1,'msg'=>'注册失败']);
                }
            }
            return json(['code'=>-1,'msg'=>'请求错误']);
        }catch (\Exception $e){
            return json(['code'=>-1,'msg'=>$e->getMessage()]);
        }
    }
}