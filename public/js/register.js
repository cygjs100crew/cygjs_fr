// JavaScript Document
$(document).ready(function(){
	
	$("#btnSendCode1").click(function(){
		var phone = $.trim($("#phone1").val());
		//var img = $.trim($("#img").val());
		var username =$.trim($("#username1").val());
		var checkCode=$.trim($('#checkCode').val());
		
		
		if(username==''){
			alert('用户名不能为空');
			$("#username1").focus();
			return false;
		}
    	var partten =/^\d{10,13}$/;
      	if (!partten.test(phone)) {
        	alert('手机号码格式不正确');
        	$("#phone1").focus();
        	return;
      	}
      	/*if(img == ''){
			alert('图片验证码不能为空');
			$("#img").focus();
			return false;
    	}*/
      
    	
	});
	$("#btnSendCode2").click(function(){
		var phone = $.trim($("#phone2").val());
		var checkCode=$.trim($('#checkCode2').val());
		
		if(phone==''){
			alert('手机号不能为空');
			$("#phone2").focus();
			return false;
		}
    	var partten =/^\d{10,13}$/;
      	if (!partten.test(phone)) {
        	alert('手机号码格式不正确');
        	$("#phone2").focus();
        	return;
      	}
    	
	});
	
	
	$('#phone').click(function(){
		var pass1=$('#password').val();
	    var pass2=$('#repassword').val();
	    if(pass1!=pass2){
	    	  alert('两次输入的密码不一致');
	      }
	});
	
})  