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
	$.post('/cygjs_fr/index.php/hushen300/investor_detail_add',{capital:$('#capital').val(),invest_type:1,symbol:$('#symbol').val()},function(data){
	    var json=JSON.parse(data);
	    layer.alert('看涨。买入价：'+$('#capital').val()+'。中奖请查看历史记录。', {
	    icon: 1,
	    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
		});
	    /*60秒倒计时开始*/
		var intDiff = parseInt(59); //120秒倒计时总秒数量
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
    $.post('/cygjs_fr/index.php/hushen300/investor_detail_add',{capital:$('#capital').val(),invest_type:0,symbol:$('#symbol').val()},function(data){
        var json=JSON.parse(data);
        layer.alert('看跌。买入价：'+$('#capital').val()+'。中奖请查看历史记录。', {
	    icon: 1,
	    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
		});
        /*60秒倒计时开始*/
		var intDiff = parseInt(59); //120秒倒计时总秒数量
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

setInterval(function(){  
    $.ajax({
        url: "http://hq.sinajs.cn/?list=s_sz399300",
        method: 'GET',
        dataType: "script",
        scriptCharset: "gb2312",
        cache: true,
        success: function(data, textStatus){
             var hq = hq_str_s_sz399300.split(",");
            var title,rt_hq = '';
            if(Number(hq[2])>Number(0)){
                title = hq[3]+' +'+String(((Number(hq[3])-Number(hq[2]))*100/Number(hq[2])).toFixed(2))+'%';
                rt_hq = '<span style="color:Red;">'+hq[1]+'&nbsp;'+hq[2]+'&nbsp;'+hq[3]+'%</span>';
            }else if(Number(hq[2])<Number(0)){
                title = hq[3]+' '+String(((Number(hq[3])-Number(hq[2]))*100/Number(hq[2])).toFixed(2))+'%';
                rt_hq = '<span style="color:Lime;">'+hq[1]+'&nbsp;'+hq[2]+'&nbsp;'+hq[3]+'%</span>';
            }else{
                title = hq[3]+' '+String(((Number(hq[3])-Number(hq[2]))*100/Number(hq[2])).toFixed(2))+'%';
               rt_hq = '<span style="color:Black;">'+hq[1]+'&nbsp;'+hq[2]+'&nbsp;'+hq[3]+'%</span>';
            }
            document.title = title;
            
                var html_src = '实时行情: '+rt_hq+"|量: "+hq[4]+"手 | 额: "+hq[5]+"万元";
                $("#m-chart-realhq").html(html_src);
                $('#capital').val(hq[1]);
                // $.post('/cygjs_fr/index.php/hushen300/data_add',{price:hq[1]},function(data){
                //     // console.log(data.data_date);
                //     var json=JSON.parse(data);
                //     $("#sxin").html(json.info);
                // });
            } 
        });
},5000);