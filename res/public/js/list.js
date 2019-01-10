layui.config({
    base: "/res/public/js/"
}).use(['form', 'layer', 'common', 'jquery', 'laydate'], function () {
    var form = layui.form,
        layer = layui.layer,
        common = layui.common,
        laydate = layui.laydate,
        $ = layui.jquery;
    common.checkAction($('.tb_list'));

    if($('.layui-btn').hasClass('list-to-tab') || $('.layui-card-body').hasClass('list-to-tab')){
        var cms = window.parent.cms;
        $('.list-to-tab').click(function () {
            var id = $(this).attr('tab-id');
            var url = $(this).attr('data-url');
            var title = $(this).attr('data-title');
            cms.addTab(title, url, id);
        });
    }


    $.modalOpen = function (options) {
        var defaults = {
            id: null,
            title: '系统窗口',
            width: "800px",
            height: "600px",
            url: '',
            shade: 0.3,
            btn: ['确认', '关闭'],
            btnclass: ['btn btn-primary', 'btn btn-danger'],
            callBack: null
        };
        var options = $.extend(defaults, options);
        var _width = $(window).width() > parseInt(options.width.replace('px', '')) ? options.width : $(window).width() + 'px';
        var _height = $(window).height() > parseInt(options.height.replace('px', '')) ? options.height : $(window).height() + 'px';

        var index = layui.layer.open({
            title: options.title,
            type: 2,
            content: options.url,
            area: [_width, _height],
            btn: options.btn,
            btnclass: options.btnclass,
            yes: function (index, layero) {
                var iframeWin = window[layero.find('iframe')[0]['name']];
                //调用授权提交方法
                var flag = iframeWin.SubmitForm();
            }, cancel: function () {
                return true;
            }
        })
    }

    $.deleteForm = function (options) {
        var defaults = {
            prompt: "注：您确定要处理该项数据吗？",
            url: "",
            param: [],
            success: null,
            close: true
        };
        var options = $.extend(defaults, options);
        if ($('[name=__RequestVerificationToken]').length > 0) {
            options.param["__RequestVerificationToken"] = $('[name=__RequestVerificationToken]').val();
        }
        layer.confirm(options.prompt, {icon: 3, title: '提示信息'}, function (index) {
            $.post(options.url, options.param, function (result) {
                if (result.code == 0) {
                    layer.msg(result.message);
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                } else {
                    layer.msg(result.message);
                }

            }, "json");
        });
    }

    $.modelfull = function (options) {
        var defaults = {
            title: '系统窗口',
            url: '',
            type: 2,
            shade: 0.3,
            btn: null,
            callBack: null
        };
        var options = $.extend(defaults, options);
        var index = layui.layer.open({
            title: options.title,
            type: 2,
            content: options.url,
            btn: options.btn,
        })
        layui.layer.full(index);
    }

    $('a[authorize="yes"]').each(function () {

        var $ele = $(this);

        if ($ele.attr('yxbd_method') == 'form') {
            $ele.on('click', function () {
                var id = $ele.parents('tr').attr('data-id') || $ele.attr('data-id') || 0;
                var sid = $ele.parents('tr').attr('data-sid') || $ele.attr('data-sid') || 0;
                var exfield = $ele.attr('yxbd_field') || '';

                var param = [];
                if(id) param.push(['id=' + id]);
                if(sid) param.push(['sid=' + sid]);
                if (exfield != '') param.push($ele.attr('yxbd_field'));
                var operation = {
                    id: "Form",
                    title: $ele.attr('title') || '系统窗口',
                    url: $ele.attr('action') + "?" + param.join("&")
                };
                var param = {};
                ($ele.attr('yxbd_param') || '').replace(/([^:]+?):([^;]+);?/g, function ($0, $1, $2) {
                    operation[$1] = $2;
                });
                $.modalOpen(operation);
            })

        }
        else if ($ele.attr('yxbd_method') == 'detail') {
            $ele.on('click', function () {
                var id = $ele.parents('tr').attr('data-id') || $ele.attr('data-id');
                var exfield = $ele.attr('yxbd_field') || '';
                var param = [];
                if(id) param.push(['id=' + id]);
                if (exfield != '') param.push($ele.attr('yxbd_field'));
                var operation = {
                    id: "Form",
                    title: $ele.attr('title') || '系统窗口',
                    url: $ele.attr('action') + "?" + param.join("&"),
                    btn: null
                };
                ($ele.attr('yxbd_param') || '').replace(/([^:]+?):([^;]+);?/g, function ($0, $1, $2) {
                    operation[$1] = $2;
                });
                if (operation['full'] == 'true') {
                    $.modelfull(operation);
                } else {
                    $.modalOpen(operation);
                }
            })
        }
        else if ($ele.attr('yxbd_method') == 'confirm') {
            $ele.on('click', function () {
                var operation = {
                    url: $ele.attr('action'),
                    param: {id: $ele.parents('tr').attr('data-id')},
                    success: function () {
                        location.reload();
                    }
                };
                ($ele.attr('yxbd_param') || '').replace(/([^:]+?):([^;]+);?/g, function ($0, $1, $2) {
                    operation[$1] = $2;
                });
                $.deleteForm(operation);
            })
        }else if($ele.attr('yxbd_method') == 'post'){
            //供货批量导入使用
            $ele.on('click', function () {
                var exfield = $ele.attr('yxbd_field') || '';
                var operation = {
                    url: $ele.attr('action'),
                    param: exfield,
                    success: function () {
                        location.reload();
                    }
                };
                ($ele.attr('yxbd_param') || '').replace(/([^:]+?):([^;]+);?/g, function ($0, $1, $2) {
                    operation[$1] = $2;
                });

                //例子1
                layer.prompt(function(value, index, elem){

                    layer.close(index);
                });
                $.deleteForm(operation);
            })
        }
    });

    $('a[showlink="yes"]').each(function () {
        var $ele = $(this);
        $ele.on('click', function () {
            var id = $ele.parents('tr').attr('data-id') || $ele.attr('data-id') || 0;
            var exfield = $ele.attr('yxbd_field') || '';
            var param = [];
            if (exfield != '') param.push($ele.attr('yxbd_field'));
            var operation = {
                id: "Form",
                title: $ele.attr('title') || '系统窗口',
                url: $ele.attr('action') + "?" + param.join("&"),
                btn: null
            };
            ($ele.attr('yxbd_param') || '').replace(/([^:]+?):([^;]+);?/g, function ($0, $1, $2) {
                operation[$1] = $2;
            });
            $.modalOpen(operation);
        })
    });

    $('.date-item').each(function () {
        var $ele = $(this);
        laydate.render(
            {
                elem: '#' + $ele.attr('id'),
                type: 'datetime'
            });
    });
});