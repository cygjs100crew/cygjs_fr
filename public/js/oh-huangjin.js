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
    $.post('/cygjs_fr/index.php/huangjin/investor_detail_add',{capital:$('#capital').val(),invest_type:1,symbol:$('#symbol').val()},function(data){
    	 // var obj = eval(data);
        // alert(data);
        $('#capital1').val($('#capital').val());
        var json=JSON.parse(data);
        layer.alert('看涨。买入价：'+$('#capital').val()+'。中奖请查看历史记录。', {
	    icon: 1,
	    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
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
					if (second == '00') {
						$.post('/cygjs_fr/index.php/huangjin/huangjin_js_list',function(data){
							layer.close(index); 
							var json=JSON.parse(data);
							if (json.info=='赢') {
								layer.open({
							    type: 1,
							    title: false,
							    area: ['340px', '280px'],
							    skin: 'layui-layer-demo', //样式类名
							    closeBtn: 0, //不显示关闭按钮
							    scrollbar: false,
							    shift: 2,
							    shadeClose: true, //开启遮罩关闭
							    content: '<div class="span12"><h3 class="text-center">恭喜'+json.info+'您在一次创盈二元期权获得1M奖励</h3><p class="text-center">奖品已经存放入您的账户，可进入个人中心-我的奖品查看详情</p><img class="gold_not_gold" src="public/oh_static/img/shore_jinibi_d.png"><img class="gold_x" src="public/oh_static/img/x.png"><img src="public/oh_static/img/buzu_one.png"></div>'
							    });
							};
							if (json.info=='输') {
								layer.alert('很遗憾，没中。', {
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
						$('.zhang').html('涨');
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
    $.post('/cygjs_fr/index.php/huangjin/investor_detail_add',{capital:$('#capital').val(),invest_type:0,symbol:$('#symbol').val()},function(data){
    	 // var obj = eval(data);
        // alert(data);
        $('#capital1').val($('#capital').val());
        var json=JSON.parse(data);
        layer.alert('看跌。买入价：'+$('#capital').val()+'。中奖请查看历史记录。', {
	    icon: 1,
	    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
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
					if (second == '00') {
						$.post('/cygjs_fr/index.php/huangjin/huangjin_js_list',function(data){
							layer.close(index); 
							var json=JSON.parse(data);
							if (json.info=='赢') {
								layer.open({
							    type: 1,
							    title: false,
							    area: ['340px', '280px'],
							    skin: 'layui-layer-demo', //样式类名
							    closeBtn: 0, //不显示关闭按钮
							    scrollbar: false,
							    shift: 2,
							    shadeClose: true, //开启遮罩关闭
							    content: '<div class="span12"><h3 class="text-center">恭喜'+json.info+'您在一次创盈二元期权获得1M奖励</h3><p class="text-center">奖品已经存放入您的账户，可进入个人中心-我的奖品查看详情</p><img class="gold_not_gold" src="public/oh_static/img/shore_jinibi_d.png"><img class="gold_x" src="public/oh_static/img/x.png"><img src="public/oh_static/img/buzu_one.png"></div>'
							    });
							};
							if (json.info=='输') {
								layer.alert('很遗憾，没中。', {
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
						$('.die').html('跌');
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
    // $.post('/cygjs_fr/index.php/hushen300/lishi_list',{open:123},function(data){
    //      // var obj = eval(data);
    //     // alert(data);
    //     var json=JSON.parse(data);
    //     layer.alert(json.a);
    //     // $("#lishi").html(json.a);
    // });
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



// setInterval(function(){  
// 		$.ajax({
// 	            // url: "http://hq.sinajs.cn/?_="+new Date().getTime()+"&list=sh603518",
// 	            url: "http://hq.sinajs.cn/?list=hf_GC",
// 	            method: 'GET',
// 	            dataType: "script",
// 	            scriptCharset: "gb2312",
// 	            cache: true,
// 	            success: function(data, textStatus){
// 	            	var hq = hq_str_hf_GC.split(",");
// 	            	var title,rt_hq = '';
// 	            	if(Number(hq[0])>Number(7)){
// 	            		title = hq[3]+' +'+String(((Number(hq[3])-Number(hq[2]))*100/Number(hq[2])).toFixed(2))+'%';
// 	            		rt_hq = '<span style="color:Red;">'+hq[0]+'&nbsp+'+String((Number(hq[0])-Number(hq[7])).toFixed(2))+'&nbsp'+hq[1]+'%</span>';
// 	            	}else if(Number(hq[0])<Number(7)){
// 	            		title = hq[3]+' '+String(((Number(hq[3])-Number(hq[2]))*100/Number(hq[2])).toFixed(2))+'%';
// 	            		rt_hq = '<span style="color:Lime;">'+hq[0]+'&nbsp-'+String((Number(hq[0])-Number(hq[7])).toFixed(2))+'&nbsp'+hq[1]+'%</span>';
// 	            	}else{
// 	            		title = hq[3]+' '+String(((Number(hq[3])-Number(hq[2]))*100/Number(hq[2])).toFixed(2))+'%';
// 	            		rt_hq = '<span style="color:Black;">'+hq[0]+'&nbsp'+String((Number(hq[0])-Number(hq[7])).toFixed(2))+'&nbsp'+hq[1]+'%</span>';
// 	            	}
// 	            	document.title = title;
// 	                var html_src = '实时行情: '+rt_hq+"|最高: "+hq[3]+" | 最低: "+hq[2]+"|昨收: "+hq[7]+"| 北京时间: "+hq[6]+"";
// 	                var html_src_img = '<img src="http://image.sinajs.cn/newchart/v5/futures/global/min/GC.gif?'+new Date().getTime()+'">';
// 	                if (Number($('#capital').val())<Number(hq[3])) {
// 	                	$(".button_ab dl").css("border","4px solid Red");
// 	                }
// 	                if (Number($('#capital').val())>Number(hq[3])) {
// 	                	$(".button_ab dl").css("border","4px solid Lime");
// 	                };
// 	                if (Number($('#capital').val())==Number(hq[3])) {
// 	                	$(".button_ab dl").css("border","4px solid Black");
// 	                };
// 	                $('#capital').val(hq[3]);
// 	                $("#m-chart-realhq").html(html_src);
// 	                $("#m-chart-img").html(html_src_img);


// 	                // $.post('/cygjs_fr/index.php/huangjin/data_add',{price:hq[3]},function(data){
// 	                //     // alert(data);
// 	                // });
// 	        	} 
// 	    });
// 	},3000);

setInterval(function(){
    $.post('/cygjs_fr/index.php/huangjin/price',{symbol:'CFIFZ5'},function(data){
    // alert(data);
    var json=JSON.parse(data);  
    	if (Number($('#capital').val())<Number(json.price)) {
	    	$(".button_ab dl").css("border","4px solid Red");
	    }
	    if (Number($('#capital').val())>Number(json.price)) {
	    	$(".button_ab dl").css("border","4px solid Lime");
	    };
	    if (Number($('#capital').val())==Number(json.price)) {
	    	$(".button_ab dl").css("border","4px solid Black");
	    };
    $('#capital').val(json.price);

    });
},3000);