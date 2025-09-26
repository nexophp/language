<?php
global $app;

use core\Menu;
use modules\language\lib\Niutrans;

/**
 * 模块信息
 */
$module_info = [
	'version' => '1.0.0',
	'title' => '多语言',
	'description' => '翻译',
	'url' => '',
	'email' => '68103403@qq.com',
	'author' => 'sunkangchina'
];

Menu::setGroup('admin');

/**
 * 添加应用菜单 
 */
Menu::add('language', '多语言', '/language/admin', '', 1000, 'system');

try {
	$langs = db_get("language", "code", ['status' => 1, 'ORDER' => ['sort' => 'DESC', 'id' => 'ASC']]);
	Route::$supported_languages = $langs ?: [
		'zh-cn',
	];
} catch (\Throwable $th) {
}

add_action("AppController.init", function () {
	global $app;
	$app['lang'] = cookie('lang');
});

add_action('admin.setting.form', function () {
	$all = db_get('language', "*", ['status' => 1, 'ORDER' => ['sort' => 'DESC', 'id' => 'ASC']]);
	$list = [
		'auto' => lang('自动，根据浏览器语言')
	];
	foreach ($all as $item) {
		$list[$item['code']] = lang($item['title']);
	}
?>
	<div class="mb-4">
		<h6 class="fw-bold mb-3 border-bottom pb-2">
			<i class="bi bi-flag me-2"></i><?= lang('多语言') ?>
		</h6>

		<div class="row g-3">
			<div class="col-md-6">
				<label class="form-label">
					<a href="https://niutrans.com/" class="link" target="_blank"><?= lang('小牛翻译') ?> API-KEY </a>

				</label>
				<input v-model="form.niutrans_apikey" class="form-control" placeholder="API-KEY">
			</div>

			<div class="col-md-6">
				<label class="form-label">
					<?= lang('多语言设置') ?>
				</label>
				<select v-model="form.default_lang" class="form-control">
					<?php foreach ($list as $key => $item) { ?>
						<option value="<?= $key ?>"><?= $item ?></option>
					<?php } ?>
				</select>
			</div>

		</div>
	</div>

<?php
}, 500);

add_action("lang", function ($data) {
	if (!is_admin() || !is_local()) {
		return;
	}
	$name = $data['name'];
	$value = $data['value'];
	$file_name = $data['file_name'];
	$lang = cookie('lang');
	$lang_file = PATH . '/lang/' . $lang . '/' . $file_name . '.php';
	$dir = get_dir($lang_file);
	if (!is_dir($dir)) {
		mkdir($dir, 0777, true);
	}
	$content = include($lang_file);
	if (!$content || !is_array($content)) {
		$content = [];
	}
	if ($lang && $lang != 'zh-cn' && !$content[$name]) {
		$str = Niutrans::translate($name, 'zh-cn', $lang);
		if ($str) {
			$content[$name] = ucfirst($str);
			file_put_contents($lang_file, '<?php return ' . var_export($content, true) . ';');
		}
	}
});

add_action("header_right", function () {
	if (get_config('default_lang') != 'auto') {
		return;
	}
	$all = db_get('language', "*", ['status' => 1, 'ORDER' => ['sort' => 'DESC', 'id' => 'ASC']]);
	$list = [];
	$lang = cookie('lang');
	foreach ($all as $item) {
		$list[$item['code']] = lang($item['title']);
	}
	add_js("
		$('#lang-selector').change(function(){
			var lang = $(this).val(); 
			ajax('/language/ajax/change',{
				lang:lang
			},function(){
				location.reload();
			}); 
		});
	");
?>
	<select name="lang" id="lang-selector" class="form-select" style="margin-right: 10px;">
		<?php foreach ($list as $key => $item) { ?>
			<option value="<?= $key ?>" <?php if ($key == $lang) { ?>selected<?php } ?>><?= $item ?></option>
		<?php } ?>
	</select>
<?php
});
