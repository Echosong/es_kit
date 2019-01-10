<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="Keywords" content="">
    <meta name="Description" content="">
    <title> <?= $_config['admin']['sitename']?></title>
    <link rel="stylesheet" href="<?= APP_ROOT . $GLOBALS['static'] ?>/public/layui/css/layui.css">
    <link rel="stylesheet" href="<?= APP_ROOT . $GLOBALS['static']  ?>/public/css/admin.css">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<body>
<!-- 布局容器 -->
<div class="layui-layout layui-layout-admin">
    <!-- 头部 -->
    <div class="layui-header">
        <div class="layui-main">
            <!-- logo -->
            <a href="#" class="logo">ES php极简框架</a>
            <ul class="layui-nav" style="position: absolute; top: 0; left: 140px; background: none;">
                <li class="layui-nav-item t_slide slide">
                    <a style="width:35px;text-align: center;"><i class="layui-icon">&#xe671;</i></a>
                </li>
            </ul>
            <!-- 水平导航 -->
            <ul class="layui-nav" style="position: absolute; top: 0; right: 0; background: none;">
                <li class="layui-nav-item">
                    <a href="javascript:;"> <?= $_admin['name'] ?>,欢迎你！<span class="layui-nav-more"></span></a>
                    <dl class="layui-nav-child layui-anim layui-anim-upbit"> <!-- 二级菜单 -->
                        <dd><a href="https://coding.net/s/a7e45ced-8eef-423c-8478-3a51332f39f1">系统帮助</a></dd>
                        <dd><a id="password" data-url="<?=Helper::url('user', 'password')?>" data-id="'-1">修改密码</a></dd>
                        <dd><a href="<?= Helper::url('main', 'Logout') ?>">退出</a></dd>
                    </dl>
                </li>
            </ul>
        </div>
    </div>

    <!--侧边栏 -->
    <div class="layui-side layui-bg-black m-side">
        <div class="layui-side-scroll">
            <ul class="layui-nav layui-nav-tree " lay-filter="left-nav" style="border-radius: 0;">
            </ul>
        </div>
    </div>

    <!-- 主体 -->
    <div class="layui-body m_body">
        <!-- 顶部切换卡 -->
        <div class="layui-tab layui-tab-brief" lay-filter="top-tab" lay-allowClose="true" style="margin: 0;">
            <ul class="layui-tab-title"></ul>
            <div class="layui-tab-content site-body"></div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?= APP_ROOT . $GLOBALS['static']  ?>/public/layui/layui.js"></script>
<script type="text/javascript">
    var actions = null;
    var currentmenuid = null;
    layui.config({
        base: "/res/public/js/"
    });

    /**
     * 初始化整个系统骨架
     */
    layui.use(['cms'], function () {
        var cms = layui.cms('left-nav', 'top-tab'),
            $ = layui.jquery;

        $('#password').click(function () {
           var url = $(this).attr('data-url');
           cms.addTab('修改密码', url, 1000);
        });
        var title = '<i class="layui-icon" id="aaaaaa" >&#xe68e;</i>'
        cms.addTab(title, '<?=Helper::url('main', "index")?>', 1000);

         var menu=[
            {id: "11", pid: "10", node: "后台首页", url: "<?=Helper::url('main', "index")?>"},
            {id: "10", pid: "0", node: '<i class="layui-icon">&#xe620;</i>&nbsp; DEMO管理', url: "#"},
            {id: "42", pid: "10", node: "用户管理", url: "<?=Helper::url('user', "list")?>"},
        ];
        cms.addNav(menu, 0, 'id', 'pid', 'node', 'url');
        cms.bind(60 + 41 + 20 + 10); //头部高度 + 顶部切换卡标题高度 + 顶部切换卡内容padding + 底部高度
        cms.clickLI(0);
    });
</script>
</body>
<style>
    #aaaaaa{
        display:inline-block !important;
        font-size: 15px; color: #000 !important;"
    }

</style>
</html>