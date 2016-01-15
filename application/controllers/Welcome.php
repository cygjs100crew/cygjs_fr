<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	//判断cookie中是否有username,没有就是游客,看看游客有多少流量
	function index(){
		$this->load->database();
		$this->load->helper('cookie');
		
		if(!get_cookie('id')){//手动生成唯一的id，来唯一记录该游客
			$uid=$this->uuid();
			set_cookie('id',$uid);
		}
				
		$username=get_cookie('username')?get_cookie('username'):'游客';
		$flow=get_cookie('flow')?get_cookie('flow'):'0M';
		
		$data['username']=$username;
		$data['flow']=$flow;
		$this->load->view('index',$data);//前端在某个地方输出$username,$flow；
	}
	//这里设置游客有多少流量，此时用户可能没有注册;玩了游戏的游客才会被记录到游客表中
	function setFlow(){
		$this->load->database();
		//存入cookie中
		$this->load->helper('cookie');
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
		$username=$this->session->userdata['username'];
		if($username){//如果用户已经注册，则还要存入用户表
			$this->db->where('username',$username)->update('userinfo',array('flow'=>'100M'));
		}
		echo 'success';
	}
	
	//用户注册的地方，假设用户表中有三个字段，用户名，密码，流量
	function register(){
		$this->load->database();
		$data=$this->input->post();
		$userinfo['name']=$data['username'];
		//将用户名到session中
		$this->session->set_userdata(array('username'=>$data['username']));
		$userinfo['password']=md5($data['password']);
		//取出该用户session中的流量数据，与上述用户名和密码一起存储在userinfo数据表中
		$userinfo['flow']=$this->session->userdata['flow'];
		
		$this->db->insert('userinfo',$userinfo);	
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
	
}
