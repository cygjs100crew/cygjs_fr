// 涨事件
$('#zhang').on('click', function(){
	/*买入价不能为空*/
	if($('#capital').val() == '') {
	layer.alert('数据加载未完成', {
    icon: 5,
    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
	});
	return false;
	}
	var index = layer.load();
	$.post('/cygjs_fr/index.php/hushen300/investor_detail_add',{capital:$('#capital').val(),invest_type:1,symbol:$('#symbol').val()},function(data){
    	layer.close(index);
    	 // var obj = eval(data);
        // alert(data);
        $('#capital1').val($('#capital').val());
        var json=JSON.parse(data);
  //       layer.alert('看涨。买入价：'+$('#capital').val()+'。中奖请查看历史记录。', {
	 //    icon: 1,
	 //    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
		// });
		layer.open({
						    type: 1,
						    title: false,
						    closeBtn: 0,
						    skin: 'layui-layer-nobg', //没有背景色
						    // shadeClose: true,
						    content:'<img src="public/img/bisai_text.png" />'
						    });
        /*60秒倒计时开始*/
		var intDiff = parseInt(60); //120秒倒计时总秒数量
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
					if (second < 60 && second >59) {
						layer.closeAll('page');
						layer.open({
						    type: 1,
						    title: false,
						    closeBtn: 0,
						    skin: 'layui-layer-nobg', //没有背景色
						    // shadeClose: true,
						    content:'<img src="public/img/challenge.png" />'
						    });
					}
					// if (second < 59 && second >12) {
					// 	layer.closeAll('page');
					// 	if (Number($('#capital').val())<Number($('#capital1').val())) {
					//     	layer.open({
					// 	    type: 1,
					// 	    title: false,
					// 	    closeBtn: 0,
					// 	    skin: 'layui-layer-nobg', //没有背景色
					// 	    // shadeClose: true,
					// 	    content:'<img src="public/img/y2.png" />'
					// 	    });
					//     }
					//     if (Number($('#capital').val())>Number($('#capital1').val())) {
					//     	layer.open({
					// 	    type: 1,
					// 	    title: false,
					// 	    closeBtn: 0,
					// 	    skin: 'layui-layer-nobg', //没有背景色
					// 	    // shadeClose: true,
					// 	    content:'<img src="public/img/y1.png" />'
					// 	    });
					//     };
					//     if (Number($('#capital').val())==Number($('#capital1').val())) {challenge_hou.png
					//     	layer.open({
					// 	    type: 1,
					// 	    title: false,
					// 	    closeBtn: 0,
					// 	    skin: 'layui-layer-nobg', //没有背景色
					// 	    // shadeClose: true,
					// 	    content:'<img src="public/img/y5.png" />'
					// 	    });
					//     };
					// }
					if (second < 12 && second >10) {
						layer.closeAll('page');
						layer.open({
						    type: 1,
						    title: false,
						    closeBtn: 0,
						    skin: 'layui-layer-nobg', //没有背景色
						    // shadeClose: true,
						    content:'<img src="public/img/challenge.png" />'
						    });
					}
					if (second < 10 && second >0) {
						layer.closeAll('page');
						layer.open({
						    type: 1,
						    title: false,
						    closeBtn: 0,
						    skin: 'layui-layer-nobg', //没有背景色
						    // shadeClose: true,
						    content:'<img src="public/img/jishi_'+second+'.png" />'
						    });
				    }
					if (second == '00') {
						layer.closeAll('page');
						var index = layer.load();
						$.post('/cygjs_fr/index.php/hushen300/huangjin_js_list',function(data){
							layer.close(index); 
							var json=JSON.parse(data);
							if (json.info=='赢') {
								if (json.num==1) {
									layer.open({
								    type: 1,
								    title: false,
								    area: ['340px', '280px'],
								    skin: 'layui-layer-demo', //样式类名
								    closeBtn: 0, //不显示关闭按钮
								    scrollbar: false,
								    shift: 2,
								    shadeClose: true, //开启遮罩关闭
								    content: '<div class="span12"><h3 class="text-center">恭喜您，猜对了，继续加油哦！</h3><p class="text-center">奖品已经存放入您的账户，连续三次可获得1M数据流量。</p><img class="gold_not_gold" src="public/img/tong_bei.png"><img class="gold_x" src="public/oh_static/img/x.png"><img src="public/oh_static/img/buzu_one.png"></div>'
								    });
								}
								if (json.num==2) {
									layer.open({
								    type: 1,
								    title: false,
								    area: ['340px', '280px'],
								    skin: 'layui-layer-demo', //样式类名
								    closeBtn: 0, //不显示关闭按钮
								    scrollbar: false,
								    shift: 2,
								    shadeClose: true, //开启遮罩关闭
								    content: '<div class="span12"><h3 class="text-center">恭喜你，梅开二度，离流量大包仅一步之遥了！</h3><p class="text-center">奖品已经存放入您的账户，连续三次可获得1M数据流量。</p><img class="gold_not_gold" src="public/img/silver_bei.png"><img class="gold_x" src="public/oh_static/img/x.png"><img src="public/oh_static/img/buzu_one.png"></div>'
								    });
								}
								if (json.num==3) {
									layer.open({
								    type: 1,
								    title: false,
								    area: ['340px', '280px'],
								    skin: 'layui-layer-demo', //样式类名
								    closeBtn: 0, //不显示关闭按钮
								    scrollbar: false,
								    shift: 2,
								    shadeClose: true, //开启遮罩关闭
								    content: '<div class="span12"><h3 class="text-center">恭喜你，连中三元，获得金裕二元期权1M流量奖励！</h3><p class="text-center">奖品已经存放入您的账户，连续三次可获得1M数据流量。</p><img class="gold_not_gold" src="public/img/gold_bei.png"><img class="gold_x" src="public/oh_static/img/x.png"><img src="public/oh_static/img/buzu_one.png"></div>'
								    });
								}
								
							};
							if (json.info=='输') {
								layer.alert('很遗憾，您没猜对了，谢谢参与！继续努力吧！', {
								    icon: 5,
								    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
								});
							};
							if (json.info=='平') {
								layer.alert('不涨也不跌，再接再厉。');
							};
							if (json.info=='未开奖') {
								layer.alert('获奖信息去哪里？去历史交易看看。', {
								    icon: 0,
								    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
								});
							};
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
	if($('#capital').val() == '') {
	layer.alert('数据加载未完成', {
    icon: 5,
    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
	});
	return false;
	}
	var index = layer.load();
    $.post('/cygjs_fr/index.php/hushen300/investor_detail_add',{capital:$('#capital').val(),invest_type:0,symbol:$('#symbol').val()},function(data){
    	layer.close(index); 
    	 // var obj = eval(data);
        // alert(data);
        $('#capital1').val($('#capital').val());
        var json=JSON.parse(data);
  //       layer.alert('看跌。买入价：'+$('#capital').val()+'。中奖请查看历史记录。', {
	 //    icon: 1,
	 //    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
		// });
		layer.open({
				    type: 1,
				    title: false,
				    closeBtn: 0,
				    skin: 'layui-layer-nobg', //没有背景色
				    // shadeClose: true,
				    content:'<img src="public/img/bisai_text.png" />'
				    });
        /*60秒倒计时开始*/
		var intDiff = parseInt(60); //120秒倒计时总秒数量
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
					if (second < 60 && second >59) {
						layer.closeAll('page');
						layer.open({
						    type: 1,
						    title: false,
						    closeBtn: 0,
						    skin: 'layui-layer-nobg', //没有背景色
						    // shadeClose: true,
						    content:'<img src="public/img/challenge.png" />'
						    });
					}
					// if (second < 59 && second >12) {
					// 	layer.closeAll('page');
					// 	if (Number($('#capital').val())>Number($('#capital1').val())) {
					//     	layer.open({
					// 	    type: 1,
					// 	    title: false,
					// 	    closeBtn: 0,
					// 	    skin: 'layui-layer-nobg', //没有背景色
					// 	    // shadeClose: true,
					// 	    content:'<img src="public/img/y2.png" />'
					// 	    });
					//     }
					//     if (Number($('#capital').val())<Number($('#capital1').val())) {
					//     	layer.open({
					// 	    type: 1,
					// 	    title: false,
					// 	    closeBtn: 0,
					// 	    skin: 'layui-layer-nobg', //没有背景色
					// 	    // shadeClose: true,
					// 	    content:'<img src="public/img/y1.png" />'
					// 	    });
					//     };
					//     if (Number($('#capital').val())==Number($('#capital1').val())) {challenge_hou.png
					//     	layer.open({
					// 	    type: 1,
					// 	    title: false,
					// 	    closeBtn: 0,
					// 	    skin: 'layui-layer-nobg', //没有背景色
					// 	    // shadeClose: true,
					// 	    content:'<img src="public/img/y5.png" />'
					// 	    });
					//     };
					// }
					if (second < 12 && second >10) {
						layer.closeAll('page');
						layer.open({
						    type: 1,
						    title: false,
						    closeBtn: 0,
						    skin: 'layui-layer-nobg', //没有背景色
						    // shadeClose: true,
						    content:'<img src="public/img/challenge.png" />'
						    });
					}
					if (second < 10 && second >0) {
						layer.closeAll('page');
						layer.open({
						    type: 1,
						    title: false,
						    closeBtn: 0,
						    skin: 'layui-layer-nobg', //没有背景色
						    // shadeClose: true,
						    content:'<img src="public/img/jishi_'+second+'.png" />'
						    });
				    }
					if (second == '00') {
						layer.closeAll('page');
						var index = layer.load();
						$.post('/cygjs_fr/index.php/hushen300/huangjin_js_list',function(data){
							layer.close(index); 
							var json=JSON.parse(data);
							if (json.info=='赢') {
								if (json.num==1) {
									layer.open({
								    type: 1,
								    title: false,
								    area: ['340px', '280px'],
								    skin: 'layui-layer-demo', //样式类名
								    closeBtn: 0, //不显示关闭按钮
								    scrollbar: false,
								    shift: 2,
								    shadeClose: true, //开启遮罩关闭
								    content: '<div class="span12"><h3 class="text-center">恭喜您，猜对了，继续加油哦！</h3><p class="text-center">奖品已经存放入您的账户，连续三次可获得1M数据流量。</p><img class="gold_not_gold" src="public/img/tong_bei.png"><img class="gold_x" src="public/oh_static/img/x.png"><img src="public/oh_static/img/buzu_one.png"></div>'
								    });
								}
								if (json.num==2) {
									layer.open({
								    type: 1,
								    title: false,
								    area: ['340px', '280px'],
								    skin: 'layui-layer-demo', //样式类名
								    closeBtn: 0, //不显示关闭按钮
								    scrollbar: false,
								    shift: 2,
								    shadeClose: true, //开启遮罩关闭
								    content: '<div class="span12"><h3 class="text-center">恭喜你，梅开二度，离流量大包仅一步之遥了！</h3><p class="text-center">奖品已经存放入您的账户，连续三次可获得1M数据流量。</p><img class="gold_not_gold" src="public/img/silver_bei.png"><img class="gold_x" src="public/oh_static/img/x.png"><img src="public/oh_static/img/buzu_one.png"></div>'
								    });
								}
								if (json.num==3) {
									layer.open({
								    type: 1,
								    title: false,
								    area: ['340px', '280px'],
								    skin: 'layui-layer-demo', //样式类名
								    closeBtn: 0, //不显示关闭按钮
								    scrollbar: false,
								    shift: 2,
								    shadeClose: true, //开启遮罩关闭
								    content: '<div class="span12"><h3 class="text-center">恭喜你，连中三元，获得金裕二元期权1M流量奖励！</h3><p class="text-center">奖品已经存放入您的账户，连续三次可获得1M数据流量。</p><img class="gold_not_gold" src="public/img/gold_bei.png"><img class="gold_x" src="public/oh_static/img/x.png"><img src="public/oh_static/img/buzu_one.png"></div>'
								    });
								}
								
							};
							if (json.info=='输') {
								layer.alert('很遗憾，您没猜对了，谢谢参与！继续努力吧！', {
								    icon: 5,
								    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
								});
							};
							if (json.info=='平') {
								layer.alert('不涨也不跌，再接再厉。');
							};
							if (json.info=='未开奖') {
								layer.alert('获奖信息去哪里？去历史交易看看。', {
								    icon: 0,
								    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
								});
							};
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
        content: '/cygjs_fr/index.php/hushen300/lishi_html_list'
    });
});
$('#sinalink').on('click', function(){
           $("#dd").attr('src','/cygjs_fr/index.php/hushen300/hushen_sinalink');
           $('#symbol').val('s_sz399300');
});
$('#jinyulink').on('click', function(){
           $("#dd").attr('src','/cygjs_fr/index.php/hushen300/hushen_link');
           $('#symbol').val('CFIFZ5');
});

// setInterval(function(){  
//     $.ajax({
//         url: "http://hq.sinajs.cn/?list=s_sz399300",
//         method: 'GET',
//         dataType: "script",
//         scriptCharset: "gb2312",
//         cache: true,
//         success: function(data, textStatus){
//              var hq = hq_str_s_sz399300.split(",");
//             var title,rt_hq = '';
//             if(Number(hq[2])>Number(0)){
//                 title = hq[3]+' +'+String(((Number(hq[3])-Number(hq[2]))*100/Number(hq[2])).toFixed(2))+'%';
//                 rt_hq = '<span style="color:Red;">'+hq[1]+'&nbsp;'+hq[2]+'&nbsp;'+hq[3]+'%</span>';
//             }else if(Number(hq[2])<Number(0)){
//                 title = hq[3]+' '+String(((Number(hq[3])-Number(hq[2]))*100/Number(hq[2])).toFixed(2))+'%';
//                 rt_hq = '<span style="color:Lime;">'+hq[1]+'&nbsp;'+hq[2]+'&nbsp;'+hq[3]+'%</span>';
//             }else{
//                 title = hq[3]+' '+String(((Number(hq[3])-Number(hq[2]))*100/Number(hq[2])).toFixed(2))+'%';
//                rt_hq = '<span style="color:Black;">'+hq[1]+'&nbsp;'+hq[2]+'&nbsp;'+hq[3]+'%</span>';
//             }
//             document.title = title;
//             	if (Number($('#capital').val())<Number(hq[1])) {
// 	                	$(".button_ab dl").css("border","4px solid Red");
// 	                }
// 	                if (Number($('#capital').val())>Number(hq[1])) {
// 	                	$(".button_ab dl").css("border","4px solid Lime");
// 	                };
// 	                if (Number($('#capital').val())==Number(hq[1])) {
// 	                	$(".button_ab dl").css("border","4px solid Black");
// 	                };
//                 var html_src = '实时行情: '+rt_hq+"|量: "+hq[4]+"手 | 额: "+hq[5]+"万元";
//                 $("#m-chart-realhq").html(html_src);
//                 $('#capital').val(hq[1]);
//                 // $.post('/cygjs_fr/index.php/hushen300/data_add',{price:hq[1]},function(data){
//                 //     // console.log(data.data_date);
//                 //     var json=JSON.parse(data);
//                 //     $("#sxin").html(json.info);
//                 // });
//             } 
//         });
// },5000);

// setInterval(function(){
//     $.post('/cygjs_fr/index.php/hushen300/price',{symbol:'CFIFZ5'},function(data){
//     // alert(data);
//     var json=JSON.parse(data);
//         if (Number($('#capital').val())<Number(json.price)) {
// 	    	$(".button_ab dl").css("border","4px solid Red");
// 	    }
// 	    if (Number($('#capital').val())>Number(json.price)) {
// 	    	$(".button_ab dl").css("border","4px solid Lime");
// 	    };
// 	    if (Number($('#capital').val())==Number(json.price)) {
// 	    	$(".button_ab dl").css("border","4px solid Black");
// 	    };
//     $('#capital').val(json.price);
//     $('#lianying').html(json.num);
//     	if ($('#timeed').val()==json.time) {
// 	    	$('#tip').html('服务器维护,停止交易！<a href="http://test-wx.cygjs100.com/cygjs_fr/index.php/huangjin_ed/index">历史数据接着玩</a>');
// 	    	$('#capital').val('');
// 	    	$('#capital1').val('');
// 	    };
//     $('#timeed').val(json.time);
//     });
// },5000);

// $('.tx').on('click', function(){
// 	layer.alert('流量取现请联系客服确认取现时间。', {
//     icon: 6,
//     skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
// 	});
// });