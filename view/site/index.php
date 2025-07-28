<?php
view_header(lang('多语言'));
global $vue;
$url = '/admin/language/list';
$vue->data("height", "");
$vue->data("currentLang", $lang);
$vue->data("langContent", []);
$vue->data("languages", []);
$vue->data("editingKey", ""); // 编辑状态变量
$vue->data("editingValue", ""); // 编辑值变量
$vue->data("isModified", false); // 是否修改标记
$vue->created(["load()"]);
$vue->method("load()", "
this.height = 'calc(100vh - ".get_config('admin_table_height')."px)';
this.loadLangContent(this.currentLang);
"); 
$vue->method("loadLangContent(lang)", "
    this.currentLang = lang;
    ajax('/language/site/get-lang-content?lang=' + lang,{},function(res){
        _this.langContent = res.data;
        _this.isModified = false;  
    });
");

// 开始编辑方法
$vue->method("startEdit(key, value)", "
    this.editingKey = key;
    this.editingValue = value;
");

// 确认编辑方法
$vue->method("confirmEdit()", "
    if (this.editingKey && this.langContent[this.editingKey] !== this.editingValue) {
        this.langContent[this.editingKey] = this.editingValue;
        this.isModified = true; 
    }
    this.editingKey = '';
");

// 取消编辑方法
$vue->method("cancelEdit()", "
    this.editingKey = '';
");

// 保存方法
$vue->method("saveLangContent()", "
    if (!this.isModified) {
        this.\$message.info('" . lang('没有修改内容') . "');
        return;
    }
    
    var data = {
        lang: this.currentLang,
        content: this.langContent
    };
    
    ajax('/language/site/save-lang-content', data, function(res) {
        _this.\$message.success(res.msg || '" . lang('保存成功') . "');
        _this.isModified = false;
    });
");
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card"> 
                <div class="card-body"> 
                    <div class="mt-3"> 
                        <div v-if="Object.keys(langContent).length > 0" class="bg-light p-3 rounded mt-4">
                            <table class="table table-striped table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th width="40%"><?= lang('键') ?></th>
                                        <th><?= lang('值') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(value, key) in langContent">
                                        <td>{{ key }}</td>
                                        <td>
                                            <div v-if="editingKey !== key" @click="startEdit(key, value)" class="editable-cell">
                                                {{ value }}
                                                <small class="text-muted ms-2"><i class="bi bi-pencil"></i> <?= lang('点击编辑') ?></small>
                                            </div>
                                            <div v-else class="input-group">
                                                <input type="text" class="form-control" v-model="editingValue" @keyup.enter="confirmEdit()" ref="editInput" v-focus>
                                                <button class="btn btn-success" @click="confirmEdit()">
                                                    <i class="bi bi-check"></i>
                                                </button>
                                                <button class="btn btn-secondary" @click="cancelEdit()">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <!-- 保存按钮 -->
                            <div class="text-center mt-3">
                                <button class="btn btn-primary" @click="saveLangContent()" :disabled="!isModified">
                                    <?= lang('保存修改') ?>
                                </button>
                            </div>
                        </div>
                        <div v-else class="alert alert-info">
                            <?= lang('没有找到语言文件或语言文件为空') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 

<?php 
view_footer();
?>