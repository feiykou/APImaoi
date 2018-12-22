
// function edit(title,url) {
//     var index = layer.open({
//         type: 2,
//         title: title,
//         content: url
//     });
//     layer.full(index);
// }

//设置上传个数
function setNoDrop($dom,num){
    var num = num?num:1;
    var length = $dom.length;
    if(length >=num){
        $dom.parents(".queueList").prev().find(".webuploader-pick").css({
            'position': 'relative',
            'zIndex': 1,
            'background': '#f0f0f0',
            'cursor': 'no-drop'
        });
    }
}
// 删除图片
function cancelImg(callback) {
    $(".filelist-exist").on("click","span.cancel",function(){
        $this = $(this);
        layer.confirm('确定要删除图片吗？', {icon: 3, title:'提示'}, function(index){
            $btnDom = $this.parents(".queueList").prev().find(".webuploader-pick");
            $btnDom.css({
                'background': "none",
                'cursor': 'pointer',
                'z-index': 0});
            $li = $this.parents("li");
            $parents = $li.parents('.filelist-exist');
            $li.remove();
            if($parents.find("li").length == 0){
                $parents.remove();
            }
            callback && callback($this);
            layer.close(index);
        });
    });
}


/**
 * 编辑图片
 */
$(function () {
    $(".filelist-exist").parents(".queueList").css({
        'display': 'block',
        'opacity': 1
    });
    $(".filelist-exist").find("li").hover(function(){
        $(this).find(".file-panel").stop(true).animate({height:"30px"},300);
    },function(){
        $(this).find(".file-panel").stop(true).animate({height:0},300);
    });



});


function editFull(title,url){
    var index = layer.open({
        type: 2,
        title:  title,
        content: url
    });
    layer.full(index);
}


function edit(title,url,w,h){


    layer.open({
        type: 2,
        area: [w, h],
        fixed: false, //不固定
        maxmin: true,
        content: url,
        title:title
    });
}

function del(obj, url){
    layer.confirm('确认要删除吗？',function(){
        $.ajax({
            type: 'POST',
            url: url,
            dataType: 'json',
            success: function(){
                $(obj).parents("tr").remove();
                layer.msg('已删除!',{icon:1,time:1000});
            },
            error:function(data) {
                layer.msg(data.msg,{icon:1,time:1000});
            }
        });
    });
}

function add(title,url){
	layui.layer.open({
		title : title,
		type : 2,
		content : url,
		area: ['100%', '100%'],
		success : function(layero, index){
			setTimeout(function(){
				layui.layer.tips('点击此处返回', '.layui-layer-setwin .layui-layer-close', {
					tips: 2
				});
			},500)
		}
	})
}


/**
 *
 * 针对图片上传获取字符串
 * $childsDom  元素集
 * attr        属性值
 * delimiter   分隔符
 * len         截取个数
 *
 **/
function setUpdateUrl($childsDom,attr,delimiter,len){
    var params = "";
    var index = 0;
    var delimiter = delimiter?delimiter:";";
    var attr = attr?attr:"src";
    len = len?len:false;
    for(var i=0;i<$childsDom.length;i++){
        $li = $childsDom.eq(i);
        if($li.hasClass("state-complete")){
            index++;
            if(len && index>len){
                break;
            }
            params += $li.data(attr) + delimiter;
        }
    }
    // 删除最后一个分隔符
    params = params.substring(0,params.length-1);
    return params;
}




layui.use(['form','layer','jquery'],function(){
    var form = layui.form,
        layer = layui.layer,
        $ = layui.jquery;


    // $(".batchDel").click(function(){
    //     var $checkbox = $('.news_list tbody input[type="checkbox"][name="checked"]');
    //     var $checked = $('.news_list tbody input[type="checkbox"][name="checked"]:checked');
    //     if($checkbox.is(":checked")){
    //         layer.confirm('确定删除选中的信息？',{icon:3, title:'提示信息'},function(index){
    //             var index = layer.msg('删除中，请稍候',{icon: 16,time:false,shade:0.5});
    //             var idsArr = [];
    //
    //             for(var j=0;j<$checked.length;j++){
    //                 var $trDom = $checked.eq(j).parents('tr');
    //                 idsArr.push($trDom.data('id'));
    //             }
    //             $.ajax({
    //                 url: url,
    //                 type: "post",
    //                 data: {idsArr:idsArr},
    //                 success: function(){
    //                     //删除数据
    //                     for(var j=0;j<$checked.length;j++){
    //                         $checked.eq(j).parents('tr').remove();
    //                     }
    //                     layer.msg('已删除!',{icon:1,time:1000});
    //                 },
    //                 error:function(data) {
    //                     console.log(data.msg);
    //                 }
    //             });
    //
    //             form.render();
    //             layer.close(index);
    //             layer.msg("删除成功");
    //         })
    //     }else{
    //         layer.msg("请选择需要删除的文章");
    //     }
    // });

    $("body").on("click",".news_del",function(){  //删除
        var _this = $(this);
        layer.confirm('确定删除此信息？',{icon:3, title:'提示信息'},function(index){
        	var url = _this.data('url');
            $.ajax({
                type: 'POST',
                url: url,
                dataType: 'json',
                success: function(){
                    _this.parents("tr").remove();
                    layer.msg('已删除!',{icon:1,time:1000});
                },
                error:function(data) {
                    layer.msg(data.msg,{icon:1,time:1000});
                }
            });
            layer.close(index);
        });
    });

    //全选
    form.on('checkbox(allChoose)', function(data){
        var child = $(data.elem).parents('table').find('tbody input[type="checkbox"][name="checked"]:not([name="show"])');
        child.each(function(index, item){
            item.checked = data.elem.checked;
        });
        form.render('checkbox');
    });

    //通过判断文章是否全部选中来确定全选按钮是否选中
    form.on("checkbox(choose)",function(data){
        var child = $(data.elem).parents('table').find('tbody input[type="checkbox"]:not([name="show"])');
        var childChecked = $(data.elem).parents('table').find('tbody input[type="checkbox"]:not([name="show"]):checked');
        if(childChecked.length == child.length){
            $(data.elem).parents('table').find('thead input#allChoose').get(0).checked = true;
        }else{
            $(data.elem).parents('table').find('thead input#allChoose').get(0).checked = false;
        }
        form.render('checkbox');
    });

    //是否展示
    form.on('switch(isShow)', function(data){
        var url = $(data.elem).data('url');
        var status_name = $(data.elem).data('status') || 'status';
        var status = 1;
        if(!data.elem.checked){
            status = 0;
        }
        var index = layer.msg('修改中，请稍候',{icon: 16,time:false,shade:0.5});
        $.ajax({
            url: url+'?'+status_name+'='+status+'&attr='+status_name,
            success: function(res){
                layer.close(index);
                layer.msg(res.msg);
            }
        })
    });





});
