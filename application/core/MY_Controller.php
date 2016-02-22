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
			//var_dump($ret);
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
				$data=$this->db->get_where('investor_detail',array('result'=>""))->result_array();                                 // 读取未开奖的条目
				for ($i=0; $i < count($data); $i++) {                                                                                     // 逐条遍历
				$list=$this->db->get_where('recentquotation',array('time >'=>'2016-01-25 '.date('H:i:s',$data[$i]['and_time']),'symbol'=>$data[$i]['symbol']))->result_array();
				
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
	    if (empty($user)) {       // 判断是否为空
	    return 0;                 // true返回空0
	    } else {
	    return $user;             // false返回属性信息
	    }
	}
	/**
	 * 判断股市开市时间
	 * @return integer 0-休市，1-开市
	 * @author ohyeah
	 */
	public function is_opentime(){
        $h = intval(date("Hi"));                                                                       // 获取当前时间值
        if (($h < 1500 && $h > 930)||($h < 1130 && $h > 1300)&&((date('w') != 6)||(date('w') != 0))) { // 判断股市休市时间范围
        return 1; 
        } else {
        return 0;                                                                                      // 返回属性信息
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
        $invdata = $this->db->get("investor_detail")->result_array(); 
                                  // 查询投资记录
        $numdata = $this->db->get_where('investor_user_num',array('uid'=>$uid))->result_array(); // 查询连赢记录
			 
        if (count($numdata)<1) {  
        	$num=0;
        }elseif ($shuying_result=='赢') {                   //判断赢
        	$num=intval($numdata[0]['num'])+1;     //累计次数

        }else{                                     //中断连续，并重置次数
        	$num=0;
        }
		
		if (count($numdata)>0) {                                 // 判断是否有会员数据
		        if ($num>2) {                                        // 连续3次赢1M流量规则
	        	$data = array(
		                'customer_id' => $uid,                       // 会员ID
		                'flow'        => 1,                          // 流量
		                'play_time'   => date("Y-m-d H:i:s",time()), // 生成时间
		        );
		        $this->db->insert('play',$data);                     // 新增流量次数
		        $num=0;                                              // 重置连续
        	}
			$condition['uid'] =$uid;                                           // 更新对象id
			$updata['num'] =$num;                                              // 结果赋值
			$updata['symbol'] =$symbol;
			$updata['state'] =1;     
			                                        // 比较值
			$this->db->where($condition)->update("investor_user_num",$updata); // 执行更新语

        }else{                                                //第一次新增数据
        	$data = array(
	                'uid'      => $uid,                       // 会员ID
	                'num'      => $num,                       // 次数
	                'symbol'   => $symbol,                    // 数据标识
	                'state'    => 1,                          // 状态
	        );
	        $this->db->insert('investor_user_num',$data);     // 执行插入语句
        }
	}
	/**
	 * 查询用户连赢次数
	 * @return integer
	 * @author ohyeah
	 */
	public function ying_num(){
        $uid=$this->is_uid();
        $result = $this->db->get_where('investor_user_num',array('uid'=>$uid))->result_array(); // 查询连赢记录
        return count($result)>0?$result[0]['num']:0;
    }
    /**
	 * 查询最新报价
	 * @return integer
	 * @author ohyeah
	 */
	public function new_price($symbol=''){
        $result = $this->db->limit(1)->order_by("id","desc")->get_where('recentquotation',array('symbol'=>$symbol))->result_array(); // 查询最近一条报价记录
        return $result[0]['price'];
    }
}
