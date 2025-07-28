<?php

/**
 * 多语言
 * @author sunkangchina <68103403@qq.com>
 * @license MIT <https://mit-license.org/>
 * @date 2025
 */

namespace modules\language\controller;

class SiteController extends \core\AdminController
{
    /**
     * 语言列表页面
     * @permission 多语言.管理 
     */
    public function actionIndex() {
        $this->view_data['lang'] = g("lang");
    }

    /**
     * 获取语言文件内容
     * @permission 多语言.管理
     */
    public function actionGetLangContent()
    {
        $lang = $_GET['lang'] ?? 'zh-cn';
        $file = PATH . "/lang/{$lang}/app.php";

        if (file_exists($file)) {
            $content = include($file);
            json_success(['data' => $content]);
        }

        json_error([]);
    }
    
    /**
     * 保存语言文件内容
     * @permission 多语言.管理
     */
    public function actionSaveLangContent()
    {
        $lang = $this->post_data['lang'] ?? 'zh-cn';
        $content = $this->post_data['content'] ?? [];
        
        if (empty($content)) {
            json_error(['msg' => lang('内容不能为空')]);
        }
        
        $langDir = PATH . "/lang/{$lang}";
        $file = "{$langDir}/app.php";
        
        // 确保语言目录存在
        if (!is_dir($langDir)) {
            mkdir($langDir, 0755, true);
        }
        
        // 生成PHP数组格式的内容
        $phpContent = "<?php\nreturn " . var_export($content, true) . ";\n";
        
        // 写入文件
        if (file_put_contents($file, $phpContent)) {
            json_success(['msg' => lang('保存成功')]);
        } else {
            json_error(['msg' => lang('保存失败，请检查文件权限')]);
        }
    }
}
