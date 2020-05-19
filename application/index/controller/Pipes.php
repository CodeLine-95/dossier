<?php
namespace app\index\controller;
use think\Db;
class Pipes extends Base{
    public function index(){
        $pipes = Db::name('pipes')->order('id desc')->select();
        $this->assign('pipes',$pipes);
        return $this->fetch();
    }

    /**
     * 添加
     */
    public function add(){
        if(request()->isPost()){
            $params = request()->post();
            $field = Db::name('pipes')->where(['name'=>$params['name']])->find();
            if ($field){
                return json(['code'=>-1,'msg'=>'管道名已存在']);
            }
            $params['unit_id'] = strtoupper($params['unit_id']);
            if (Db::name('pipes')->insert($params)){
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
            $field = Db::name('pipes')->where(['name'=>$params['name']])->find();
            if ($field){
                if($params['name'] != $params['name_old']){
                    return json(['code'=>-1,'msg'=>'管道已存在']);
                }
            }
            unset($params['name_old']);
            if (Db::name('pipes')->update($params)){
                return json(['code'=>0,'msg'=>'ok']);
            }else{
                return json(['code'=>-1,'msg'=>'error']);
            }
        }
        $id = input('id');
        $field = Db::name('pipes')->where(['id'=>$id])->find();
        $this->assign('field',$field);
        return $this->fetch();
    }

    public function del(){
        //异常捕获
        try{
            if(request()->isPost()){
                $post = request()->post();
                if(Db::name('pipes')->where(['id'=>$post['id']])->delete()){
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
