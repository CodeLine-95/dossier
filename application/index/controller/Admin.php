<?php
namespace app\index\controller;
use think\Db;
/**
 * 用户类
 */
class Admin extends Base{
    public function index(){
        $param = request()->get();
        $this->assign('param',$param);
        return $this->fetch();
    }

    public function add(){
        if (request()->isPost()){
            try {
                $params = array_filter(request()->post());
                $field = Db::name('admin')->where(['user_name'=>$params['user_name']])->find();
                if ($field){
                    return json(['code'=>-1,'msg'=>'用户名已存在']);
                }
                $params['user_pass'] = password_hash(config('database.auth_key').$params['user_pass'],PASSWORD_BCRYPT);
                $params['create_time'] = date('Y-m-d H:i:s');
                if (Db::name('admin')->insert($params)){
                    return json(['code'=>0,'msg'=>'ok']);
                }else{
                    return json(['code'=>-1,'msg'=>'error']);
                }
            }catch (\Exception $e){
                return json(['code'=>-1,'msg'=>$e->getMessage()]);
            }
        }else{
            $region = Db::name('region')->order('id desc')->select();
            $this->assign('region',$region);
            return $this->fetch();
        }
    }

    public function edit(){
        if (request()->isPost()){
            try {
                $params = array_filter(request()->post());
                //不为空更新
                if (isset($params['user_pass']) && !empty($params['user_pass'])) {
                    $params['user_pass'] = password_hash(config('database.auth_key') . $params['user_pass'], PASSWORD_BCRYPT);
                }
                $params['update_time'] = date('Y-m-d H:i:s');
                if (Db::name('admin')->update($params)){
                    return json(['code'=>0,'msg'=>'ok']);
                }else{
                    return json(['code'=>-1,'msg'=>'error']);
                }
            }catch (\Exception $e){
                return json(['code'=>-1,'msg'=>$e->getMessage()]);
            }
        }else{
            $id = input('id');
            $field = Db::name('admin')->where(['id'=>$id])->find();
            $this->assign('field',$field);
            $region = Db::name('region')->order('id desc')->select();
            $this->assign('region',$region);
            return $this->fetch();
        }
    }
    /**
     * 删除用户操作
     * post 请求
     * @return string
     */
    public function del(){
        //异常捕获
        try{
            if(request()->isPost()){
                $post = request()->post();
                if(Db::name('admin')->where(['id'=>$post['id']])->delete()){
                    return json(['code'=>0,'msg'=>'删除成功']);
                }else{
                    return json(['code'=>-1,'msg'=>'删除失败']);
                }
            }else{
                return json(['code'=>-1,'msg'=>'访问错误']);
            }
        }catch(\Exception $e){
            //返回捕获的异常信息
            return json(['code'=>-1,'msg'=>$e->getMessage()]);
        }
    }

    /**
     * 分配
     */
    public function audit(){
        if(request()->isPost()){
            $params = request()->post();
            $field = Db::name('group_rules')->where(['uid'=>$params['uid']])->find();
            if($field){
                $params['id'] = $field['id'];
                if (Db::name('group_rules')->update($params)){
                    Db::name('admin')->update(['id'=>$params['uid'],'group_id'=>$params['role_id'],'region_id'=>$params['region_id']]);
                    
                    return json(['code'=>0,'msg'=>'ok']);
                }else{
                    return json(['code'=>-1,'msg'=>'error']);
                }
            }else{
                if (Db::name('group_rules')->insert($params)){
                    Db::name('admin')->update(['id'=>$params['uid'],'group_id'=>$params['role_id'],'region_id'=>$params['region_id']]);
                    return json(['code'=>0,'msg'=>'ok']);
                }else{
                    return json(['code'=>-1,'msg'=>'error']);
                }
            }
        }
        $id = input('id');
        $group = Db::name('group')->where('id not in (1)')->select();
        $this->assign('group',$group);
        $field = Db::name('admin')->where(['id'=>$id])->find();
        $this->assign('field',$field);
        $region = Db::name('region')->where(['group_id'=>$group['id']])->order('id desc')->select();
        $this->assign('region',$region);
        return $this->fetch();
    }
}