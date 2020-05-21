<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\Rules;
use think\Db;

/**
 * 统一api类
 * Class Api
 * @package app\index\controller
 */
class Api extends Controller{
    /**
     * 文件上传
     * @return \think\response\Json
     */
    public function upload(){
        try{
            // 获取表单上传文件 例如上传了001.jpg
            $file = request()->file('file');
            // 移动到框架应用根目录/public/uploads/ 目录下
            if($file){
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                if($info){
                    // 成功上传后 获取上传信息

                    $json = [
                        'ext' => $info->getExtension(),
                        'path' => DS.'uploads'.DS.$info->getSaveName(),
                        'fileName' => $info->getFilename()
                    ];

                    return json(['code'=>0,'msg'=>'ok','data'=>$json]);
                }else{
                    // 上传失败获取错误信息
                    return json(['code'=>-1,'msg'=>$file->getError()]);
                }
            }else{
                return json(['code'=>-1,'msg'=>'未找到上传的文件']);
            }
        }catch (\Exception $e){
            return json(['code'=>-1,'msg'=>$e->getMessage()]);
        }
    }

    /**
     * tree二叉树数组
     */
    public function treeData(){
        if(request()->isPost()){
            $params = request()->post();
            $menu = (new Rules)->getMenu('All',$params['group_rules_old'],true);
            return json(['code'=>0,'msg'=>'获取成功','data'=>$menu]);
        }else{
            return json(['code'=>-1,'msg'=>'访问错误']);
        }
    }

    /**
     * 过滤角色权限中的顶级菜单编号
     */
    public function notParentRules(){
        if(request()->isPost()){
            $params = request()->post();
            if(!empty($params['nodeStr'])){
                $rules = new Rules();
                $result = $rules->where('id in('.$params['nodeStr'].')')->select();
                $menus = $rules->nodeParentMenu($result);
            }else{
                $menus = [];
            }
            return json(['code'=>0,'msg'=>'获取成功','data'=>$menus]);
        }else{
            return json(['code'=>-1,'msg'=>'访问错误']);
        }
    }

    /**
     * 用户ajax列表数据
     */
    public function admin_list(){
        $param = request()->get();
        $where = ' 1=1 and oil_admin.id != 1 ';
        if(!empty($param['name'])){
            $where = $where.' and (oil_admin.user_name like "%'.$param['name'].'%")';
        }
        $param['limit'] = 10;
        //数据总数
        $count = Db::table('oil_admin')
            ->field(['oil_admin.*','oil_group.group_name'])
            ->join('oil_group_rules', 'oil_admin.id=oil_group_rules.uid','LEFT')
            ->join('oil_group', 'oil_group_rules.role_id=oil_group.id','LEFT')
            ->where($where)
            ->count();
        //总页数
        $totalPage = ceil($count/$param['limit']);
        //分页数据
        $admin = Db::table('oil_admin')
            ->field(['oil_admin.*','oil_group.group_name','oil_region.region_name'])
            ->join('oil_region', 'oil_region.id=oil_admin.region_id','LEFT')
            ->join('oil_group_rules', 'oil_admin.id=oil_group_rules.uid','LEFT')
            ->join('oil_group', 'oil_group_rules.role_id=oil_group.id','LEFT')
            ->where($where)
            ->order('oil_admin.id','desc')->paginate($param['limit']);
        //返回值
        return json(['code'=>0,'msg'=>'','pages'=>$totalPage,'data'=>$admin]);
    }

    /**
     * 角色ajax
     */
    public function group_list(){
        $param = request()->get();
        $where = ' 1=1 and id != 1 ';
        if(!empty($param['name'])){
            $where = $where.' and (group_name like "%'.$param['name'].'%")';
        }
        $param['limit'] = 10;
        $count = Db::name('group')->where($where)->order('id','desc')->count();
        $totalPage = ceil($count/$param['limit']);
        $group = Db::name('group')->where($where)->order('id','desc')->paginate($param['limit']);
        return json(['code'=>0,'msg'=>'','pages'=>$totalPage,'data'=>$group]);
    }

    /**
     * 档案ajax
     */
    public function archives_list(){
        $param = $this->request->get();
        $where = ' 1=1 ';
        if(!empty($param['name'])){
            $where = $where.' and (a.name like "%'.$param['name'].'%" or b.branch_name like "%'.$param['name'].'%")';
        }
        $param['limit'] = 10;
        $count = Db::name('archives')->alias('a')
            ->field(['a.*','b.branch_name','p.name pipe_name'])
            ->join('branch b','b.id = a.enter_id','LEFT')
            ->join('pipes p','p.unit_id = a.pipe_id','LEFT')
            ->where($where)->count();
        $totalPage = ceil($count/$param['limit']);
        $archives = Db::name('archives')->alias('a')
            ->field(['a.*','FROM_UNIXTIME(a.create_t) as create_time','b.branch_name','p.name pipe_name'])
            ->join('branch b','b.id = a.enter_id','LEFT')
            ->join('pipes p','p.unit_id = a.pipe_id','LEFT')
            ->where($where)->order('a.id','desc')->paginate($param['limit']);
        return json(['code'=>0,'msg'=>'','pages'=>$totalPage,'data'=>$archives]);
    }

    /**
     * 公司ajax
     */
    public function branch_list(){
        $param = request()->get();
        $where = ' 1=1 ';
        if(!empty($param['name'])){
            $where = $where.' and (branch_name like "%'.$param['name'].'%")';
        }
        $param['limit'] = 10;
        $count = Db::name('branch')->where($where)->order('id','desc')->count();
        $totalPage = ceil($count/$param['limit']);
        $branch = Db::name('branch')->where($where)->order('id','desc')->paginate($param['limit']);
        return json(['code'=>0,'msg'=>'','pages'=>$totalPage,'data'=>$branch]);
    }

    /**
     * 管道ajax
     */
    public function pipes_list(){
        $param = request()->get();
        $where = ' 1=1 ';
        if(!empty($param['name'])){
            $where = $where.' and (name like "%'.$param['name'].'%")';
        }
        $param['limit'] = 10;
        $count = Db::name('pipes')->where($where)->order('id','desc')->count();
        $totalPage = ceil($count/$param['limit']);
        $pipes = Db::name('pipes')->where($where)->order('id','desc')->paginate($param['limit']);
        return json(['code'=>0,'msg'=>'','pages'=>$totalPage,'data'=>$pipes]);
    }

    /**
     * 区域ajax
     */
    public function region_list(){
        $param = request()->get();
        $where = ' 1=1 ';
        if(!empty($param['name'])){
            $where = $where.' and (r.region_name like "%'.$param['name'].'%")';
        }
        $param['limit'] = 10;
        $count = Db::name('region')->alias('r')
            ->field(['r.*','g.group_name'])
            ->join('group g','g.id = r.group_id','LEFT')
            ->where($where)->order('r.id','desc')->count();
        $totalPage = ceil($count/$param['limit']);
        $region = Db::name('region')->alias('r')
            ->field(['r.*','g.group_name'])
            ->join('group g','g.id = r.group_id','LEFT')
            ->where($where)->order('r.id','desc')->paginate($param['limit']);
        return json(['code'=>0,'msg'=>'','pages'=>$totalPage,'data'=>$region]);
    }
}