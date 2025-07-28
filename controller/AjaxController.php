<?php
/**
 * 语言切换
 * @author sunkangchina <68103403@qq.com>
 * @license MIT <https://mit-license.org/>
 * @date 2025
 */

namespace modules\language\controller;

class AjaxController extends \core\AppController
{
    /**
     * 切换语言
     */
    public function actionChange() {
        $lang = g('lang');
        cookie('lang',$lang);
        return json_success([]);
    }

   
}
