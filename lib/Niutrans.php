<?php

/**
 * 小牛翻译
 * 购买，有免费额度
 * https://niutrans.com/documents/contents/trans_text#accessMode
 * @author sunkangchina <68103403@qq.com>
 * @license MIT <https://mit-license.org/>
 * @date 2025
 */

namespace modules\language\lib;


class Niutrans
{
    /**
     * 翻译
     * 支持的语言列表  https://niutrans.com/documents/contents/transapi_text_v2#languageList
     * @param string $SourceText 要翻译的文本
     * @param string $Source 源语言
     * @param string $Target 目标语言
     */
    public static function translate($SourceText, $Source = 'zh', $Target = 'en')
    {
        if (strpos($Source, 'zh') !== false) {
            $Source = 'zh';
        }
        if(!get_config('niutrans_apikey')){
            return;
        }
        $cache_key = "niutrans_translate:".md5($SourceText.$Source.$Target);
        $data = cache($cache_key);
        if ($data) { 
            return $data;
        }
        $url = "https://api.niutrans.com/NiuTransServer/translation";
        $data = [
            'src_text' => $SourceText,
            'from' => $Source,
            'to' => $Target,
            'apikey' => get_config('niutrans_apikey'),
        ];
        $url = $url . '?' . http_build_query($data);
        $client = new \GuzzleHttp\Client();
        $res    = $client->request('GET', $url);
        $res = (string)$res->getBody();
        $res = json_decode($res, true);
        $tgt_text = $res['tgt_text'] ?? '';
        if ($tgt_text) {
            cache($cache_key, $tgt_text);
            return $tgt_text;
        }
    }
}
