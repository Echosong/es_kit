/**
 * Created by LJC on 2017/9/6.
 */

layui.define(['layer', 'form', 'jquery'], function (exports) {
    var layer = layui.layer,
        form = layui.form,
        $ = layui.jquery;

    function authorizeAction(area) {
        var authorizeAction = top.actions[top.currentmenuid];
        var $element = $(area);
        $element.find("a[authorize=yes]").attr('authorize', 'no');
        if (authorizeAction != undefined) {
            $.each(authorizeAction, function (i, item) {
                $element.find("a[action='" + item + "']").attr('authorize', 'yes');
            })
        }
        $element.find('a[authorize=no]').remove();
    }

    function getCookieAction(actions) {
        return top.actions;
    }

    function setCookieAction(actions) {
        $.cookie('actions', actions);
    }

    exports('common', function () {
        return {
            checkAction: authorizeAction,
            setAction: setCookieAction,
            getAction: getCookieAction
        }
    });
})
;