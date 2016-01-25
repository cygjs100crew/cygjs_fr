<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class MY_Controller extends CI_Controller {
	public function __construct() {
		parent::__construct ();
		$this->load->helper ( 'cookie' );
		$this->load->library ( 'session' );
	    $this->set_customerId();//在所有controller前调用
	}
	
	// 设置游客Id,在所有控制器调用前执行，这样就保证了无论用户从网站哪里进入，都可以第一时间分配一个唯一的sessionid
	public function set_customerId() {
		if (! get_cookie ( 'customerId' )) { // 手动生成唯一的id，来唯一记录该游客
			$data=array(
				'create_time'=>date('Y-m-d H:i:s')
			);
			$this->db->insert('customer',$data);
			$customerId=$this->db->insert_id();
			set_cookie('customerId',$customerId,0);
		}
		
	}

}
