// 涨事件
$('#zhang').on('click', function(){
	
	/*买入价不能为空*/
	if($('#capital').html() == '') {
	layer.alert('数据加载未完成', {
    icon: 5,
    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
	});
	return false;
	}
	var index = layer.load();
	layer.open({
				    type: 1,
				    title: false,
				    closeBtn: 0,
				    skin: 'layui-layer-nobg', //没有背景色
				    // shadeClose: true,
				    content:'<div class="starttext"><span>正等待开局...</span></div>'
				    });
    $.post('/cygjs_fr/index.php/hushen300/investor_detail_add',{capital:$('#capital').html(),invest_type:1,symbol:$('#symbol').val()},function(data){
    	layer.close(index);
    	 // var obj = eval(data);
        // alert(data);
        $('#capital1').html($('#capital').html());
        var json=JSON.parse(data);
        if (json.xznum==false) {
        	layer.closeAll('page');
        	layer.alert('今天的游戏次数已使用完毕，明天再来吧！', {
		    icon: 5,
		    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
			});
			exit();
        }

        /*60秒倒计时开始*/
		var intDiff = parseInt(json.shijian); //120秒倒计时总秒数量
		function timer(intDiff) {
			window.setInterval(function() {
				var day = 0,
					hour = 0,
					minute = 0,
					second = 0; //时间默认值

				if (intDiff > -1) {
						
					second = Math.floor(intDiff) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60);
					if (minute <= 9) minute = '0' + minute;
					if (second <= 9) second = '0' + second;
					if (second < 60 && second >0) {
						layer.closeAll('page');
						layer.open({
						    type: 1,
						    title: false,
						    closeBtn: 0,
						    skin: 'layui-layer-nobg', //没有背景色
						    // shadeClose: true,
						    content:'<div class="countdown"><span>'+second+'</span></div>'
						    });
				    }
				    if (second < 10 && second >0) {
                        $('#chatAudio')[0].play(); //播放声音 
				    }
					if (second == '00') {
						layer.closeAll('page');
						var index = layer.load();
						$.post('/cygjs_fr/index.php/huangjin/huangjin_js_list',{st:$('#st').val(),symbol:$('#symbol').val()},function(data){
							layer.close(index); 
							var json=JSON.parse(data);
							$('#chatAudio1')[0].play(); //播放声音 
							if (json.info=='赢') {
								if (json.num==1) {
									layer.open({
								    type: 1,
								    title: false,
								    skin: 'layui-layer-nobg', //样式类名
								    closeBtn: 0, //不显示关闭按钮
								    scrollbar: false,
								    shift: 2,
								    shadeClose: true, //开启遮罩关闭
								    content: '<div class="bag-popup"><div class="light"></div><div class="span12"><h3 class="text-center">恭喜你，猜对了！<br />获得金裕二元期权<em>3M</em>流量奖励！</h3><p class="text-center">您的账户已累计'+json.flow+'M流量</p><img class="gold_not_gold" src="public/img/3mcard.png"></div><div class="getbtn"><span class="rmb"><a class="ls" href="javascript:void(0);">我要赢现金</a></span><span class="getm"><a class="lliang" href="javascript:void(0);">我要领流量</a></span></div><div class="gamestar"><a class="layui-layer-ico layui-layer-close layui-layer-close2 animated gostart" href="javascript:;">继续游戏</a></div></div>'
								    });
								    $('.ls').on('click', function(){
									           layer.open({
							    type: 1,
							    title: false,
							    skin: 'layui-layer-nobg', //样式类名
							    closeBtn: 0, //不显示关闭按钮
							    scrollbar: false,
							    shift: 2,
							    shadeClose: true, //开启遮罩关闭
							    content: '<div class="bag-popup"><div class="welcomebox1"><span><img src="public/img/ios.gif"></span><span><img src="public/img/andr.gif"></span><p><h3>长按二维码选择“识别”安装二元期权APP</h3></p></div></div>'
							    });
									});
									$('.lliang').on('click', function(){
									           layer.tips('点击右下角注册领取属于您的流量。', '.lliang', {
												       tips: [1, '#78BA32']
												});
									});
								}
								if (json.num==2) {
									layer.open({
								    type: 1,
								    title: false,
								    skin: 'layui-layer-nobg', //样式类名
								    closeBtn: 0, //不显示关闭按钮
								    scrollbar: false,
								    shift: 2,
								    shadeClose: true, //开启遮罩关闭
								    content: '<div class="bag-popup"><div class="light"></div><div class="span12"><h3 class="text-center">恭喜你，猜对了！<br />获得金裕二元期权<em>3M</em>流量奖励！</h3><p class="text-center">您的账户已累计'+json.flow+'M流量</p><img class="gold_not_gold" src="public/img/3mcard.png"></div><div class="getbtn"><span class="rmb"><a class="ls" href="javascript:void(0);">我要赢现金</a></span><span class="getm"><a class="lliang" href="javascript:void(0);">我要领流量</a></span></div><div class="gamestar"><a class="layui-layer-ico layui-layer-close layui-layer-close2 animated gostart" href="javascript:;">继续游戏</a></div></div>'
								    });
								    $('.ls').on('click', function(){
									           layer.open({
							    type: 1,
							    title: false,
							    skin: 'layui-layer-nobg', //样式类名
							    closeBtn: 0, //不显示关闭按钮
							    scrollbar: false,
							    shift: 2,
							    shadeClose: true, //开启遮罩关闭
							    content: '<div class="bag-popup"><div class="welcomebox1"><span><img src="public/img/ios.gif"></span><span><img src="public/img/andr.gif"></span><p><h3>长按二维码选择“识别”安装二元期权APP</h3></p></div></div>'
							    });
									});
									$('.lliang').on('click', function(){
									           layer.tips('点击右下角注册领取属于您的流量。', '.lliang', {
												       tips: [1, '#78BA32']
												});
									});
								}
								if (json.num>2) {
									layer.open({
								    type: 1,
								    title: false,
								    skin: 'layui-layer-nobg', //样式类名
								    closeBtn: 0, //不显示关闭按钮
								    scrollbar: false,
								    shift: 2,
								    shadeClose: true, //开启遮罩关闭
								    content: '<div class="bag-popup"><div class="light"></div><div class="span12"><h3 class="text-center">恭喜你，又猜对了！<br />你已晋升<em>二元期权高手</em>行列，来点真实、刺激的吧！马上参与</h3><p class="text-center">您的账户已累计'+json.flow+'M流量</p><img class="gold_not_gold" src="public/img/3mcard.png"></div><div class="getbtn"><span class="rmb"><a class="ls" href="javascript:void(0);">我要赢现金</a></span><span class="getm"><a class="lliang" href="javascript:void(0);">我要领流量</a></span></div><div class="gamestar"><a class="layui-layer-ico layui-layer-close layui-layer-close2 animated gostart" href="javascript:;">继续游戏</a></div></div>'
								    });
								    $('.ls').on('click', function(){
									           layer.open({
							    type: 1,
							    title: false,
							    skin: 'layui-layer-nobg', //样式类名
							    closeBtn: 0, //不显示关闭按钮
							    scrollbar: false,
							    shift: 2,
							    shadeClose: true, //开启遮罩关闭
							    content: '<div class="bag-popup"><div class="welcomebox1"><span><img src="public/img/ios.gif"></span><span><img src="public/img/andr.gif"></span><p><h3>长按二维码选择“识别”安装二元期权APP</h3></p></div></div>'
							    });
									});
									$('.lliang').on('click', function(){
									           layer.tips('点击右下角注册领取属于您的流量。', '.lliang', {
												       tips: [1, '#78BA32']
												});
									});
								}
								get_current_flow();
							};
							if (json.info=='输') {
								layer.alert('很遗憾，您没猜对，继续努力吧！', {
								    icon: 5,
								    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
								});
							};
							if (json.info=='平') {
								layer.alert('不涨也不跌，再接再厉。');
							};
							// if (json.info=='未开奖') {
							// 	layer.alert('获奖信息去哪里？去历史交易看看。', {
							// 	    icon: 0,
							// 	    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
							// 	});
							// };
						});
						$('.zhang').html('猜涨');
						$('.zhang').attr('id','zhang');
					}else{
						$('.zhang').html('<s></s>' + second + ' ');
						$('.zhang').attr('id','');
					}
				}
				intDiff--;
			}, 1000);
		}
		$(function() {

			timer(intDiff);
		});
		/*60秒倒计时结束*/
    });
});
// 跌事件
$('#die').on('click', function(){
	/*买入价不能为空*/
	if($('#capital').html() == '') {
	layer.alert('数据加载未完成', {
    icon: 5,
    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
	});
	return false;
	}
	var index = layer.load();
	layer.open({
		    type: 1,
		    title: false,
		    closeBtn: 0,
		    skin: 'layui-layer-nobg', //没有背景色
		    // shadeClose: true,
		    content:'<div class="starttext"><span>正等待开局...</span></div>'
		    });
    $.post('/cygjs_fr/index.php/hushen300/investor_detail_add',{capital:$('#capital').html(),invest_type:0,symbol:$('#symbol').val()},function(data){
    	layer.close(index); 
        $('#capital1').html($('#capital').html());
        var json=JSON.parse(data);
        if (json.xznum==false) {
        	layer.closeAll('page');
        	layer.alert('今天的游戏次数已使用完毕，明天再来吧！', {
		    icon: 5,
		    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
			});
			exit();
        }

        /*60秒倒计时开始*/
		var intDiff = parseInt(json.shijian); //120秒倒计时总秒数量
		function timer(intDiff) {
			window.setInterval(function() {
				var day = 0,
					hour = 0,
					minute = 0,
					second = 0; //时间默认值
				if (intDiff > -1) {
					second = Math.floor(intDiff) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60);
					if (minute <= 9) minute = '0' + minute;
					if (second <= 9) second = '0' + second;
					if (second < 60 && second >0) {
						layer.closeAll('page');
						layer.open({
						    type: 1,
						    title: false,
						    closeBtn: 0,
						    skin: 'layui-layer-nobg', //没有背景色
						    // shadeClose: true,
						    content:'<div class="countdown"><span>'+second+'</span></div>'
						    });
				    }
				    if (second < 10 && second >0) {
                        $('#chatAudio')[0].play(); //播放声音 
				    }
					if (second == '00') {
						layer.closeAll('page');
						var index = layer.load();
						$.post('/cygjs_fr/index.php/huangjin/huangjin_js_list',{st:$('#st').val(),symbol:$('#symbol').val()},function(data){
							layer.close(index); 
							var json=JSON.parse(data);
							$('#chatAudio1')[0].play(); //播放声音 
							if (json.info=='赢') {
								if (json.num==1) {
									layer.open({
								    type: 1,
								    title: false,
								    skin: 'layui-layer-nobg', //样式类名
								    closeBtn: 0, //不显示关闭按钮
								    scrollbar: false,
								    shift: 2,
								    shadeClose: true, //开启遮罩关闭
								    content: '<div class="bag-popup"><div class="light"></div><div class="span12"><h3 class="text-center">恭喜你，猜对了！<br />获得金裕二元期权<em>3M</em>流量奖励！</h3><p class="text-center">您的账户已累计'+json.flow+'M流量</p><img class="gold_not_gold" src="public/img/3mcard.png"></div><div class="getbtn"><span class="rmb"><a class="ls" href="javascript:void(0);">我要赢现金</a></span><span class="getm"><a class="lliang" href="javascript:void(0);">我要领流量</a></span></div><div class="gamestar"><a class="layui-layer-ico layui-layer-close layui-layer-close2 animated gostart" href="javascript:;">继续游戏</a></div></div>'
								    });
								    $('.ls').on('click', function(){
									           layer.tips('赢现金活动即将推出，注册订阅，获取最新动态。', '.ls', {
												       tips: [1, '#78BA32']
												});
									});
									$('.lliang').on('click', function(){
									           layer.tips('点击右下角注册领取属于您的流量。', '.lliang', {
												       tips: [1, '#78BA32']
												});
									});
								}
								if (json.num==2) {
									layer.open({
								    type: 1,
								    title: false,
								    skin: 'layui-layer-nobg', //样式类名
								    closeBtn: 0, //不显示关闭按钮
								    scrollbar: false,
								    shift: 2,
								    shadeClose: true, //开启遮罩关闭
								    content: '<div class="bag-popup"><div class="light"></div><div class="span12"><h3 class="text-center">恭喜你，猜对了！<br />获得金裕二元期权<em>3M</em>流量奖励！</h3><p class="text-center">您的账户已累计'+json.flow+'M流量</p><img class="gold_not_gold" src="public/img/3mcard.png"></div><div class="getbtn"><span class="rmb"><a class="ls" href="javascript:void(0);">我要赢现金</a></span><span class="getm"><a class="lliang" href="javascript:void(0);">我要领流量</a></span></div><div class="gamestar"><a class="layui-layer-ico layui-layer-close layui-layer-close2 animated gostart" href="javascript:;">继续游戏</a></div></div>'
								    });
								    $('.ls').on('click', function(){
									           layer.tips('赢现金活动即将推出，注册订阅，获取最新动态。', '.ls', {
												       tips: [1, '#78BA32']
												});
									});
									$('.lliang').on('click', function(){
									           layer.tips('点击右下角注册领取属于您的流量。', '.lliang', {
												       tips: [1, '#78BA32']
												});
									});
								}
								if (json.num>2) {
									layer.open({
								    type: 1,
								    title: false,
								    skin: 'layui-layer-nobg', //样式类名
								    closeBtn: 0, //不显示关闭按钮
								    scrollbar: false,
								    shift: 2,
								    shadeClose: true, //开启遮罩关闭
								    content: '<div class="bag-popup"><div class="light"></div><div class="span12"><h3 class="text-center">恭喜你，又猜对了！<br />你已晋升<em>二元期权高手</em>行列，来点真实、刺激的吧！马上参与</h3><p class="text-center">您的账户已累计'+json.flow+'M流量</p><img class="gold_not_gold" src="public/img/3mcard.png"></div><div class="getbtn"><span class="rmb"><a class="ls" href="javascript:void(0);">我要赢现金</a></span><span class="getm"><a class="lliang" href="javascript:void(0);">我要领流量</a></span></div><div class="gamestar"><a class="layui-layer-ico layui-layer-close layui-layer-close2 animated gostart" href="javascript:;">继续游戏</a></div></div>'
								    });
								    $('.ls').on('click', function(){
									           layer.tips('赢现金活动即将推出，注册订阅，获取最新动态。', '.ls', {
												       tips: [1, '#78BA32']
												});
									});
									$('.lliang').on('click', function(){
									           layer.tips('点击右下角注册领取属于您的流量。', '.lliang', {
												       tips: [1, '#78BA32']
												});
									});
								}
								get_current_flow();	
							};
							if (json.info=='输') {
								layer.alert('很遗憾，您没猜对，继续努力吧！', {
								    icon: 5,
								    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
								});
							};
							if (json.info=='平') {
								layer.alert('不涨也不跌，再接再厉。');
							};
							// if (json.info=='未开奖') {
							// 	layer.alert('获奖信息去哪里？去历史交易看看。', {
							// 	    icon: 0,
							// 	    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
							// 	});
							// };
						});
						$('.die').html('猜跌');
						$('.die').attr('id','zhang');
					}else{
						$('.die').html('<s></s>' + second + ' ');
						$('.die').attr('id','');
					}
				}
				intDiff--;
			}, 1000);
		}
		$(function() {
			timer(intDiff);
		});
		/*60秒倒计时结束*/
    });
});
$('#ls').on('click', function(){
    layer.open({
        type: 2,
        title: '交易历史详情',
        shadeClose: true,
        shade: false,
        maxmin: true, //开启最大化最小化按钮
        area: ['893px', '600px'],
        content: '/cygjs_fr/index.php/huangjin/huangjin_html_list'
    });
});
$('#sinalink').on('click', function(){
           $("#dd").attr('src','/cygjs_fr/index.php/huangjin/huangjin_sinalink');
           $('#symbol').val('hf_GC');
});
$('#jinyulink').on('click', function(){
           $("#dd").attr('src','/cygjs_fr/index.php/huangjin/huangjin_link');
           $('#symbol').val('XAU');
});
$('.ls').on('click', function(){
           layer.tips('赢现金活动即将推出，注册订阅，获取最新动态。', '.ls', {
			       tips: [1, '#78BA32']
			});
});
$(function(){
	$('#tips').on('click', function(){
			layer.open({
			    type: 1,
			    title: false,
			    skin: 'layui-layer-nobg', //样式类名
			    closeBtn: 0, //不显示关闭按钮
			    scrollbar: false,
			    shift: 2,
			    shadeClose: true, //开启遮罩关闭
			    content: '<div class="bag-popup"><div class="welcomebox"><h3 class="text-center">亲,每天有<em>五次</em>分享机会<br />可以免费获取流量的哟！</h3></div><div class="getbtn"><span class="getm"><a href="javascript:void(0)" id="rule_link">游戏规则</a></span></div></div>'
			    });
		$('#rule_link').on('click', function(){
			layer.open({
			    type: 1,
			    title: false,
			    skin: 'layui-layer-nobg', //样式类名
			    closeBtn: 1, //不显示关闭按钮
			    scrollbar: true,
			    shift: 2,
			    shadeClose: true, //开启遮罩关闭
			    content: '<div class="bag-popup2"><div id="rule_cont2" class="rule_cont"><center><b>《活动须知》</b></center><h2>2016年金裕二元期权<br />“玩游戏赚流量，无限流量助你玩转猴年”</h2><h3>一、活动时间：即日起至</h3><h3>二、活动介绍：</h3><p>1、从手机界面进入游戏，金裕二元期权猜涨跌。每次猜对即送你3M流量，根据兑换规则，流量才可以领取，可以提取。</p><p>2、奔走相告抢流量，动动你的手指，把此链接分享到你的朋友圈，即送1M流量，每天有5次机会哦！</p><h3>三、兑换流量规则：</h3><p>流量可以累积，累积满30M及以上即可作首次提取（第二次后需累积满100M以上方可提取），点击页面“提取流量”按钮，根据提示操作即可，就是这么简单、便捷。</p><h3>四、活动说明：</h3><p>1、请按照须知要求参与活动，一旦发现有非正常行为作弊的用户，金裕将有权取消其继续参与活动的资格。</p><p>2、本次活动最终解释权归金裕所有。</p></div></div>'
			});
		});
	});
});

$(function(){  
	$('<audio id="chatAudio"><source src="public/audio/pi.mp3" type="audio/ogg"> <source src="public/audio/pi.mp3" type="audio/mpeg"><source src="notify.wav" type="audio/wav"> </audio>').appendTo('body');//载入声音文件
	$('<audio id="chatAudio1"><source src="public/audio/chime.mp3" type="audio/ogg"> <source src="public/audio/chime.mp3" type="audio/mpeg"><source src="notify.wav" type="audio/wav"> </audio>').appendTo('body');//载入声音文件
	$('<audio id="chatAudio2"><source src="public/audio/cat.mp3" type="audio/ogg"> <source src="public/audio/chime.mp3" type="audio/mpeg"><source src="notify.wav" type="audio/wav"> </audio>').appendTo('body');//载入声音文件

}); 
    $("#oy").click(function(){ 
    	
            $('#chatAudio2')[0].play(); //播放声音 
            layer.tips('恭喜发财！', '#oy', {
					       tips: [1, '#78BA32']
					});
				
    }); 
window.setInterval(function() {
	    
	    var x = 12;
		var y = 1;
		var rand = parseInt(Math.random() * (x - y + 1) + y);
		switch(rand)
				{
				case 1:
				    layer.tips('棒棒的！', '#oy', {
					       tips: [1, '#78BA32']
					});
				  break;
				case 2:
				    layer.tips('恭喜发财！', '#oy', {
					       tips: [1, '#78BA32']
					});
				  break;
			  case 3:
			    layer.tips('再接再厉！', '#oy', {
				       tips: [1, '#78BA32']
				});
			  break;
			  case 4:
			    layer.tips('我叫猴萌！', '#oy', {
				       tips: [1, '#78BA32']
				});
			  break;
			  case 5:
			    layer.tips('别担心，有我陪着你！', '#oy', {
				       tips: [1, '#78BA32']
				});
			  break;
			  case 6:
			    layer.tips('好棒哦！', '#oy', {
				       tips: [1, '#78BA32']
				});
			  break;
				default:

				}
}, parseInt((Math.random() * (99 - 1 + 1))*2000));