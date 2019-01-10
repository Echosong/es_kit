/**
 * Created by LJC on 2017/9/12.
 */
layui.config({
    base: "/res/public/js/"
}).use(['form', 'upload', 'laydate', 'element', 'layer'], function () {
    var form = layui.form,
        $ = layui.jquery,
        upload = layui.upload,
        laydate = layui.laydate,
        element = layui.element;

    $('#v-mobile').on('blur', function () {
        $.get($(this).attr('action') + '?mobile=' + $(this).val(), function (result) {
            $('#v-name').show();
            $('#v-name .layui-input-block').text(result.message.name);
        })
    });


    //监听提交
    form.on('submit(btnSubmit)', function (data) {
        //弹出loading
        if ($('#content').val()) {
            data.field['content'] = $('#content').val();
        }
        var h = 0;
        var index = layer.load();
        $.post($('form').attr('action'), data.field, function (result) {
            if (result.code == 0) {
                h = 1;
                layer.close(index);
                layer.msg(result.message);
                setTimeout(function () {
                    if ($('form').hasClass('not-to-parent')) {
                        if ($('form').hasClass('search_btn_menuform')) {
                            $('#search_btn_menuform').click();
                        } else {
                            location.reload();
                        }
                    } else {
                        window.parent.location.reload(); //刷新父页面
                    }
                    var index = top.layer.getFrameIndex(window.name); //获取窗口索引
                    top.layer.close(index);  // 关闭layer
                }, 800);
            } else{
                h = 1;
                layer.close(index);
                layer.msg(result.message);
            }
            if (h == 0) {
                setTimeout(function () {
                    layer.close(index);
                    layer.msg('超时');
                }, 10000);
            }
        }, "json");
        return false;
    });

    function isIntNum(val){
        var regPos = / ^\d+$/; // 非负整数
        var regNeg = /^\-[1-9][0-9]*$/; // 负整数
        if(regPos.test(val) || regNeg.test(val)){
            return true;
        }else{
            return false;
        }
    }
    let setUrl = function (url, $ele) {
        $ele.before(' <div class="block-item-img" >\n' +
            '                    <div class="block-item-alert">\n' +
            '                        <i class="layui-icon">&#xe640;</i>\n' +
            '                    </div>\n' +
            '                    <img style="width: 120px; height: 120px;" src="' + url + '">\n' +
            '                </div>');
        var $input = $('#' + $ele.attr('data-input'));
        let valurl = $input.val() ? ($input.val() + ',' + url) : url;
        $input.val(valurl);
    };

    $(document).on("mouseover mouseout", '.block-item-img', function (event) {
        if (event.type == "mouseover") {
            //鼠标悬浮
            $(this).children('.block-item-alert').css('display', 'flex')
        } else if (event.type == "mouseout") {
            //鼠标离开
            $(this).children('.block-item-alert').css('display', 'none')
        }
    })

    $(document).on('click', '.block-item-img i', function () {
        var $item = $(this).parent().parent();
        var currimgurl = $item.children('img').attr('src');
        var imgUrl = $item.parent().prev('input').val();
        $item.parent().prev('input').val(imgUrl.replace(currimgurl + ',', '').replace(currimgurl, ''));
        $item.remove();
    });
    $('.upload').each(function () {
        var $ele = $(this);
        var imgStr = $(this).attr('imgs');

        if (imgStr) {
            var imgs = imgStr.split(',');
            for (let i = 0; i < imgs.length; i++) {
                setUrl(imgs[i], $ele)
            }
        }

        var operation = {
            elem: '#' + $ele.attr('id')
            , url: $ele.attr('url')
            , size: 2048 //限制文件大小，单位 KB
            , before: function (obj) {
                //预读本地文件示例，不支持ie8
                obj.preview(function (index, file, result) {
                    $('#' + $ele.attr('demo')).attr('src', result); //图片链接（base64）
                });
            }
            , done: function (res) {
                if (res.code == 0) {
                    $('#' + $ele.attr('tag')).val(res.message.paths[0]);
                    //特殊功能集合 begin
                    //上传会员卡封面图自定义code
                    if ($ele.attr('custCode') == 'coverBgp') {
                        $('#cover-bgimg').css('background', 'url(' + res.message.paths[0] + ') 0px 0px no-repeat');
                        $('#cover-bgimg').css('background-size', '100% 100%');
                        $('#background_pic_url').val(res.message.paths[0]);
                    }
                    //上传会员卡LOGO图自定义code
                    if ($ele.attr('custCode') == 'coverLogo') {
                        $('#logo_url').val(res.message.paths[0]);
                        $('#logologo').attr('src', res.message.paths[0]);
                    }
                    //上传多个文件
                    if ($ele.attr('custCode') == 'uploads') {
                        setUrl(res.message.paths[0], $ele);
                    }
                    //特殊功能集合 end

                    if ($ele.attr('itag')) {
                        $('#' + $ele.attr('itag')).empty();
                        $('#' + $ele.attr('itag')).append(
                            $('<image src="' + res.message.paths[0] + '" style="max-width: 80%;"/>')
                        );
                    }

                    layer.msg(res.message.msg);
                } else {
                    layer.msg(res.message);
                }
            }
        };
        //设定文件大小限制
        upload.render(operation);
    });


    $('.date-item').each(function () {
        var $ele = $(this);
        laydate.render(
            {
                elem: '#' + $ele.attr('id'),
                type: 'datetime'
            });
    });

    //通用input监控限制字数

    $('.font-limit-input').each(function () {
        var $ele = $(this);
        var limit = $ele.attr('font-limit');
        pubInputLimit($ele, limit)
    });

    function pubInputLimit(obj, limit) {
        obj.bind("input propertychange", function (event) {
            inputLimit($(this), limit);
        });
    }


    function inputLimit(obj, limit) {
        var text = obj.val(),
            reg = /[\u4e00-\u9fa5]{1}/g,            //中文
            notReg = /\w{1}/g,  //非中文
            resultCn = text.match(reg),
            resultEn = text.match(notReg),
            textlen = 0;

        if (resultCn) {
            textlen += resultCn.length * 2;
            // limit -= resultCn.length;
        }
        if (resultEn) {
            textlen += resultEn.length;
        }

        if (textlen > limit) {
            var lenText = text.substring(0, limit);
            obj.val(lenText);
            layer.msg('字符不能超过' + limit + '个,一个汉字占2个字符');
        }
    }

});

function SubmitForm() {
    document.getElementById('btnSubmit').click();
}