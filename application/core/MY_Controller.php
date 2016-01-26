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
			$ret=$this->db->insert('customer',$data);
			var_dump($ret);
			$customerId=$this->db->insert_id();
			set_cookie('customerId',$customerId,0);
		}
	}
    /**
	 * 验证投资结果
	 * @param string $uid     用户ID
	 * @param string $symbol  投资类型标识码
	 * @param string $capital 投资金额
	 * @return integer 0-输，大于0-赢
	 * @author ohyeah
	 */
    public function shuying($uid=0,$symbol='',$capital=0){
				$data=$this->db->get_where('investor_detail',array('result'=>""))->result_array();                                        // 读取未开奖的条目
				for ($i=0; $i < count($data); $i++) {                                                                                     // 逐条遍历
				$list=$this->db->get_where('recentquotation',array('time >'=>date("Y-m-d H:i:s",$data[$i]['and_time']),'symbol'=>$data[$i]['symbol']))->result_array(); // 匹配离开奖时间最近一条结果
				if (count($list)>0){                                                                                                      // 有数据则进入
				if (intval($data[$i]['invest_type'])==1) {                                                                                // 判断涨
				    if ($data[$i]['capital']>$list[0]['price']) {
				        $a='赢';
				    }
				    else if ($data[$i]['capital']<$list[0]['price']) {
				        $a='输';
				    }
				}
				if (intval($data[$i]['invest_type'])==0) {                    // 判断跌
				    if ($data[$i]['capital']<$list[0]['price']) {
				        $a='赢';
				    }
				    else if ($data[$i]['capital']>$list[0]['price']) {
				        $a='输';
				    }
				}
				$condition['id'] =$data[$i]['id'];                            // 更新对象id
				$map['result'] =$a;                                           // 结果赋值
				$map['currented'] =$list[0]['price'];                         // 比较值
				$this->db->where($condition)->update("investor_detail",$map); // 执行更新语句
            }
        }
    }
    /**
	 * 获取用户ID
	 * @return integer 0-失败，属性信息
	 * @author ohyeah
	 */
	public function is_uid(){
	    $user = get_cookie('id'); // 获取cookie值
	    if (empty($user)) {       // 判断是否为空
	    return 0;                 // true返回空0
	    } else {
	    return $user;             // false返回属性信息
	    }
	}
}
