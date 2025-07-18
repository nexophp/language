<?php
 

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
