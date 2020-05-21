<?php
namespace app\index\controller;
use think\Db;
class Region extends Base{
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
            $params = request()->post();
            $field = Db::name('region')->where(['region_name'=>$params['region_name']])->find();
            if ($field){
                return json(['code'=>-1,'msg'=>'区域名已存在']);
            }
            if (Db::name('region')->insert($params)){
                return json(['code'=>0,'msg'=>'ok']);
            }else{
                return json(['code'=>-1,'msg'=>'error']);
            }
        }
        $group = Db::name('group')->where('id != 1')->order('id desc')->select();
        $this->assign('group',$group);
        return $this->fetch();
    }
    /**
     * 编辑
     */
    public function edit(){
        if(request()->isPost()){
            $params = request()->post();
            $field = Db::name('region')->where(['region_name'=>$params['region_name']])->find();
            if ($field){
                if($params['region_name'] != $params['region_name_old']){
                    return json(['code'=>-1,'msg'=>'区域已存在']);
                }
            }
            unset($params['region_name_old']);
            if (Db::name('region')->update($params)){
                return json(['code'=>0,'msg'=>'ok']);
            }else{
                return json(['code'=>-1,'msg'=>'error']);
            }
        }
        $id = input('id');
        $field = Db::name('region')->where(['id'=>$id])->find();
        $this->assign('field',$field);
        $group = Db::name('group')->where('id != 1')->order('id desc')->select();
        $this->assign('group',$group);
        return $this->fetch();
    }

    public function del(){
        //异常捕获
        try{
            if(request()->isPost()){
                $post = request()->post();
                if(Db::name('region')->where(['id'=>$post['id']])->delete()){
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
