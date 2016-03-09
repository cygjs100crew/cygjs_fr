<?php
//error_reporting(0);
class Huangjin_ed extends MY_Controller{
    public function __construct(){
        parent::__construct();
        $wx_param=array(
            'appId'=>'wxb789734a2a886553',
            'appSecret'=>'4f65d464420c998e7754c4cd1487a262'
        );
        $this->load->library('jssdk',$wx_param);
    }
    
//判断cookie中是否有username,没有就是游客,看看游客有多少流量
 function index(){
        $this->load->database();		
        $username=get_cookie('username')?get_cookie('username'):'请登入';
        
        

        $data['username']=$username; 
        $data['num']=$this->ying_num();
        $data['uid']=$this->is_uid().'号会员';
        $userplay=$this->db->get_where('play',array('customer_id'=>$data['uid']))->result_array();
        $data['flow']=count($userplay)>1?$userplay[0]['flow']:0;
        $signPackage = $this->jssdk->GetSignPackage();
        $data['signPackage']= $signPackage;
        $this->load->view('huangjin.html',$data);//前端在某个地方输出$username  
       
        
        
    }
 
    
    //用户注册的地方，假设用户表中有这几个字段，用户名，密码，确认密码，手机号,验证码
    function register(){
        $this->load->database();
        $data=$this->input->post();
        $userinfo['name']=$data['username'];
		$userinfo['passwd']=md5($data['password']);
        $userinfo['phone']=$data['phone'];
		$userinfo['register_time']=date('Y-m-d H:i:s');

		$sms_code=$this->session->userdata('sms_code');
		if($sms_code!=$data['checkCode']){
			echo json_encode(array('success'=>false,'info'=>'短信验证码不对,请重新输入'));
			return;
		}
		//防止重复注册
		$check_arr=array(
			'username'=>$data['username'],
			'phone'=>$data['phone']
		);
		
		$ret=$this->_check($check_arr);
		if(!$ret){
			echo json_encode(array('success'=>false,'info'=>'重复注册'));
			return;
		}
       // $ret=$this->db->insert('customer',$userinfo);
        //此处应该利用原有的id
         $id=get_cookie('customerId');
      
		$ret=$this->db->where('id',$id)->update('customer',$userinfo);
		if($ret){
			echo json_encode(array('success'=>true,'info'=>'注册成功'));
			set_cookie('username',$data['username'],0);
		}else{
			echo json_encode(array('success'=>false,'info'=>'注册失败'));
		}
    }
    // 检验用户名和手机号是否重复注册
	private function _check($data=array()){		
		
		if(isset($data['username'])){
			$ret=$this->db->get_where('customer',array('name'=>$data['username']))->result_array();
		}
		if(isset($data['phone'])){
			$ret=$this->db->get_where('customer',array('phone'=>$data['phone']))->result_array();
		}
		
		if(count($ret)>0){
			return false;
		}
		return true;
		
		
	}
	
	public function check(){
		$data=$this->input->post();
		$ret=$this->_check($data);
		if(!$ret){
			echo json_encode(array('success'=>false,'info'=>'已注册，请重新输入'));
			return;
		}else{
			echo json_encode(array('success'=>true,'info'=>'正常'));
		}
	}
    // 登入
   public  function login_name(){
	   if(get_cookie('username')){//如果登陆了，就不用再登陆了
		   die('你已经登陆！');
	   }
       $data=$this->input->post();
       $ret=$this->db->get_where('customer',array('name'=>$data['username'],'passwd'=>md5($data['password'])))->result_array();
      // var_dump($ret);
	   if(count($ret)>0){
		   $customerId=$ret[0]['id'];//登陆后从数据库里获取的id
		  // var_dump($customerId);//3
		    
		   $new_customerId=get_cookie('customerId');//cookie里的id,这是客户进来就有的id,可能跟数据库里的id不一致
		  //var_dump( $new_customerId);//16
		   if($customerId!=$new_customerId){//登陆成功后，把用户原来作为游客玩游戏和分享的记录更新为用户的名下
			   set_cookie('customerId',$customerId,0);//覆盖原来的游客id
			   set_cookie('username',$data['username'],0);//用户名存入cookie
			   $this->db->where('customer_id',$new_customerId)->update('share',array('customer_id'=>$customerId));
			   $this->db->where('customer_id',$new_customerId)->update('play',array('customer_id'=>$customerId));
			   //原来游客id名下的flow要转移过来
			   $total_flow=$this->db->select('total_flow')->where('id',$new_customerId)->get('customer')->row()->total_flow;
			   $this->db->query("update customer set total_flow=total_flow+".$total_flow." where id=".$customerId);
			   $this->db->where('id',$new_customerId)->delete('customer');
		   }  
		   echo json_encode(array('success'=>true,'info'=>'登陆成功'));
	   }else{
		   echo json_encode(array('success'=>false,'info'=>'用户名或者密码不对'));
	   }      
	}
	
	public  function login_phone(){
       $data=$this->input->post();
       $sms_code=$this->session->userdata('sms_code');
       $ret=$this->db->get_where('customer',array('phone'=>$data['phone']))->result_array(); 
       $username=$ret[0]['name'];
       $customerId=$ret[0]['id'];
       $new_customerId=get_cookie('customerId');
       if($customerId!=$new_customerId){
           set_cookie('customerId',$customerId,0);
           $this->db->where('customer_id',$new_customerId)->update('share',array('customer_id'=>$customerId));
           $this->db->where('customer_id',$new_customerId)->update('play',array('customer_id'=>$customerId));
		   //原来游客id名下的flow要转移过来
		   $total_flow=$this->db->select('total_flow')->where('id',$new_customerId)->get('customer')->row()->total_flow;
		   $this->db->query("update customer set total_flow=total_flow+".$total_flow." where id=".$customerId);
           $this->db->where('id',$new_customerId)->delete('customer');
       }
       if($sms_code !=$data['checkCode']){
           echo json_encode(array('success'=>false,'info'=>'短信验证码不对,请重新输入'));
           return;
       }
	   if(count($ret)>0 && $data['checkCode']!=''){
		   set_cookie('username',$username,0);//存入cookie
		   echo json_encode(array('success'=>true,'info'=>'登陆成功'));
	   }else{
		   echo json_encode(array('success'=>false,'info'=>'手机号或者验证码不对'));
	   }   
	}
	
	//兑现流量
function cash_flow(){
		$customerId=get_cookie('customerId');
		$total_flow=$this->_stat_total_flow();	
		$result=$this->db->where('customer_id',$customerId)->get('user_flow')->num_rows();//查询兑现时间存不存在
		if($result==0 && $total_flow<30){
			echo json_encode(array('success'=>false,'info'=>'首次兑换流量满30M才能兑换！'));
			return;	
		}
		if($result!=0 && $total_flow<100){
		    echo json_encode(array('success'=>false,'info'=>'流量满100M才能兑换！'));
		    return;
		
 		}
		$row=$this->db->select('phone')->where('id',$customerId)->get('customer')->row_array();
		if(!$row['phone']){
			echo json_encode(array('success'=>false,'info'=>'对不起，你没有注册电话号码！'));
			return;
		}
		$numb=rand(10,99);
		$orderid=date('YmdHis').$numb;	
		if($total_flow>=30 && $total_flow<100){
			$cash_flow=30;
		}else{
			$cash_flow=100;
		}
		//此处调用流量公司提供的接口查询该号码有哪些套餐可用
		$ret1=file_get_contents('http://liuliang.huagaotx.cn/Interface/InfcForEC.aspx?INTECMD=A_CPCX&USERNAME=18805710101&PASSWORD=710101&MOBILE='.$row['phone'].'&CATEGORY=1');
		$ret1_arr=json_decode($ret1,true);//把json数据转换成数组
		if($ret1_arr['Status']=='0000'){
			$tmp=array();
			foreach($ret1_arr['Packages'] as $package){
				if($package['Package']<=$cash_flow){//先把<=要兑换的流量的套餐放入一个数组array(20=>HG012,50=>HG013);键为套餐，值为对应的code
					$tmp[$package['Package']]=$package['Code'];
				}
			}
			$packages_arr=array_keys($tmp);//取出键组成一个数组   
			$cash_flow=max($packages_arr);//取出最大的键值，就是最接近的套餐   
			$product_code=$tmp[$cash_flow];//实际兑换的套餐选择$tmp里最大的一个套餐  
			
			
		}else{
			echo json_encode(array('success'=>false,'info'=>'对不起，没有查到你的号码归属！'));
		}
		//此处调用流量公司提供的接口来下单
		$callback=urlencode('http://test-wx.cygjs100.com/cygjs_fr/index.php/huangjin/callback');
		$url='http://liuliang.huagaotx.cn/Interface/InfcForEC.aspx?INTECMD=A_CPCZ&USERNAME=18805710101&PASSWORD=710101&MOBILE='.$row['phone'].'&ORDERID='.$orderid.'&PRODUCTCODE='.$product_code.'&CTMRETURL='.$callback.'&APIKEY=4866f53d0563496385bc2f67009c9d4f';
		//redirect($url);
		die;
		$ret=file_get_contents($url);
		$ret_arr=json_decode($ret,true);
		
		if($ret_arr['STATUS']==0){ 
 		    $data=array(
 		        'customer_id'=>$customerId,
				'cash_flow'=>$cash_flow,
 		        'cash_time'=>date('Y-m-d H:i:s'),
				'order_id'=>$orderid,
				'trade_status'=>2
 		    );
 		    $this->db->insert('user_flow',$data);
 		    set_cookie('order_id',$orderid,0);
			//兑换成功后一定要在总流量减去兑换了的流量
			$this->db->query('update customer set total_flow=total_flow-'.$cash_flow.' where id='.$customerId);
			//注意，此处最为关键，兑换成功后，要把share和play表里的该用户的所有流量都置0
			//$this->where('customer_id',$customerId)->update('share',array('flow'=>0));
			//$this->where('customer_id',$customerId)->update('play',array('flow'=>0));
			echo json_encode(array('success'=>true,'info'=>'下单成功，请耐心等待！','flow_package'=>$cash_flow));
			return;
		}else{
			echo json_encode(array('success'=>false,'info'=>'下单失败！'));
			return;
		}
		
    }
	
	//充值回调函数
	function callback(){
		$trade_status=$this->input->get_post('TRADESTATUS');//交易状态码
		$order_id=$this->input->get_post('ORDERID');//订单号，必须唯一
		$trade_error=$this->input->get_post('TRADEERROR');
		$package=$this->input->get_post('PACKAGE');//流量包
		if($trade_status==1){//兑现成功
			$this->db->where('order_id',$order_id)->update('user_flow',array('trade_status'=>1));
		}else{
			$this->db->where('order_id',$order_id)->update('user_flow',array('trade_status'=>$trade_status,'trade_error'=>$trade_error));
			//如果兑现失败了，就要把该失败的流量又加到总流量上去
			$customer_id=$this->db->select('customer_id')->where('order_id',$order_id)->get('user_flow')->row()->customer_id;
			$this->db->query('update customer set total_flow=total_flow+'.$package.' where id='.$customer_id);
		}
		echo 'success';
	}
   
	function test_callback(){
	    $url="http://liuliang.huagaotx.cn/Interface/InfcForEC.aspx?INTECMD=A_CPCZ&USERNAME=18805710101&PASSWORD=710101&MOBILE=15074716900&ORDERID=2016030910531202&PRODUCTCODE=HG001&CTMRETURL=http%3A%2F%2Ftest-wx.cygjs100.com%2Fcygjs_fr%2Findex.php%2Fhuangjin%2Fcallback&APIKEY=4866f53d0563496385bc2f67009c9d4f";
        $ret=file_get_contents($url);
	    var_dump($ret);
	}
	//订单情况
	function order_status(){
	    $customer_id=get_cookie('customerId');
	    $orderid=get_cookie('order_id');
	    /*$ret=$this->db->get_where('user_flow',array('customer_id'=>$customer_id,'order_id'=>$orderid))->row_array();//查询有没有这个兑换流量的订单号
	    if($ret && count($ret)>0){
	        $cashflow=$ret['cash_flow'];
	    }else{
	        $cashflow='0';
	    }*/
	    $trade_status=$this->db->select('trade_status')->where(array('customer_id'=>$customer_id,'order_id'=>$orderid))->get('user_flow')->row()->trade_status;
	    if($trade_status==0){
	        echo json_encode(array('success'=>true,'status'=>0));
	        return;
	    }
	    if($trade_status==1){
	        echo json_encode(array('success'=>true,'status'=>1));
	    }
	    if($trade_status==2){
	        echo json_encode(array('success'=>false,'status'=>2));
	    } 
	}
	//调用短信接口
	public function send_sms(){
		//$this->load->model('phone_model','phone');
		$phone=$this->input->post('phone');
		
		//生成验证码
		$code = rand(1000,9999);
		$this->session->set_userdata('sms_code',$code);//动态生成的短信验证码存入session中，后面注册验证时要用
		//短信内容
		//$date=date('Y年m月d日',time());
		//$MessageContent ='您本次验证码为'.$code.'，如需退订回复TD。';
		$MessageContent='尊敬的客户，您的验证码是'.$code.'，请妥善保管，3分钟内有效【金裕黄金】';
		/*$data = array(
		    'phone' => $phone,
		    'MessageContent' => $MessageContent
		);*/
		//var_dump($data);
		//$ret=$this->phone->send($data);
		//$ret=$this->phone->Get($url);
		$url="http://120.24.167.205/msg/HttpSendSM?account=gzjygjs&pswd=GZjygjs05&mobile=".$phone."&msg=".$MessageContent."&needstatus=true&product=";
		$ret=file_get_contents($url);
		//var_dump($ret);
		die;
		//echo $ret;
 		/*if(preg_match('/,0$/i',$ret)){
 			echo json_encode(array('success'=>true,'info'=>'发送成功'));
 			return;
 		}else{
 			echo json_encode(array('success'=>false,'info'=>$ret));
 		}*/
	}
	
	//统计总流量
	private function _stat_total_flow(){
		$customerId=get_cookie('customerId');
		//$share_flow=$this->db->query('select sum(flow) as sum from share where customer_id='.$customerId)->row()->sum;
		//$game_flow=$this->db->query('select sum(flow) as sum from play where customer_id='.$customerId)->row()->sum;
		//return $share_flow+$game_flow;
	   $total_flow=$this->db->select('total_flow')->where('id',$customerId)->get('customer')->row();
		if(isset($total_flow)){
			return $total_flow->total_flow;
		}
		
	}
	//统计总流量
	public function stat_total_flow(){	
		echo $this->_stat_total_flow();
		
	}
	//测试短信接口用
	public function test_sms(){
		$this->load->model('phone_model','phone');
		$data = array(
		    'phone' => '15074716900',
		    'MessageContent' => '您本次验证码为12345678如需退订回复TD。',
		);
		echo $this->phone->send($data);
	}
	/* 新浪数据添加 @ohyeah */
    // public function data_add(){
    //     $data = array(
    //             'price' => $_POST['price'],//最新报价
    //             'time'    =>date("Y-m-d H:i:s",time()),//时间
    //             'symbol' =>'hf_GC',//黄金数据标识
    //     );
    //     $this->db->insert('recentquotation',$data);
    // }
    /* 会员投资（下单） @ohyeah */
    public function investor_detail_add(){
        $data = array(
                'start_time'   => time(),                   // 开始时间
                'and_time'     => strtotime("+60 seconds"), // 结束时间
                'capital'      => $_POST['capital'],        // 买入价
                'duration'     => 60,                       // 间隔时间
                'add_ip'       => $_SERVER["REMOTE_ADDR"],  // 用户IP
                'invest_type'  => $_POST['invest_type'],    // 投资方向，涨或者跌
                'status'       => 1,                        // 状态
                'investor_uid' => $this->is_uid(),          // 用户ID 
                'current'      => 1,
                'symbol'       => $_POST['symbol'],                     // 数据标识
            );
        if($this->db->insert('investor_detail',$data)){     //执行插入语句
            echo json_encode($data);                        //返回属性信息
        }
    }
    /* 黄金走势线iframe显示页面（下单） @ohyeah */
    function huangjin_link_ed(){
        $list=$this->db->get_where('recentquotation',array('time >'=>'2016-01-25 '.date('H:i:s',strtotime('-1 hours')),'time <'=>'2016-01-25 '.date('H:i:s'),'symbol'=>"XAU"))->result_array(); // 查询图表数据
        if (count($list)<1) {                   
        	echo "╮(╯﹏╰)╭暂时没有数据！";     //没有数据则提示
        	exit();
        }
        foreach($list as $k=>$v){
            $Kdata[$k] =$v['price'];
            $data_date[$k] ='"'.$v['time'].'"';
        $result['data_date'] = implode(',', $data_date);                                                        // 拼接报价数据格式
        $result['ipdata'] = implode(',', $Kdata);                                                               // 拼接时间数据格式
        }
        $this->load->view('huangjin_link_ed.html',$result);                                                        // 加载模板
    }
        /* 黄金走势线iframe显示页面（下单） @ohyeah */
    function data_ed(){
        $list=$this->db->get_where('recentquotation',array('time >'=>'2016-01-25 '.date('H:i:s',strtotime('-1 hours')),'time <'=>'2016-01-25 '.date('H:i:s'),'symbol'=>"XAU"))->result_array(); // 查询图表数据
        if (count($list)<1) {                   
            echo "╮(╯﹏╰)╭暂时没有数据！";     //没有数据则提示
            exit();
        }
        foreach($list as $k=>$v){
            $Kdata[$k] =$v['price'];
            $data_date[$k] ='"'.$v['time'].'"';
        $result['data_date'] = implode(',', $data_date);                                                        // 拼接报价数据格式
        $result['ipdata'] = implode(',', $Kdata);                                                               // 拼接时间数据格式
        }
        echo json_encode($result);                                                         // 加载模板
    }
    /* [新浪]黄金走势线iframe显示页面（下单） @ohyeah */
    function huangjin_sinalink(){
        $list=$this->db->get_where('recentquotation',array('time >'=>date('Y-m-d',strtotime('-0 day')),'time <'=>date('Y-m-d',strtotime('+1 day')),'symbol'=>"hf_GC"))->result_array(); // 查询图表数据
        if (count($list)<1) {                   
        	echo "╮(╯﹏╰)╭暂时没有数据！";     //没有数据则提示
        	exit();
        }
        foreach($list as $k=>$v){
            $Kdata[$k] =$v['price'];
            $data_date[$k] ='"'.$v['time'].'"';
        $result['data_date'] = implode(',', $data_date);                                                        // 拼接报价数据格式
        $result['ipdata'] = implode(',', $Kdata);                                                               // 拼接时间数据格式
        }
        $this->load->view('huangjin_sinalink.html',$result);                                                        // 加载模板
    }
    /* 交易历史iframe显示页面 @ohyeah */
    function huangjin_html_list(){
        $data['lishi'] = $this->db->limit(20)->order_by("id","desc")->get_where('investor_detail',array('symbol'=>"XAU"))->result_array(); // 查询历史交易
        $result=$this->shuying_ed();                                                                                                          // 验证结果
        $this->load->view('huangjin_html_list.html',$data);                                                                                // 加载模板
    }
    function huangjin_js_list(){
        $result=$this->shuying_ed(); 
        $uid=$this->is_uid();
        $data = $this->db->select('result')->limit(1)->order_by("id","desc")->get_where('investor_detail',array('symbol'=>"XAU",'investor_uid'=>$uid))->result_array(); // 查询历史交易

        $num=$this->ying_num();
        if ($data[0]['result']=='赢') {
            echo json_encode(array('success'=>true,'info'=>'赢','num'=>$num)); 
        }else if($data[0]['result']=='输'){
            echo json_encode(array('success'=>true,'info'=>'输','num'=>$num));
        }else if($data[0]['result']=='平'){
            echo json_encode(array('success'=>true,'info'=>'平','num'=>$num));
        }else{
            echo json_encode(array('success'=>false,'info'=>'未开奖'));
        }                                                                 
    }
    function price(){
        $result=$this->shuying_ed(); 
        $symbol=$_POST['symbol'];
        $result = $this->db->limit(1)->order_by("id","desc")->get_where('recentquotation',array('time <'=>'2016-01-25 '.date('H:i:s'),'symbol'=>$symbol))->result_array(); // 查询最近一条报价记录
        $data['price'] = $result[0]['price'];
        $data['time'] = $result[0]['time'];
        $data['num']=$this->ying_num();
        echo json_encode($data); 
    }
    function e(){ 
        $this->load->view('e.html');  
    }
    function e_data(){
        $symbol=$_POST['symbol'];
        $list=$this->db->select('price,time')->get_where('recentquotation',array('time >'=>'2016-01-25 '.date('H:i:s',strtotime('-5 minutes')),'time <'=>'2016-01-25 '.date('H:i:s',strtotime('-10 seconds')),'symbol'=>$symbol))->result_array(); // 查询图表数据
        if (count($list)<1) {                       
            $result['st'] =0; //没有数据则提示
            echo json_encode($result);
            exit();
        }
        foreach($list as $k=>$v){
            $Kdata[$k] =round($v['price'],2);
            $data_date[$k] =$v['time'];
        // $result['data_date'] = implode(',', $data_date);                                                        // 拼接报价数据格式
        // $result['ipdata'] = implode(',', $Kdata);       
        $result['data_date'] = $data_date;                                                        // 拼接报价数据格式
        $result['ipdata'] = $Kdata; 
        $result['price'] = $v['price'];
                                                                   // 拼接时间数据格式
        }

        // $result = $_POST['symbol'];
        $result['st'] =1;
        $result['flow']=$this->_stat_total_flow();
        $result['num']=$this->ying_num();
        echo json_encode($result);    
    }
    function tt(){
        // $data['num']=$this->is_user_num(1201,'213','赢');
        // $this->db->select('price');  
        // $this->db->from('recentquotation');  
        // $this->db->where('id', 1);  
          
        // $list= $this->db->get(); 
        $list=$this->db->select('price')->get_where('recentquotation',array('time >'=>'2016-01-25 '.date('H:i:s',strtotime('-5 minutes')),'time <'=>'2016-01-25 '.date('H:i:s',strtotime('-10 seconds')),'symbol'=>'XAU'))->result_array(); // 查询图表数据
        var_dump($list);
    }
    function t1(){
            $condition['uid'] =888;                                           // 更新对象id
            $updata['uid'] =888; 
            $updata['num'] =1;                                              // 结果赋值
            $updata['symbol'] ='123';
            $updata['state'] =1;     
                                                    // 比较值
            $this->db->where($condition)->update("investor_user_num",$updata); // 执行更新语
    }
}