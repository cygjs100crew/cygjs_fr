<?php
class hushen300 extends MY_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->helper('cookie');
        $this->load->library('session');
    }
  

//判断cookie中是否有username,没有就是游客,看看游客有多少流量
    function index(){
        $this->load->database();		
        $username=get_cookie('username')?get_cookie('username'):'';
        $data['username']=$username;
        $this->load->view('hushen300.html',$data);//前端在某个地方输出$username      
    }
    //这里设置游客有多少流量，此时用户可能没有注册;玩了游戏的游客才会被记录到游客表中
  /*  function setFlow(){
        $this->load->database();
        //存入cookie中

        $yk_id=get_cookie('sessionid');//获取游客id
        set_cookie('flow',$flow,0);

        $count=$this->db->where('sessionid',$yk_id)->from('user_session')->count_all_results();//插入之前先查查游客表该游客是否被记录了

        $session_data=array(
            'sessionid'=>$yk_id   
        );
        if($count>0){//游客已经存入表中，只是更新
            unset($session_data['sessionid']);
            $this->db->where('sessionid',$yk_id)->update('user_session',$session_data);
        }else{
            $this->db->insert('user_session',$session_data);
        }
//         if(get_cookie('username')){//如果用户已经注册，则还要存入用户表
//             $this->db->where('username',$username)->update('user_info',array('flow'=>'100M'));
//         }
        echo 'success';
    }*/
  
    
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
        $ret=$this->db->insert('user_info',$userinfo);
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
	   if(count($ret)>0){
		   $customerId=$ret['id'];//登陆后从数据库里获取的id
		   $new_customerId=get_cookie('customerId');//cookie里的id,这是客户进来就有的id,可能跟数据库里的id不一致
		   if($customerId!=$new_customerId){//登陆成功后，把用户原来作为游客玩游戏和分享的记录更新为用户的名下
			   set_cookie('customerId',$customerId,0);//覆盖原来的游客id
			   set_cookie('username',$data['username'],0);//用户名存入cookie
			   $this->db->where('customer_id',$new_customerId)->update('share',array('customer_id'=>$customerId));
			   $this->db->where('customer_id',$new_customerId)->update('game',array('customer_id'=>$customerId));
		   }  
		   echo json_encode(array('success'=>true,'info'=>'登陆成功'));
	   }else{
		   echo json_encode(array('success'=>false,'info'=>'用户名或者密码不对'));
	   }      
	}
	
	public  function login_phone(){
       $data=$this->input->post();
       $ret=$this->db->get_where('user_info',array('username'=>$data['username'],'password'=>md5($data['password'])))->result_array();
	   if(count($ret)>0){
		   set_cookie('username',$data['username'],0);//存入cookie
		   echo json_encode(array('success'=>true,'info'=>'登陆成功'));
	   }else{
		   echo json_encode(array('success'=>false,'info'=>'用户名或者密码不对'));
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
		var_dump($data);
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
            'phone' => '158****365',
            'MessageContent' => '您本次验证码为12345678如需退订回复TD。',
        );
        echo $this->phone->send($data);
    }
    /* 数据添加 @ohyeah */
    public function data_add(){
        $data = array(
                'current' =>$_POST['current'],
                'time'      => time(),
            );
            $h = intval(date("Hi")); 
            if (($h < 1500 && $h > 930)&&($h < 1130 && $h > 1300)&&((date('w') != 6)||(date('w') != 0))) {
                // $this->db->insert('data_source',$data);
            } else {
                echo json_encode(array('success'=>false,'info'=>'现在处于休市状态！'));
            }
    }
    /* 会员投资（下单） @ohyeah */
    public function investor_detail_add(){
        $data = array(
                'start_time'   => time(),
                'and_time'     => strtotime("+60 seconds"),
                'capital'      => $_POST['capital'],
                'duration'     => 60,
                'add_ip'       => $_SERVER["REMOTE_ADDR"],
                'invest_type'  => $_POST['invest_type'],
                'status'       => 1,
                'investor_uid' => get_cookie('id'),
                'current'      => 1,
                'symbol'       => 'CFIFZ5'
            );
        if($this->db->insert('investor_detail',$data)){
            echo json_encode($data);
        }
    }
    /* 查询历史交易 @ohyeah */
    public function lishi_list(){
        $data = $this->db->get("investor_detail")->result_array();
        $row = '';
        foreach ($data as $key => $val)
        {
            $row .='编号：'.$val['id'].'金额:'.$val['capital'].'开始:'.date("Y-m-d H:i:s",$val['start_time']).'结束:'.date("Y-m-d H:i:s",$val['and_time']).'结果：'.$val['result'];
        }
        $row .= '';
        echo json_encode(array('result' => true,'a' =>$row));
    }
    /* 沪深300走势线iframe显示页面 @ohyeah */
    function hushen_link(){
        $mun=$this->db->where('symbol',"CFIFZ5")->from('recentquotation')->count_all_results();
        $list=$this->db->limit($mun,5000)->order_by("id","asc")->get_where('recentquotation',array('symbol'=>"CFIFZ5"))->result_array();
        foreach($list as $k=>$v){
            $Kdata[$k] =$v['price'];
            $data_date[$k] ='"'.$v['time'].'"';
            $result['data_date'] = implode(',', $data_date);
            $result['ipdata'] = implode(',', $Kdata);
        }
        $this->load->view('hushen_link.html',$result);//前端在某个地方输出$username,$flow；
    }
    /* 交易历史iframe显示页面 @ohyeah */
    function lishi_html_list(){
        $data['lishi'] = $this->db->limit(20)->order_by("id","desc")->get_where('investor_detail',array('symbol'=>"CFIFZ5"))->result_array();


        // $this->load->library('pagination');
        // $config['base_url'] = 'http://example.com/index.php/test/page/';
        // $config['total_rows'] = 200;
        // $config['per_page'] = 20;
        // $this->pagination->initialize($config);//序列化
        // echo $this->pagination->create_links();//生成分页导航

        $result=$this->shuying();

        $this->load->view('lishi_html_list.html',$data);//前端在某个地方输出$username,$flow；
         
    }
    function tt(){
        $uid = $this->shuying();
        echo $uid;

    }
    // /* 验证输赢 @ohyeah */
    // function shuying(){
    //     $data=$this->db->get_where('investor_detail',array('result'=>""))->result_array();
    //     for ($i=0; $i < count($data); $i++) { 

    //         $list=$this->db->get_where('data_source',array('time >'=>$data[$i]['and_time']))->result_array();

    //         if (count($list)>0){

				// if (intval($data[$i]['current'])==1) {
				//     if ($data[$i]['current']>$list[0]['current']) {
				//         $a='赢';
				//     }
				//     else if ($data[$i]['current']<$list[0]['current']) {
				//         $a='输';
				//     }
				// }
				// if (intval($data[$i]['current'])==0) {
				//     if ($data[$i]['current']<$list[0]['current']) {
				//         $a='赢';
				//     }
				//     else if ($data[$i]['current']>$list[0]['current']) {
				//         $a='输';
				//     }
				// }
    //             $condition['id'] =$data[$i]['id'];//更新id
    //             $map['result'] =$a; //更新内容
    //             $this->db->where($condition)->update("investor_detail",$map);
    //         }
    //     }
    // }
}
