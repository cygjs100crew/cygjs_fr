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
				$data=$this->db->select('and_time,symbol,invest_type,capital,investor_uid,id')->get_where('investor_detail',array('result'=>""))->result_array();                                        // 读取未开奖的条目
				for ($i=0; $i < count($data); $i++) {                                                                                     // 逐条遍历
				$list=$this->db->select('price')->get_where('recentquotation',array('time >'=>date("Y-m-d H:i:s",$data[$i]['and_time']),'symbol'=>$data[$i]['symbol']))->result_array(); // 匹配离开奖时间最近一条结果
				
				if (count($list)>0){                                                                                                      // 有数据则进入
				if (intval($data[$i]['invest_type'])==1) {                                                                                // 判断涨
				    if ($data[$i]['capital']>$list[0]['price']) {
				        $shuying_result='输';
				    }
				    else if ($data[$i]['capital']<$list[0]['price']) {
				        $shuying_result='赢';
				    }
				    else{
				    	$shuying_result='平';
				    }
				}
				if (intval($data[$i]['invest_type'])==0) {                    // 判断跌
				    if ($data[$i]['capital']<$list[0]['price']) {
				        $shuying_result='输';
				    }
				    else if ($data[$i]['capital']>$list[0]['price']) {
				        $shuying_result='赢';
				    }
				    else{
				    	$shuying_result='平';
				    }
				}
				$num_result=$this->is_user_num($data[$i]['investor_uid'],$data[$i]['symbol'],$shuying_result); // 执行验证规则
				$condition['id'] =$data[$i]['id'];                                                   // 更新对象id
				$map['result'] =$shuying_result;                                                     // 结果赋值
				$map['currented'] =$list[0]['price'];                                                // 比较值
				$this->db->where($condition)->update("investor_detail",$map);                        // 执行更新语句
            }
        }
    }
    /**
	 * 验证投资结果(历史)
	 * @param string $uid     用户ID
	 * @param string $symbol  投资类型标识码
	 * @param string $capital 投资金额
	 * @return integer 0-输，大于0-赢
	 * @author ohyeah
	 */
    public function shuying_ed($uid=0,$symbol='',$capital=0){
				$data=$this->db->select('and_time,symbol,invest_type,capital,investor_uid,id')->get_where('investor_detail',array('result'=>""))->result_array();                                 // 读取未开奖的条目
				for ($i=0; $i < count($data); $i++) {                                                                                     // 逐条遍历
				$list=$this->db->select('price')->get_where('recentquotation',array('time >'=>'2016-01-25 '.date('H:i:s',$data[$i]['and_time']),'symbol'=>$data[$i]['symbol']))->result_array();
				// $list[0]['price']=$list[0]['price']+200;
				 // 匹配离开奖时间最近一条结果
				if (count($list)>0){                                                                                                      // 有数据则进入
				if (intval($data[$i]['invest_type'])==1) {                                                                                // 判断涨
				    if ($data[$i]['capital']>$list[0]['price']) {
				        $shuying_result='输';
				    }
				    else if ($data[$i]['capital']<$list[0]['price']) {
				        $shuying_result='赢';
				    }
				    else{
				    	$shuying_result='平';
				    }
				}
				if (intval($data[$i]['invest_type'])==0) {                    // 判断跌
				    if ($data[$i]['capital']<$list[0]['price']) {
				        $shuying_result='输';
				    }
				    else if ($data[$i]['capital']>$list[0]['price']) {
				        $shuying_result='赢';
				    }
				    else{
				    	$shuying_result='平';
				    }
				}
				$num_result=$this->is_user_num($data[$i]['investor_uid'],$data[$i]['symbol'],$shuying_result); // 执行验证规则
				$condition['id'] =$data[$i]['id'];                                                   // 更新对象id
				$map['result'] =$shuying_result;                                                     // 结果赋值
				$map['currented'] =$list[0]['price'];                                                // 比较值
				$this->db->where($condition)->update("investor_detail",$map);                        // 执行更新语句
            }
        }
    }
    /**
	 * 获取用户ID
	 * @return integer 0-失败，属性信息
	 * @author ohyeah
	 */
	public function is_uid(){
	    $user = get_cookie('customerId'); // 获取cookie值
	    if (empty($user)) {               // 判断是否为空
	    return 0;                         // true返回空0
	    } else {
	    return $user;                     // false返回属性信息
	    }
	}
	/**
	 * 判断股市开市时间
	 * @return integer 0-休市，1-开市
	 * @author ohyeah
	 */
	public function is_opentime(){
        $h = intval(date("Hi"));                                                                        // 获取当前时间值
        if (( $h > 930 && $h < 1130)||($h > 1300 && $h < 1500)&&((date('w') != 6)||(date('w') != 0))) { // 判断股市休市时间范围
        return 1; 
        } else {
        return 0;                                                                                       // 返回属性信息
        }
	}
	/**
	 * 用户投资获利规则
	 * @param string $uid     用户ID
	 * @param string $symbol  投资类型标识码
	 * @param string $shuying_result 投资结果
	 * @return integer
	 * @author ohyeah
	 */
	public function is_user_num($uid=0,$symbol='',$shuying_result=''){
        $numdata = $this->db->get_where('investor_user_num',array('uid'=>$uid))->result_array();        // 查询连赢记录
			 
        if (count($numdata)<1) {  
        	if ($shuying_result=='赢') {
        		$num=1;
        	}
        	else{
        		$num=0;
 		    }
        	$data = array(
	                'uid'      => $uid,                       // 会员ID
	                'num'      => $num,                       // 次数
	                'symbol'   => $symbol,                    // 数据标识
	                'state'    => 1,                          // 状态
	        );
	        $this->db->insert('investor_user_num',$data);     // 执行插入语句

        }elseif ($shuying_result=='赢') {                     //判断赢
        	$num=intval($numdata[0]['num'])+1;                //累计次数
        }elseif ($shuying_result=='输'){                      //中断连续，并重置次数
        	$num=0;
        }
		
		if ($shuying_result=='赢') {                             // 判断是否有会员数据
        	$data1 = array(
	                'customer_id' => $uid,                       // 会员ID
	                'flow'        => 3,                          // 流量
	                'play_time'   => date("Y-m-d H:i:s",time()), // 生成时间
	        );
	        $this->db->insert('play',$data1);                     // 新增流量次数
	        $result = $this->db->select('total_flow')->get_where('customer',array('id'=>$uid))->result_array(); // 查询连赢记录
	        // if (count($result)<1) {
	        // 	$flownum=intval($numdata[0]['num'])+1;
	        // }
	        $data2 = array(
                    // 会员ID
	                'total_flow' =>intval($result[0]['total_flow'])+3,         // 流量
	        );
	        $this->db->where('id',$uid)->update('customer',$data2);         // 新增流量次数


			$condition['uid'] =$uid;                                           // 更新对象id
			$updata['num'] =$num;                                              // 结果赋值
			$updata['symbol'] =$symbol;
			$updata['state'] =1;     
			$this->db->where($condition)->update("investor_user_num",$updata); // 执行更新语

        }
        if ($shuying_result=='输') {                                 // 判断是否有会员数据
			$condition['uid'] =$uid;                                           // 更新对象id
			$updata['num'] =$num;                                              // 结果赋值
			$updata['symbol'] =$symbol;
			$updata['state'] =1;     
			$this->db->where($condition)->update("investor_user_num",$updata); // 执行更新语
        }
	}
	/**
	 * 查询用户连赢次数
	 * @return integer
	 * @author ohyeah
	 */
	public function ying_num(){
        $uid=$this->is_uid();
        $result = $this->db->select('num')->get_where('investor_user_num',array('uid'=>$uid))->result_array(); // 查询连赢记录
        return count($result)>0?$result[0]['num']:0;
    }
    /**
	 * 取现订单状态转换
	 * @return integer
	 * @author ohyeah
	 */
    public function is_trade_status($trade_status=0) {
		switch ($trade_status){
	        case 0  : return    '失败'; break;
	        case 1  : return    '成功'; break;
	        case 2  : return    '处理中'; break;
	        default : return    false;  break;
	    }
	}
    /**
	 * 在线人数查询
	 * @return integer
	 * @author ohyeah
	 */
	public function online_number() {
		$list=$this->db->group_by('investor_uid')->get_where('investor_detail',array('start_time >'=>strtotime('-30 minutes')))->result_array();
		 return count($list)>0?count($list):0;
	}
	 /**
	 * 兑现一次流量后限制20次
	 * @return integer
	 * @author ohyeah
	 */
	public function game_times() {
		$uid=$this->is_uid();
		$data['lishi'] = $this->db->get_where('user_flow',array('customer_id '=>$uid))->result_array(); // 查询是否完成首次取现
        if (count($data['lishi'])>0) {
            $data2= $this->db->get_where('investor_detail',array('investor_uid '=>$uid,'start_time >'=>strtotime(date('Y-m-d 00:00:00'))))->result_array(); // 统计次数
            return count($data2)<20?count($data2):false;
        }
        return true;
	}
	public function game_shijian(){
		$uid  = $this->db->get_where('config')->result_array(); 
        return $uid[0]['extra'];
	}
}
