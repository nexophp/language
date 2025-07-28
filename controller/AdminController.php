<?php

/**
 * 语言管理
 * @author sunkangchina <68103403@qq.com>
 * @license MIT <https://mit-license.org/>
 * @date 2025
 */

namespace modules\language\controller;

class AdminController extends \core\AdminController
{
    /**
     * 语言列表页面
     * @permission 多语言.管理
     */
    public function actionIndex() { 
        if(!db_get_one('language','*',['code'=>'zh-cn'])){
            db_insert('language',[
                'title'=>'简体中文',
                'code'=>'zh-cn',
                'status'=>1,
                'created_at'=>time(),
                'updated_at'=>time(),
            ]);
        }
        if(!db_get_one('language','*',['code'=>'en'])){
            db_insert('language',[
                'title'=>'English',
                'code'=>'en',
                'status'=>1,
                'created_at'=>time(),
                'updated_at'=>time(),
            ]);
        }
    }

    /**
     * 获取语言列表
     * @permission 多语言.管理
     */
    public function actionList()
    {
        $list = db_get_all('language', '*', ['ORDER'=>['sort'=>'DESC']]);
        json_success(['data' => $list]);
    }

    /**
     * 添加语言
     * @permission 多语言.管理
     */
    public function actionAdd()
    {
        $data = $this->post_data;
        $data['created_at'] = time();
        $data['updated_at'] = time();
        
        // 检查语言代码是否已存在
        $exists = db_get_one('language', 'id', ['code' => $data['code']]);
        if ($exists) {
            json_error(['msg' => '语言代码已存在']);
        }
        
        $id = db_insert('language', $data);
        if ($id) {
            json_success(['msg' => '添加成功']);
        }
        json_error(['msg' => '添加失败']);
    }

    /**
     * 更新语言
     * @permission 多语言.管理
     */
    public function actionUpdate()
    {
        $data = $this->post_data;
        $id = $data['id'];
        unset($data['id']);
        $data['updated_at'] = time();
        
        // 检查语言代码是否已存在（排除当前记录）
        $exists = db_get_one('language', 'id', ['code' => $data['code'], 'id[!]' => $id]);
        if ($exists) {
            json_error(['msg' => '语言代码已存在']);
        }
        
        $res = db_update('language', $data, ['id' => $id]);
        if ($res) {
            json_success(['msg' => '更新成功']);
        }
        json_error(['msg' => '更新失败']);
    }

    /**
     * 删除语言
     * @permission 多语言.管理
     */
    public function actionDelete()
    {
        $id = $this->post_data['id'];
        $res = db_delete('language', ['id' => $id]);
        if ($res) {
            json_success(['msg' => '删除成功']);
        }
        json_error(['msg' => '删除失败']);
    }

    /**
     * 获取语言详情
     * @permission 多语言.管理
     */
    public function actionDetail()
    {
        $id = $this->post_data['id'];
        $data = db_get_one('language', '*', ['id' => $id]);
        if ($data) {
            json_success(['data' => $data]);
        }
        json_error(['msg' => '语言不存在']);
    }
}