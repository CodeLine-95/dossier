<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
/**
 * 角色-组类
 */
class Branch extends Base{
    public function index(){
        $branch = Db::name('branch')->order('id','desc')->paginate(10);
        $this->assign('branch',$branch);
        return $this->fetch();
    }

    /**
     * 添加
     */
    public function add(){
        if(request()->isPost()){
            $params = request()->post();
            $field = Db::name('branch')->where(['branch_name'=>$params['branch_name']])->find();
            if ($field){
                return json(['code'=>-1,'msg'=>'公司已存在']);
            }
            if (Db::name('branch')->insert($params)){
                return json(['code'=>0,'msg'=>'ok']);
            }else{
                return json(['code'=>-1,'msg'=>'error']);
            }
        }
        return $this->fetch();
    }
    /**
     * 编辑
     */
    public function edit(){
        if(request()->isPost()){
            $params = request()->post();
            $field = Db::name('branch')->where(['branch_name'=>$params['branch_name']])->find();
            if ($field){
                if($params['branch_name'] != $params['branch_name_old']){
                    return json(['code'=>-1,'msg'=>'角色已存在']);
                }
            }
            unset($params['branch_name_old']);
            if (Db::name('branch')->update($params)){
                return json(['code'=>0,'msg'=>'ok']);
            }else{
                return json(['code'=>-1,'msg'=>'error']);
            }
        }
        $id = input('id');
        $field = Db::name('branch')->where(['id'=>$id])->find();
        $this->assign('field',$field);
        return $this->fetch();
    }

    public function del(){
        //异常捕获
        try{
            if(request()->isPost()){
                $post = request()->post();
                if(Db::name('branch')->where(['id'=>$post['id']])->delete()){
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
}