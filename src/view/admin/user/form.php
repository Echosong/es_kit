选择表
tb_user

<form class="layui-form" action="<?=Helper::url('user', "form")?>">

    <? if( $user->id and  $user->udpated ) { ?>
        <blockquote class="layui-elem-quote"><strong>创建时间</strong>：<?= $user->created  ?>
            <strong>上次修改时间</strong>： <?= $user->updated  ?> </blockquote>
    <? } ?>

    <input type="hidden" id="id" name="id" value="<?= $user->id ?>">
    <div class="layui-form-item">
        <label class="layui-form-label">账号</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="username" name="username" lay-verify=""
                   value="<?= $user->username ?>"
                   placeholder="请输入username">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">密码</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="password" name="password" lay-verify=""
                   value="<?= $user->password ?>"
                   placeholder="请输入password">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">邮箱</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="email" name="email" lay-verify="email"
                   value="<?= $user->email ?>"
                   placeholder="请输入email">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">登陆次数</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="login_count" name="login_count" lay-verify="number"
                   value="<?= $user->login_count ?>"
                   placeholder="请输入login_count">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">最后登陆时间</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="last_time" name="last_time" lay-verify="date"
                   value="<?= $user->last_time ?>"
                   placeholder="请输入last_time">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">last_ip</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="last_ip" name="last_ip" lay-verify=""
                   value="<?= $user->last_ip ?>"
                   placeholder="请输入last_ip">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">状态</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="state" name="state" lay-verify="number"
                   value="<?= $user->state ?>"
                   placeholder="请输入state">
        </div>
    </div>
    <input type="hidden" id="id" name="id" value="<?= $user->id ?>">
    <div class="layui-form-item">
        <label class="layui-form-label">真实姓名</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="name" name="name" lay-verify=""
                   value="<?= $user->name ?>"
                   placeholder="请输入name">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">手机号码/账号</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="username" name="username" lay-verify=""
                   value="<?= $user->username ?>"
                   placeholder="请输入username">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">密码</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="password" name="password" lay-verify=""
                   value="<?= $user->password ?>"
                   placeholder="请输入password">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">昵称</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="nick" name="nick" lay-verify=""
                   value="<?= $user->nick ?>"
                   placeholder="请输入nick">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">头像路径</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="avatar" name="avatar" lay-verify=""
                   value="<?= $user->avatar ?>"
                   placeholder="请输入avatar">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">上次登录时间</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="last_time" name="last_time" lay-verify="date"
                   value="<?= $user->last_time ?>"
                   placeholder="请输入last_time">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">上次登陆ip</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="last_ip" name="last_ip" lay-verify=""
                   value="<?= $user->last_ip ?>"
                   placeholder="请输入last_ip">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">弃用</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="level" name="level" lay-verify="number"
                   value="<?= $user->level ?>"
                   placeholder="请输入level">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">值为1时是男性，值为2时是女性，值为0时是未知</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="sex" name="sex" lay-verify=""
                   value="<?= $user->sex ?>"
                   placeholder="请输入sex">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">模型枚举</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="state" name="state" lay-verify=""
                   value="<?= $user->state ?>"
                   placeholder="请输入state">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">注册IP</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="reg_ip" name="reg_ip" lay-verify=""
                   value="<?= $user->reg_ip ?>"
                   placeholder="请输入reg_ip">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">交易密码</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="password_sc" name="password_sc" lay-verify=""
                   value="<?= $user->password_sc ?>"
                   placeholder="请输入password_sc">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">用于手机推送 地址</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="mac" name="mac" lay-verify=""
                   value="<?= $user->mac ?>"
                   placeholder="请输入mac">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">company</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="company" name="company" lay-verify=""
                   value="<?= $user->company ?>"
                   placeholder="请输入company">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">由于 会员全部在系统里面，所以不应该属于哪个公司，可以属于多个公司</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="company_id" name="company_id" lay-verify="number"
                   value="<?= $user->company_id ?>"
                   placeholder="请输入company_id">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">关联管理员ID</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="staff_id" name="staff_id" lay-verify="number"
                   value="<?= $user->staff_id ?>"
                   placeholder="请输入staff_id">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">父亲uid</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="father_id" name="father_id" lay-verify="number"
                   value="<?= $user->father_id ?>"
                   placeholder="请输入father_id">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">爷爷uid</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="grand_father_id" name="grand_father_id" lay-verify="number"
                   value="<?= $user->grand_father_id ?>"
                   placeholder="请输入grand_father_id">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">联系地址</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="address" name="address" lay-verify=""
                   value="<?= $user->address ?>"
                   placeholder="请输入address">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">微信号</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="wx" name="wx" lay-verify=""
                   value="<?= $user->wx ?>"
                   placeholder="请输入wx">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">联系电话</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="qq" name="qq" lay-verify=""
                   value="<?= $user->qq ?>"
                   placeholder="请输入qq">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">商家对用户的标注</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="remark" name="remark" lay-verify=""
                   value="<?= $user->remark ?>"
                   placeholder="请输入remark">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">开卡填的附信息</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="info" name="info" lay-verify=""
                   value="<?= $user->info ?>"
                   placeholder="请输入info">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">身份证</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="id_card" name="id_card" lay-verify=""
                   value="<?= $user->id_card ?>"
                   placeholder="请输入id_card">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">经纬度集合</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="location" name="location" lay-verify=""
                   value="<?= $user->location ?>"
                   placeholder="请输入location">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">是否有会员卡</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="iscard" name="iscard" lay-verify="number"
                   value="<?= $user->iscard ?>"
                   placeholder="请输入iscard">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">生日</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="birthday" name="birthday" lay-verify=""
                   value="<?= $user->birthday ?>"
                   placeholder="请输入birthday">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">openid</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="openid" name="openid" lay-verify=""
                   value="<?= $user->openid ?>"
                   placeholder="请输入openid">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">上次关注时间</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input" id="subscribe_time" name="subscribe_time" lay-verify="date"
                   value="<?= $user->subscribe_time ?>"
                   placeholder="请输入subscribe_time">
        </div>
    </div>
    <div class="layui-form-item layui-hide">
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit="" id="btnSubmit" lay-filter="btnSubmit">立即提交</button>
        </div>
    </div>
</form>

<script src="<?= APP_ROOT . $GLOBALS['static'] ?>/public/js/form.js" charset="utf-8"></script>