<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>
        ES 助手
    </title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="<?= APP_ROOT . $GLOBALS['static'] ?>/public/layui/css/layui.css">
    <link rel="stylesheet" href="<?= APP_ROOT . $GLOBALS['static'] ?>/public/css/admin.css">
    <link rel="stylesheet" href=" https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.13.1/styles/vs.min.css">


</head>
<body style="margin-left: 50px">
<script src="<?= APP_ROOT . $GLOBALS['static'] ?>/public/layui/layui.js" charset="utf-8"></script>
<link href="https://cdn.bootcss.com/highlight.js/8.3/styles/docco.min.css" rel="stylesheet">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.13.1/highlight.min.js"></script>
<form class="layui-form" action="">
    <div class="layui-form-item">
        <div class="layui-form-item">
            <label class="layui-form-label">选择表</label>
            <div class="layui-input-inline">
                <select name="table" id="table" class="layui-input-inline">
                    <?php foreach ($tables as $table) { ?>
                        <option value="<?= $table["Tables_in_{$_db_name}"] ?>"><?= $table["Tables_in_{$_db_name}"] ?></option>
                    <?php } ?>
                </select>
            </div>
            <button class="layui-btn" name="3" lay-submit lay-filter="btnSubmit">生成模型类</button>
            <button class="layui-btn" name="2" lay-submit lay-filter="btnSubmit">生成控制器</button>
            <button class="layui-btn" name="0" lay-submit lay-filter="btnSubmit">生成列表</button>
            <button class="layui-btn" name="1" lay-submit lay-filter="btnSubmit">生成表单</button>
        </div>
        <pre class="layui-code">
        //代码区域
        var a = 'hello layui';
        </pre>
        <pre><code style="width: 1200px;height: 800px" id="layui-code-info" class="layui-form-text " > 显示代码区域 </code></pre>
</form>
<script>
    //Demo
    layui.use(['form', 'code'], function () {
        var form = layui.form, $ = layui.jquery;
        layui.code(); //引用code方法
        form.on('submit(btnSubmit)', function (data) {
            var type = data.elem.name;
            $.post('<?=Helper::url('kit', 'tpl')?>', {type: type, table: data.field.table}, function (msg) {
                $("#textareaCode textarea").text(msg.message);
                $("#layui-code-info").text(msg.message).each(function (i, block) {
                    hljs.highlightBlock(block);
                });
            }, 'json');
            return false;
        });
    });
</script>
</body>

</html>

