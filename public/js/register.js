// JavaScript Document
$(document).ready(function(){
	
	$("#btnSendCode").click(function(){
		var phone = $.trim($("#phone").val());
		//var img = $.trim($("#img").val());
		var username =$.trim($("#username").val());
		var checkCode=$.trim($('#checkCode').val());
		
    	if(phone == ''){
			alert('手机号码不能为空');
			$("#phone").focus();
			return false;
    	}
		if(username==''){
			alert('用户名不能为空');
			$("#username").focus();
			return false;
		}
    	var partten =/^\d{10,13}$/;
      	if (!partten.test(phone)) {
        	alert('手机号码格式不正确');
        	$("#phone").focus();
        	return;
      	}
      	/*if(img == ''){
			alert('图片验证码不能为空');
			$("#img").focus();
			return false;
    	}*/
      
    	
	});
	
	$('#phone').click(function(){
		var pass1=$('#password').val();
	    var pass2=$('#repassword').val();
	    if(pass1!=pass2){
	    	  alert('两次输入的密码不一致');
	      }
	});
})  