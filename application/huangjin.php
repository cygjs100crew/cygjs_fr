<?php
//error_reporting(0);
class huangjin extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->helper('cookie');
                
    }
//判断cookie中是否有username,没有就是游客,看看游客有多少流量
	function index(){
		$this->load->database();
		
		
		if(!get_cookie('id')){//手动生成唯一的id，来唯一记录该游客
			$uid=$this->uuid();
			set_cookie('id',$uid,0);
			//echo $uid;
		}
				
		$username=get_cookie('username')?get_cookie('username'):'游客';
		$flow=get_cookie('flow')?get_cookie('flow'):'0M';
		
		$data['username']=$username;
		$data['flow']=$flow;
		$this->load->view('huangjin.html',$data);//前端在某个地方输出$username,$flow；
	}
	//这里设置游客有多少流量，此时用户可能没有注册;玩了游戏的游客才会被记录到游客表中
	function setFlow(){
		$this->load->database();
		//存入cookie中
		
		$yk_id=get_cookie('id');//获取游客id
		set_cookie('flow','100M');
		
		$count=$this->db->where('id',$yk_id)->from('user_session')->count_all_results();//插入之前先查查游客表该游客是否被记录了
		
		$session_data=array(
			'id'=>$yk_id,
			'flow'=>'100M'
		);
		if($count>0){//游客已经存入表中，只是更新
			unset($session_data['id']);
			$this->db->where('id',$yk_id)->update('user_session',$session_data);
		}else{
			$this->db->insert('user_session',$session_data);
		}
		if(get_cookie('username')){//如果用户已经注册，则还要存入用户表
			$this->db->where('username',$username)->update('user_info',array('flow'=>'100M'));
		}
		echo 'success';
	}
	
	//用户注册的地方，假设用户表中有这几个字段，用户名，密码，邮箱,手机号,验证码
	function register(){
		$this->load->database();
		$data=$this->input->post();
		$userinfo['name']=$data['username'];
		$userinfo['email']=$data['email'];
		$userinfo['phone']=$data['phone'];
		$userinfo['code']=$data['code'];
		//将用户名到cookie中,千万不要将密码写入cookie中，这样容易导致信息泄露
		if(!get_cookie('username') || get_cookie('username')!==$data['username']){//cookie中不存在用户名，或者存在的用户名与上传的用户名不同，就新增或者更新用户名
			set_cookie('username',$data['username'],0);
		}
		$userinfo['password']=md5($data['password']);
		//取出该用户cookie中的流量数据，如果cookie中没有，就设置为0M
		//$userinfo['flow']=get_cookie('flow')?get_cookie('flow'):'0M';
		
		$this->db->insert('user_info',$userinfo);	
		
	}	
	
	//生成唯一id
 function uuid($prefix = '')

     {

		 $chars = md5(uniqid(mt_rand(), true));

		 $uuid     = substr($chars,0,8) . '-';

		 $uuid .= substr($chars,8,4) . '-';

		 $uuid .= substr($chars,12,4) . '-';

		 $uuid .= substr($chars,16,4) . '-';

		 $uuid .= substr($chars,20,12);

		 return $prefix . $uuid;

     }
     //图片验证
    public function img(){
        //载入验证码辅助函数
        $this->load->helper('captcha');
        $speed = 'sfljlwjqrljlfafasdfasldfj1231443534507698';
        $word="";
        for($i=0;$i<4;$i++){
            $word .=$speed[mt_rand(0,strlen($speed)-1)];
        }
        //配置项
        $vals=array(
            'word'=>'$word',
            'img_path'=>'./captcha/',
            'img_url'=>base_url().'captcha/',
            'img_width'=> 80,
			'img_height'=>25,
			'expiration'=>60
        );
        //创建验证码
       $cap = create_captcha($vals);
	   $_SESSION['code'] = $cap['word'];
	   echo $cap['img'];
     /*if(!isset($_SESSION)){
          	session_start();
           }*/
          
     
          /*$data['captcha'] = $cap['image'];
           
         $this->load->view('huangjin.html',$data);*/
        
    }
	
}