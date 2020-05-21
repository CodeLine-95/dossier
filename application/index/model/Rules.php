<?php
namespace app\index\model;
use think\Model;
class Rules extends Model{
    /**
     * [getMenu 根据节点数据获取对应的菜单]
     */
    public function getMenu($status=1,$nodeStr = '',$is_ajax = false){
        if($is_ajax){
            $where = '';
            //状态判断：ALL-》获取全部权限，1菜单权限，2隐藏权限
            $where1 = ($status == 'All') ? $where : $where . ' rule_status = '.$status;
            $result = $this->field(true)->where($where1)->select();
            $menu = $this->prepareMenuAjax($result,$nodeStr);
        }else{
            //超级管理员没有节点数组
            $where = empty($nodeStr) ? '' : 'id in('.$nodeStr.') and';
            //状态判断：ALL-》获取全部权限，1菜单权限，2隐藏权限
            $where1 = ($status == 'All') ? $where : $where . ' rule_status = '.$status;
            $result = $this->field(true)->where($where1)->select();
            $menu = $this->prepareMenu($result); 
        }
        return $menu;
    }

    /**
     * 二叉树菜单数据生成，递归算法
     * @param $param
     * @return array
     */
    public function prepareMenu($param,$pid=0){   
        $menus = $one = array();
        foreach ($param as $k => $v) {
            $one['id'] = $v['id'];
            $one['rule_name'] = $v['rule_name'];
            $one['rule_icon'] = $v['rule_icon'];
            if ($v['rule_url'] !== '#'){
              $one['rule_url'] = url($v['rule_url']);
            }else{
              unset($one['rule_url']);
            }
            if($v['rule_pid']==$pid){
                $one['rule_child'] = $this->prepareMenu($param,$v['id']);
                $menus[]=$one;
            }
        }
        return $menus;
    }

    /**
     * 权限ajax数据生成，当前角色已分配过的权限，进行全选和非全选的数据组合
     * 目前这个tree是不支持直接在初始数据中进行全选和非全选的
     * 遗留了这个功能，但是没有用到
     */
    public function prepareMenuAjax($param,$nodeStr='',$pid=0){   
        $MenuAjax = $MenuAjaxOne = array();
        foreach ($param as $k => $v) {
            $MenuAjaxOne['id'] = $v['id'];
            $MenuAjaxOne['title'] = $v['rule_name'];
            $MenuAjaxOne['field'] = 'group_rules';
            $MenuAjaxOne['spread'] = true;
            $nodeStrArr = explode(',',$nodeStr);
            $MenuAjaxOne['checked'] = false;
            $MenuAjaxOne['disabled'] = false;
            if ($v['rule_url'] !== '#'){
              $MenuAjaxOne['href'] = url($v['rule_url']);
            }else{
              unset($MenuAjaxOne['href']);
            }
            if($v['rule_pid']==$pid){
                // if(!empty($nodeStrArr) && in_array($v['id'],$nodeStrArr)){
                //     $MenuAjaxOne['checked'] = true;
                // }else{
                //     $MenuAjaxOne['checked'] = false;
                // }
                $MenuAjaxOne['children'] = $this->prepareMenuAjax($param,$nodeStr,$v['id']);
                $MenuAjax[]=$MenuAjaxOne;
            }
        }
        return $MenuAjax;
    }

    /**
     * 过滤角色权限顶级菜单权限
     * 在tree这个组件渲染中需要过滤顶级菜单的id编号
     */

     public function nodeParentMenu($param){
        $menus = array();
        foreach ($param as $k => $v) {
            if($v['rule_pid'] != 0){
                $menus[] = $v['id'];
            }else{
                continue;
            }
        }
        return $menus;
     }
}