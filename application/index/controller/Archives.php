<?php
namespace app\index\controller;
use think\Db;
class Archives extends Base{
    public function index(){
        $param = $this->request->get();
        $this->assign('param',$param);
        return $this->fetch();
    }

    /**
     * 添加
     */
    public function add(){
        if(request()->isPost()){
            $params = array_filter(request()->post());
            if(isset($params['const_time']) && !empty($params['const_time'])){
                $params['const_time'] = strtotime($params['const_time']);
            }
            if(isset($params['test_time']) && !empty($params['test_time'])){
                $params['test_time'] = strtotime($params['test_time']);
            }

            if(isset($params['pic']) && !empty($params['pic'])){
                $params['pic'] = json_encode($params['pic']);
            }
            $params['create_t'] = time();
            if (Db::name('archives')->insert($params)){
                return json(['code'=>0,'msg'=>'ok']);
            }else{
                return json(['code'=>-1,'msg'=>'error']);
            }
        }
        $pipes = Db::name('pipes')->order('id desc')->select();
        $this->assign('pipes',$pipes);
        $branch = Db::name('branch')->order('id desc')->select();
        $this->assign('branch',$branch);
        return $this->fetch();
    }

    /**
     * 编辑
     */
    public function edit(){
        if(request()->isPost()){
            $params = array_filter(request()->post());
            if(isset($params['const_time']) && !empty($params['const_time'])){
                $params['const_time'] = strtotime($params['const_time']);
            }
            if(isset($params['test_time']) && !empty($params['test_time'])){
                $params['test_time'] = strtotime($params['test_time']);
            }

            if(isset($params['pic']) && !empty($params['pic'])){
                $params['pic'] = json_encode($params['pic']);
            }else{
                $params['pic'] = '';
            }
            if (Db::name('archives')->update($params)){
                return json(['code'=>0,'msg'=>'ok']);
            }else{
                return json(['code'=>-1,'msg'=>'error']);
            }
        }
        $id = input('id');
        $pipes = Db::name('pipes')->order('id desc')->select();
        $this->assign('pipes',$pipes);
        $branch = Db::name('branch')->order('id desc')->select();
        $this->assign('branch',$branch);
        $field = Db::name('archives')->where(['id'=>$id])->find();
        if(!empty($field['pic'])){
            $field['pic'] = json_decode($field['pic'],true);
        }
        $this->assign('field',$field);
        return $this->fetch();
    }

    public function del(){
        //异常捕获
        try{
            if(request()->isPost()){
                $post = request()->post();
                if(Db::name('archives')->where(['id'=>$post['id']])->delete()){
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
     * 审核
     */
    public function audit(){
        if(request()->isPost()){
            $params = request()->post();
            $user = session('user');
            $params['audit_id'] = $user['id'];
            if (Db::name('archives')->update($params)){
                return json(['code'=>0,'msg'=>'ok']);
            }else{
                return json(['code'=>-1,'msg'=>'error']);
            }
        }
        $id = input('id');
        $admins = Db::name('admin')->where('id not in (1)')->select();
        $this->assign('admins',$admins);
        $field = Db::name('archives')->where(['id'=>$id])->find();
        $this->assign('field',$field);
        return $this->fetch();
    }

    /**
     * 查看
     */
    public function show(){
        $id = input('id');
        $field = Db::name('archives')->where(['id'=>$id])->find();
        if(!empty($field['pic'])){
            $field['pic'] = json_decode($field['pic'],true);
        }
        if(!empty($field['enter_id'])){
            $branch = Db::name('branch')->where(['id'=>$field['enter_id']])->find();
            $field['enter_id'] = $branch['branch_name'];
        }
        if(!empty($field['pipe_id'])){
            $branch = Db::name('pipes')->where(['unit_id'=>$field['pipe_id']])->find();
            $field['pipe_id'] = $branch['name'];
        }
        $this->assign('field',$field);
        return $this->fetch();
    }
}
