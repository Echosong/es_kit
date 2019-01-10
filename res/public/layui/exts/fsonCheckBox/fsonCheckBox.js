/**
 * Created by 卢文志 on 2018/11/15 0015.
 *-------------------------------------------------------
 * @git https://gitee.com/okcoder
 * @author OkCoder
 * @QQ 1046512080
 */
layui.define(['element', 'form', 'layer', 'laytpl'], function (exports) {
    "use strict";
    var $ = layui.$
        , element = layui.element
        , form = layui.form
        , laytpl = layui.laytpl
        , layer = layui.layer
        , MOD_NAME = 'fsonCheckBox'
        , fsonCheckBox = {
            index: layui.fsonCheckBox ? (layui.fsonCheckBox.index + 10000) : 0
            //设置全局项
            , set: function (options) {
                var that = this;
                that.config = $.extend({}, that.config, options);
                return that;
            }

            //事件监听
            , on: function (events, callback) {
                return layui.onevent.call(this, MOD_NAME, events, callback);
            }
        }
        , pullData = function (options) {
            var that = this;
            $.ajax({
                method: options.method || 'get'
                , url: options.url
                , dataType: options.dataType || 'json'
                , success: function (res) {
                    if (res.code === 0) {
                        renderData.call(that, res.data, options)
                    } else {
                        layer.msg(res.msg)
                    }
                }
            })
        }
        , renderData = function (data, options) {
            var ids = []
                , keys = [],
                tpl = ['<div class="layui-fsonCheckBox">', '<dl style="width: {{ d.options.width }}px;" class="layui-fsonCheckBox-left layui-form">', '{{# layui.each(d.data,function(index,item){ }}', '<dd><input type="checkbox" lay-filter="fsonCheckBox-left-{{ d.options.id }}" lay-skin="primary" title="{{ item.name }}" value="{{ item.id }}" data-key="{{ index }}" {{# if(d.keys.indexOf(index) !=-1){ }}checked{{# } }}></dd>', '{{# }); }}', '</dl>', '<div class="layui-fsonCheckBox-right layui-tab" lay-allowClose="true" lay-filter="fsonCheckBox-tab-{{ d.options.id }}" style="width: calc(100% - {{d.options.width}}px);">', '<ul class="layui-tab-title">', '{{# layui.each(d.keys,function(index,item){ }}', '<li lay-id="{{ item }}" class="{{# if(index==d.keys.length-1){ }}', 'layui-this', '{{# } }}">{{ d.data[item].name }}</li>', '{{# }); }}', '</ul>',
                    '<div class="layui-tab-content layui-form">',
                    '{{# layui.each(d.keys,function(index,item){ }}',
                    '<div class="layui-tab-item {{# if(index==d.keys.length-1){ }}', 'layui-show', '"{{# } }}">',
                    '<dl>',
                    '{{# layui.each(d.data[item].child,function(index2,item2){ }}',
                    '<dd>',
                    '<input type="checkbox" data-f="{{ index }}" lay-filter="fsonCheckBox-right-{{d.options.id}}" value="{{ item2.id }}" title="{{ item2.name }}" lay-skin="primary" ', '{{# if(d.options.value[index].child.indexOf(item2.id) != -1){ }}', 'checked', '{{# } }}', ' />',
                    '</dd>',
                    '{{# }); }}',
                    '</dl>',
                    '</div>',
                    '{{# }); }}',
                    '</div>', '</div>', '</div>'].join('');

            for (var i in options.value) {
                ids.push(parseInt(options.value[i].id));
            }
            for (var j in data) {
                var index = ids.indexOf(data[j].id);
                if (index != -1) {
                    keys.push(parseInt(j))
                }
            }
            laytpl(tpl).render({data: data, options: options, keys: keys}, function (html) {
                $(options.elem).html(html)
                form.render()
            });

            form.on('checkbox(fsonCheckBox-left-' + options.id + ')', function (checkData) {
                if (checkData.elem.checked) {
                    options.value.push({id: parseInt(checkData.value), child: []})
                } else {
                    var _value = [];
                    for (var k = 0; k < options.value.length; k++) {
                        if (options.value[k].id != checkData.value) {
                            _value.push(options.value[k])
                        }
                    }
                    options.value = _value
                }
                renderData(data, options)
            });

            form.on('checkbox(fsonCheckBox-right-' + options.id + ')', function (checkData) {
                var f = $(checkData.elem).data('f'),
                    id = parseInt(checkData.value)
                if (checkData.elem.checked) {
                    options.value[f].child.push(id)
                } else {
                    options.value[f].child.splice(options.value[f].child.indexOf(id), 1)
                }
            })

            element.on('tabDelete(fsonCheckBox-tab-' + options.id + ')', function (data) {
                console.log(this); //当前Tab标题所在的原始DOM元素
                console.log(data.index); //得到当前Tab的所在下标
                console.log(data.elem); //得到当前的Tab大容器
            });


        }
        , thisIns = function () {
            var that = this
                , options = that.config
                , id = options.id || fsonCheckBox.index;
            options = $.extend(options, {id: id})
            pullData.call(this, options)
            return {
                reload: function (options) {
                    that.reload.call(that, options);
                }, config: options
            }
        }
        //构造器
        , Class = function (options) {
            var that = this;
            that.index = ++fsonCheckBox.index;
            that.config = $.extend({}, that.config, fsonCheckBox.config, options);
            that.render();
        };
    // 默认配置
    Class.prototype.config = {
        elem: ''
        , url: ''
        , width: 160
        , value: []     // 默认值[{1:'1,2,3'},{2:'4,5,6'}]
    };
    //渲染视图
    Class.prototype.render = function () {
        var that = this
            , options = that.config;

    };

    fsonCheckBox.render = function (options) {
        var ins = new Class(options);
        return thisIns.call(ins);
    };


    //加载组件所需样式
    layui.link(layui.cache.base + 'fsonCheckBox/fsonCheckBox.css?v=1', function () {
        //样式加载完毕的回调

    }, MOD_NAME);
    exports(MOD_NAME, fsonCheckBox);
})