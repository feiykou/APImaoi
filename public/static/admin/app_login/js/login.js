$(function(){
    $(document).on('click','#login',function(){
        var $userName=$('#user-name'),
            $pwd=$('#user-pwd');
        if(!$userName.val()) {
            $userName.next().show().find('div').text('请输入用户名');
            return;
        }
        if(!$pwd.val()) {
            $pwd.next().show().find('div').text('请输入密码');
            return;
        }
        var params={
            url:'/submitLogin',
            type:'post',
            data:{ac:$userName.val(),se:$pwd.val()},
            sCallback:function(res){
                console.log(res)
                if(res){
                    if(res.token){
                        window.base.setLocalStorage('token',res.token);
                    }
                    console.log(res)
                    window.location.href = '/';
                }
            },
            eCallback:function(e){
                console.log(e)
                if(e.status==401){
                    if(e.responseText){
                        $('.error-tips').text(e.responseText).show().delay(2000).hide(0);
                    }else{
                        $('.error-tips').text('帐号或密码错误').show().delay(2000).hide(0);
                    }

                }
            }
        };
        window.base.getData(params);
    });

    $(document).on('focus','.normal-input',function(){
        $('.common-error-tips').hide();
    });

    $(document).on('keydown','.normal-input',function(e){
        var e = event || window.event || arguments.callee.caller.arguments[0];
        if(e && e.keyCode==13){
            $('#login').trigger('click');
        }
    });



});