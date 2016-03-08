<?php
//error_reporting(0);
class Admin extends MY_Controller{
    public function __construct(){
        parent::__construct();
        $wx_param=array(
			'appId'=>'wxed2d1da1f9023761',
			'appSecret'=>'452f7ea20e7d0ecadd38acef8664ceec'
		);
		$this->load->library('jssdk',$wx_param);            
    } 
    
//判断cookie中是否有username,没有就是游客,看看游客有多少流量
    function index(){
    	if (!get_cookie('adminid')){
    		redirect('admin/login');
    	}
    	$list=$this->db->query('select count(distinct create_time)countid,substr(create_time,1,10)create_time from customer where create_time between "'.date('Y-m-d H:i:s',strtotime('-1 month')).'" and "'.date('Y-m-d H:i:s').'" group by substr(create_time,1,10)')->result_array();
    	foreach($list as $k=>$v){
            $Kdata[$k] =$v['countid'];
            $data_date[$k] =$v['create_time'];
        // $result['data_date'] = implode(',', $data_date);                                                        // 拼接报价数据格式
        // $result['ipdata'] = implode(',', $Kdata);       
        $data['data_date'] = '"'.implode('","', $data_date).'"';                                                    // 拼接报价数据格式
        $data['ipdata'] = implode(',', $Kdata);                                                          // 拼接时间数据格式
        }

    	$customer_list=$this->db->order_by("id","desc")->get_where('customer')->result_array();
    	$customer_phone_list=$this->db->query('select * from customer where phone  is not null')->result_array();
    	$total_flow_num=$this->db->query('select sum(total_flow) as sum from customer')->row()->sum;
    	$cash_flow_num=$this->db->query('select sum(cash_flow) as sum from user_flow where trade_status = 2')->row()->sum;
    	$idsum=$this->db->get_where('investor_detail',array('start_time >'=>date('Y-m-d H:i:s',strtotime('-1 day'))))->result_array(); // 查询图表数据
    	

    	$data['lishi'] = $this->db->limit(10)->order_by("id","desc")->get_where('investor_detail',array('symbol'=>"XAU"))->result_array(); // 查询历史交易


        $data['idsum']=count($idsum);
    	$data['usernum']=count($customer_list);
    	$data['total_flow_num']=$total_flow_num;
    	$data['cash_flow_num']=$cash_flow_num;
    	$data['customer_phone_list']=count($customer_phone_list);
    	$data['online_number']= $this->online_number();
        $this->load->view('admin/index.html',$data);//前端在某个地方输出$username  
    }
    function login(){
    	// redirect('admin/index');
        $username =$this->input->post('username');
        $p =$this->input->post('p');

        if (!(($p=='cygjs1000')&&($username=='cygjs100'))){
            echo "密码错误！";
            $this->load->view('admin/login.html');//前端在某个地方输出$username  
        }else{
            set_cookie('adminid',$username,1);
            redirect('admin/index');
        }
    	
    	
    }
    function user_index(){
    	$nickname =$this->input->post('nickname');
        if(is_numeric($nickname)){
            $map['id']=   intval($nickname);
        }else{
            $map['name like']    =  '%'.$nickname.'%';
        }
    	$data['lishi'] = $this->db->limit(50)->order_by("id","desc")->get_where('customer',$map)->result_array(); // 查询历史交易
        $this->load->view('admin/user_index.html',$data);//前端在某个地方输出$username  
    }
    function system_index(){
    	$data['lishi'] = $this->db->limit(10)->order_by("id","desc")->get_where('config')->result_array(); // 查询历史交易
    	$this->load->view('admin/system_index.html',$data);//前端在某个地方输出$username  
    }
    function flow_index(){
    	$data['lishi'] = $this->db->limit(50)->order_by("id","desc")->get_where('user_flow')->result_array(); // 查询历史交易
    	$this->load->view('admin/flow_index.html',$data);//前端在某个地方输出$username  
    }
    function count_index(){
    	$data['lishi'] = $this->db->limit(50)->order_by("id","desc")->get_where('share')->result_array(); // 查询历史交易
    	$this->load->view('admin/count_index.html',$data);//前端在某个地方输出$username  
    }
    function charts_index(){
    	$data['lishi'] = $this->db->limit(10)->order_by("id","desc")->get_where('investor_detail',array('symbol'=>"XAU"))->result_array(); // 查询历史交易
    	$this->load->view('admin/charts_index.html',$data);//前端在某个地方输出$username  
    }
    /**
     * 会员状态修改
     */
    public function changeStatus($method=null,$id=0){
		$method=$_GET['method'];
		$id=$_GET['id'];
    	switch ( strtolower($method) ){
            case 'forbiduser':
                
                break;
            case 'resumeuser':
                
                break;
            case 'deleteuser':
                                $result=$this->db->delete('customer', array('id' => $id));
                                if($result>0){
                                    echo json_encode(array('status'=>1,'info'=>'删除成功'));
                                } else {
                                    echo json_encode(array('status'=>0,'info'=>'删除失败'));
                                }
                break;
            default:
                echo json_encode(array('status'=>0,'info'=>'参数非法'));
        }
    	
        
    	
        // $id = array_unique((array)I('id',0));
        // $id = is_array($id) ? implode(',',$id) : $id;
        // if ( empty($id) ) {
        //     $this->error('请选择要操作的数据!');
        // }
        // $map['id'] =   array('in',$id);
        // switch ( strtolower($method) ){
        //     case 'forbiduser':
        //         $this->forbid('Huodong', $map );
        //         break;
        //     case 'resumeuser':
        //         $this->resume('Huodong', $map );
        //         break;
        //     case 'deleteuser':
        //                         $Model = M('Huodong');
        //                         if($Model->where($map)->delete()){
        //                             $this->success('删除成功');
        //                         } else {
        //                             $this->error('删除失败！');
        //                         }
        //         break;
        //     default:
        //         $this->error('参数非法');
        // }
    }
    function config(){

   //  		$condition['uid'] =$uid;                                           // 更新对象id
			// $updata['name'] =$num;                                              // 结果赋值
			// $updata['extra'] =$symbol;
			// $updata['value'] =1;     
			// $this->db->where($condition)->update("config",$updata); // 执行更新语
    }
}