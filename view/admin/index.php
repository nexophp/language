<?php
view_header(lang('语言管理'));
global $vue;

$vue->data("list", []);
$vue->data("form", [
    'title' => '',
    'code' => '',
    'status' => 1,
    'sort' => 0
]);
$vue->data("dialogVisible", false);
$vue->data("dialogTitle", lang('添加语言'));
$vue->data("isEdit", false);

$vue->created(["load()"]);

$vue->method("load()", "
    ajax('/language/admin/list', {}, function(res) {
        _this.list = res.data;
    });
");

$vue->method("showAdd()", "
    this.dialogTitle = '" . lang('添加语言') . "';
    this.form = {
        title: '',
        code: '',
        status: 1,
        sort: 0
    };
    this.isEdit = false;
    this.dialogVisible = true;
");

$vue->method("showEdit(row)", "
    this.dialogTitle = '" . lang('编辑语言') . "';
    ajax('/language/admin/detail', {id: row.id}, function(res) {
        _this.form = res.data;
        _this.isEdit = true;
        _this.dialogVisible = true;
    });
");

$vue->method("handleSubmit()", "
    if (!this.form.title) {
        this.\$message.error('" . lang('请输入语言名称') . "');
        return;
    }
    if (!this.form.code) {
        this.\$message.error('" . lang('请输入语言代码') . "');
        return;
    }
    
    var url = this.isEdit ? '/language/admin/update' : '/language/admin/add';
    ajax(url, this.form, function(res) {
        _this.\$message.success(res.msg);
        _this.dialogVisible = false;
        _this.load();
    });
");

$vue->method("handleDelete(row)", "
    this.\$confirm('" . lang('确认删除该语言？') . "', '" . lang('提示') . "', {
        confirmButtonText: '" . lang('确定') . "',
        cancelButtonText: '" . lang('取消') . "',
        type: 'warning'
    }).then(() => {
        ajax('/language/admin/delete', {id: row.id}, function(res) {
            _this.\$message.success(res.msg);
            _this.load();
        });
    }).catch(() => {});
");

$vue->method("formatStatus(row)", "
    return row.status == 1 ? '" . lang('启用') . "' : '" . lang('禁用') . "';
");

$vue->method("view(row)", " 
    let url = '/language/site/index?lang=' + row.code; 
    layer.open({
        title: row.title,
        type: 2,
        area: ['800px', '600px'],
        content: url
    });
");


?>

<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><?= lang('语言列表') ?></h5>
            <el-button type="primary" @click="showAdd()">
                <i class="bi bi-plus"></i> <?= lang('添加语言') ?>
            </el-button>
        </div>
        <div class="card-body">
            <el-table :data="list" border stripe> 
                <el-table-column prop="title" label="<?= lang('语言名称') ?>"></el-table-column>
                <el-table-column prop="code" label="<?= lang('语言代码') ?>"></el-table-column>
                <el-table-column label="<?= lang('状态') ?>" width="130">
                    <template slot-scope="scope">
                        <el-tag :type="scope.row.status == 1 ? 'success' : 'info'">
                            {{ formatStatus(scope.row) }}
                        </el-tag>
                    </template>
                </el-table-column> 
                <el-table-column label="<?= lang('操作') ?>" width="330">
                    <template slot-scope="scope">
                        <el-button size="mini" type="" @click="view(scope.row)">
                            <?= lang('查看翻译') ?>
                        </el-button>
                        <el-button size="mini" type="primary" @click="showEdit(scope.row)">
                            <?= lang('编辑') ?>
                        </el-button>
                        <el-button size="mini" type="danger" @click="handleDelete(scope.row)" :disabled="scope.row.code == 'zh-cn'">
                            <?= lang('删除') ?>
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>
    </div>

    <el-dialog :title="dialogTitle" :visible.sync="dialogVisible" width="500px">
        <el-form label-width="100px">
            <el-form-item label="<?= lang('语言名称') ?>">
                <el-input v-model="form.title" placeholder="<?= lang('请输入语言名称') ?>"></el-input>
            </el-form-item>
            <el-form-item label="<?= lang('语言代码') ?>">
                <el-input v-model="form.code" placeholder="<?= lang('请输入语言代码，如：zh-cn, en') ?>"></el-input>
            </el-form-item>
            <el-form-item label="<?= lang('状态') ?>">
                <el-radio-group v-model="form.status">
                    <el-radio :label="1"><?= lang('启用') ?></el-radio>
                    <el-radio :label="0"><?= lang('禁用') ?></el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="<?= lang('排序') ?>">
                <el-input-number v-model="form.sort" :min="0"></el-input-number>
            </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
            <el-button @click="dialogVisible = false"><?= lang('取消') ?></el-button>
            <el-button type="primary" @click="handleSubmit()"><?= lang('确定') ?></el-button>
        </div>
    </el-dialog>
</div>
<?php
view_footer();
?>