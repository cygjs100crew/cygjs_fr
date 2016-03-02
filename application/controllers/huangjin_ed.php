<?php
//error_reporting(0);
class Huangjin_ed extends MY_Controller{
    public function __construct(){
        parent::__construct();                
    }
    
    
  //判断cookie中是否有username,没有就是游客,看看游客有多少流量
    function index(){
        $this->load->database();		
        $username=get_cookie('username')?get_cookie('username'):'';
        $data['username']=$username;
        $data['num']=$this->ying_num();
        $data['uid']=$this->is_uid();
        $data['flow']=$this->user_play();
        $this->load->view('huangjin_ed.html',$data);//前端在某个地方输出$username      
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
        $ret=$this->db->insert('customer',$userinfo);
		if($ret){
			echo json_encode(array('success'=>true,'info'=>'注册成功'));
			set_cookie('username',$data['username'],0);
		}else{
			echo json_encode(array('success'=>false,'info'=>'注册失败'));
		}
    }
    // 检验用户名和手机号是否重复注册
	public function check(){
		$data=$this->input->post();
		
		if(isset($data['username'])){
			$ret=$this->db->get_where('customer',array('username'=>$data['username']))->result_array();
			$field='用户名';
		}elseif(isset($data['phone'])){
			$ret=$this->db->get_where('customer',array('phone'=>$data['phone']))->result_array();
			$field='手机号';
		}
		
		if(count($ret)>0){
			echo json_encode(array('success'=>false,'info'=>$field.'已注册，请重新输入'));
			return;
		}else{
			echo json_encode(array('success'=>true,'info'=>$field.'正常'));
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
       if($sms_code !=$data['code']){
           echo json_encode(array('success'=>false,'info'=>'短信验证码不对,请重新输入'));
           return;
       }
	   if(count($ret)>0){
		   set_cookie('username',$username,0);//存入cookie
		   echo json_encode(array('success'=>true,'info'=>'登陆成功'));
	   }else{
		   echo json_encode(array('success'=>false,'info'=>'手机号或者密码不对'));
	   }   
	}
   
	//调用短信接口
	public function send_sms(){
		$this->load->model('phone_model','phone');
		$phone=$this->input->post('phone');
		//$phone='15074716900';
		//$MessageContent='手机测试';
		//生成验证码
		$code = rand(1000,9999);
		$this->session->set_userdata('sms_code',$code);//动态生成的短信验证码存入session中，后面注册验证时要用
		//短信内容
		//$date=date('Y年m月d日',time());
		$MessageContent ='您本次验证码为'.$code.'，如需退订回复TD。';
		$data = array(
		    'phone' => $phone,
		    'MessageContent' => $MessageContent
		);
		//var_dump($data);
		$ret=$this->phone->send($data);
		//echo $ret;
 		if(preg_match('/^result=0.*/i',$ret)){
 			echo json_encode(array('success'=>true,'info'=>'发送成功'));
 			return;
 		}else{
 			echo json_encode(array('success'=>false,'info'=>$ret));
 		}
	}
	//统计总流量
	public function stat_total_flow(){
		$customerId=get_cookie('customerId');
		$share_flow=$this->db->query('select sum(flow) as sum from share where customer_id='.$customerId)->row()->sum;
		$game_flow=$this->db->query('select sum(flow) as sum from play where customer_id='.$customerId)->row()->sum;
		echo $share_flow+$game_flow;
		
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
        $data = $this->db->limit(1)->order_by("id","desc")->get_where('investor_detail',array('symbol'=>"XAU",'investor_uid'=>$uid))->result_array(); // 查询历史交易

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
        $list=$this->db->get_where('recentquotation',array('time >'=>'2016-01-25 '.date('H:i:s',strtotime('-5 minutes')),'time <'=>'2016-01-25 '.date('H:i:s',strtotime('-10 seconds')),'symbol'=>$symbol))->result_array(); // 查询图表数据
        if (count($list)<1) {                       
            $result['st'] =0; //没有数据则提示
            echo json_encode($result);
            exit();
        }
        foreach($list as $k=>$v){
            $Kdata[$k] =$v['price'];
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
        $result['flow']=$this->user_play();
        $result['num']=$this->ying_num();
        echo json_encode($result);    
    }
    function tt(){
        $data['num']=$this->is_user_num(1201,'213','赢');
        var_dump($data['num']);
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