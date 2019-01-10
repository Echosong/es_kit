<form class="layui-form layui-form-pane tb_list">
    <div class="layui-form-item">
        <label class="layui-form-label">关键字</label>
        <div class="layui-input-inline">
            <input type="text" id="keyword" name="keyword" value="{{?= Helper::request('keyword', '') ?}}"
                   placeholder="请输入关键字"
                   class="layui-input layui-input-small search_input">
        </div>
        <button class="layui-btn" lay-submit=""><i class="layui-icon">&#xe615;</i></button>
        <a class="layui-btn addbtn" yxbd_method="form" yxbd_param="height:800px;width:600px;title:添加权限"
           authorize="yes" action="{{?= Helper::url('<?=strtolower($table)?>', 'form') ?}}">添加权限</a>
    </div>
</form>
<div class="layui-form tb_list">
    <table class="layui-table">
        <thead>
        <tr>
            <?php foreach ($columns as $index => $column) { ?>
                <th><?= $column['comment']?$column['comment']: $column['Field'] ?></th>
            <?php } ?>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        {{?PHP foreach ($<?= $table ?>s as $k => $<?= $table ?>) { ?}}
        <tr data-id="{{?= $<?= $table ?>['id'] ?}}">
            <?php foreach ($columns as $index => $column) { ?>
                <td align="left">{{?= $<?= $table ?>['<?= $column['Field'] ?>'] ?}}</td>
            <?php } ?>
            <td>
                <a class="item_edit" yxbd_method="form" yxbd_param="height:800px;width:600px;title:修改权限"
                   authorize="yes" action="{{?= Helper::url('<?=strtolower($table)?>', 'form') ?}}">
                    <i class="layui-icon">&#xe642;</i>
                </a>
                <a class="item_del" yxbd_method="confirm" authorize="yes"
                   action="{{?= Helper::url('<?=strtolower($table)?>', 'delete') ?}}">
                    <i class="layui-icon">&#xe640;</i>
                </a>
            </td>
        </tr>
        {{?PHP } ?}}
        </tbody>
    </table>
</div>
<div id="page">
    {{?= $page ?}}
</div>
<script src="{{?= APP_ROOT . $GLOBALS['static'] ?}}/public/js/list.js" charset="utf-8"></script>