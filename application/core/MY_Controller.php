<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class MY_Controller extends CI_Controller {
	public function __construct() {
		parent::__construct ();
		$this->load->helper ( 'cookie' );
		$this->load->library ( 'session' );
		$this->set_sessionid();//在所有controller前调用
	}
	
	// 设置游客Id,在所有控制器调用前执行，这样就保证了无论用户从网站哪里进入，都可以第一时间分配一个唯一的sessionid
	public function set_sessionid() {
		if (! get_cookie ( 'sessionid' )) { // 手动生成唯一的id，来唯一记录该游客
			
			$uid = $this->uuid ();
			set_cookie ( 'sessionid', $uid, 0 );
			
			echo $uid;
		}
		echo '钩子';
	}
	
	// 生成唯一id
	function uuid($prefix = '') 

	{
		$chars = md5 ( uniqid ( mt_rand (), true ) );
		
		$uuid = substr ( $chars, 0, 8 ) . '-';
		
		$uuid .= substr ( $chars, 8, 4 ) . '-';
		
		$uuid .= substr ( $chars, 12, 4 ) . '-';
		
		$uuid .= substr ( $chars, 16, 4 ) . '-';
		
		$uuid .= substr ( $chars, 20, 12 );
		
		return $prefix . $uuid;
	}
}
