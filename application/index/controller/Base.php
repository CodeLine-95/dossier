<?php
namespace app\index\controller;
use think\Controller; 
use think\Db;
use app\index\model\Rules;
/**
 * Class Base  登录权限统一判断类
 * 统一验证类，登录，权限，菜单二叉树等
 * @package app\index\controller
 */
class Base extends Controller{
    //析构函数，加载类时执行
    public function _initialize(){
        //登录权限统一判断
        $user = session('user');
        if (!$user){
            return $this->redirect(url('index/login/index'));
        }
        $this->assign('user',$user);

        $module = strtolower(request()->module()); //获取当前访问的模块名
        $controller = strtolower(request()->controller()); //获取当前访问的控制器
        $aciton = strtolower(request()->action()); //获取当前访问的方法
        //拼接获得当前的url
        $url = $module .'/'. $controller . '/' . $aciton;
        $this->assign('url',$url);

        //获取菜单权限，并渲染到全局页面中
        $Rules = new Rules();
        if($user['id'] != 1){
            //获取当前登录用户的组
            $group = Db::name('group')->where(['id'=>$user['group_id']])->find();
            $rules = Db::name('rules')->where('id in ('.$group['group_rules'].')')->select();
            $rulesAccess = [];
            //过滤一些必须url，跳过权限验证
            $rulesAccess['rulename'][] = 'index/index/index';
            foreach($rules as $r){
                if($r['rule_url'] !== '#'){
                    $rulesAccess['rulename'][] = $r['rule_url'];
                }
            }
            //权限验证类
            $auth = new \auth\Auth();
            if(in_array($url,$rulesAccess['rulename'])){
                if(!$auth->check($url,$user['id'])){
                    $this->error('抱歉，您没有操作权限');
                }
            }
            $menu = $Rules->getMenu(1,$group['group_rules']);
        }else{
             $menu = $Rules->getMenu(1);
        }
        $this->assign('menu',$menu);
    }
}