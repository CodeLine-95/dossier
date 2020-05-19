<?php
namespace app\index\controller;

class Index extends Base {
    public function index(){
        return $this->fetch();
    }

    public function logout(){
        session('user',null);
        session(null);
        return json(['code'=>0,'msg'=>'ok']);
    }
}
