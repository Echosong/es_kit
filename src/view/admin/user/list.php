选择表
tb_user

<form class="layui-form layui-form-pane tb_list">
    <div class="layui-form-item">
        <label class="layui-form-label">关键字</label>
        <div class="layui-input-inline">
            <input type="text" id="keyword" name="keyword" value="<?= Helper::request('keyword', '') ?>"
                   placeholder="请输入关键字"
                   class="layui-input layui-input-small search_input">
        </div>
        <button class="layui-btn" lay-submit=""><i class="layui-icon">&#xe615;</i></button>
        <a class="layui-btn addbtn" yxbd_method="form" yxbd_param="height:800px;width:600px;title:添加权限"
           authorize="yes" action="<?= Helper::url('user', 'form') ?>">添加权限</a>
    </div>
</form>
<div class="layui-form tb_list">
    <table class="layui-table">
        <thead>
        <tr>
            <th>id</th>
            <th>账号</th>
            <th>密码</th>
            <th>邮箱</th>
            <th>登陆次数</th>
            <th>最后登陆时间</th>
            <th>last_ip</th>
            <th>状态</th>
            <th>创建时间</th>
            <th>修改时间</th>
            <th>主键</th>
            <th>真实姓名</th>
            <th>手机号码/账号</th>
            <th>密码</th>
            <th>昵称</th>
            <th>头像路径</th>
            <th>上次登录时间</th>
            <th>上次登陆ip</th>
            <th>弃用</th>
            <th>插入时间</th>
            <th>修改时间</th>
            <th>值为1时是男性，值为2时是女性，值为0时是未知</th>
            <th>模型枚举</th>
            <th>注册IP</th>
            <th>交易密码</th>
            <th>用于手机推送 地址</th>
            <th>company</th>
            <th>由于 会员全部在系统里面，所以不应该属于哪个公司，可以属于多个公司</th>
            <th>关联管理员ID</th>
            <th>父亲uid</th>
            <th>爷爷uid</th>
            <th>联系地址</th>
            <th>微信号</th>
            <th>联系电话</th>
            <th>商家对用户的标注</th>
            <th>开卡填的附信息</th>
            <th>身份证</th>
            <th>经纬度集合</th>
            <th>是否有会员卡</th>
            <th>生日</th>
            <th>openid</th>
            <th>上次关注时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?PHP foreach ($users as $k => $user) { ?>
            <tr data-id="<?= $user['id'] ?>">
                <td align="left"><?= $user['id'] ?></td>
                <td align="left"><?= $user['username'] ?></td>
                <td align="left"><?= $user['password'] ?></td>
                <td align="left"><?= $user['email'] ?></td>
                <td align="left"><?= $user['login_count'] ?></td>
                <td align="left"><?= $user['last_time'] ?></td>
                <td align="left"><?= $user['last_ip'] ?></td>
                <td align="left"><?= $user['state'] ?></td>
                <td align="left"><?= $user['created'] ?></td>
                <td align="left"><?= $user['updated'] ?></td>
                <td align="left"><?= $user['id'] ?></td>
                <td align="left"><?= $user['name'] ?></td>
                <td align="left"><?= $user['username'] ?></td>
                <td align="left"><?= $user['password'] ?></td>
                <td align="left"><?= $user['nick'] ?></td>
                <td align="left"><?= $user['avatar'] ?></td>
                <td align="left"><?= $user['last_time'] ?></td>
                <td align="left"><?= $user['last_ip'] ?></td>
                <td align="left"><?= $user['level'] ?></td>
                <td align="left"><?= $user['created'] ?></td>
                <td align="left"><?= $user['updated'] ?></td>
                <td align="left"><?= $user['sex'] ?></td>
                <td align="left"><?= $user['state'] ?></td>
                <td align="left"><?= $user['reg_ip'] ?></td>
                <td align="left"><?= $user['password_sc'] ?></td>
                <td align="left"><?= $user['mac'] ?></td>
                <td align="left"><?= $user['company'] ?></td>
                <td align="left"><?= $user['company_id'] ?></td>
                <td align="left"><?= $user['staff_id'] ?></td>
                <td align="left"><?= $user['father_id'] ?></td>
                <td align="left"><?= $user['grand_father_id'] ?></td>
                <td align="left"><?= $user['address'] ?></td>
                <td align="left"><?= $user['wx'] ?></td>
                <td align="left"><?= $user['qq'] ?></td>
                <td align="left"><?= $user['remark'] ?></td>
                <td align="left"><?= $user['info'] ?></td>
                <td align="left"><?= $user['id_card'] ?></td>
                <td align="left"><?= $user['location'] ?></td>
                <td align="left"><?= $user['iscard'] ?></td>
                <td align="left"><?= $user['birthday'] ?></td>
                <td align="left"><?= $user['openid'] ?></td>
                <td align="left"><?= $user['subscribe_time'] ?></td>
                <td>
                    <a class="item_edit" yxbd_method="form" yxbd_param="height:800px;width:600px;title:修改权限"
                       authorize="yes" action="<?= Helper::url('user', 'form') ?>">
                        <i class="layui-icon">&#xe642;</i>
                    </a>
                    <a class="item_del" yxbd_method="confirm" authorize="yes"
                       action="<?= Helper::url('user', 'delete') ?>">
                        <i class="layui-icon">&#xe640;</i>
                    </a>
                </td>
            </tr>
        <?PHP } ?>
        </tbody>
    </table>
</div>
<div id="page">
    <?= $page ?>
</div>
<script src="<?= APP_ROOT . $GLOBALS['static'] ?>/public/js/list.js" charset="utf-8"></script>