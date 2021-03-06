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
        $customer_paiming=$this->db->limit(10)->order_by("id","desc")->get_where('customer')->result_array();
        $customer_list1=$this->db->limit(10)->order_by("total_flow","desc")->get_where('customer')->result_array();
        $customer_list2=$this->db->limit(10)->order_by("num","desc")->get_where('investor_user_num')->result_array();
        $customer_list3=$this->db->limit(10)->order_by("cash_flow","desc")->get_where('user_flow')->result_array();
        $customer_list4=$this->db->limit(10)->order_by("flow","desc")->get_where('share')->result_array();
    	$customer_phone_list=$this->db->query('select * from customer where phone  is not null')->result_array();
    	$total_flow_num=$this->db->query('select sum(total_flow) as sum from customer')->row()->sum;
    	$cash_flow_num=$this->db->query('select sum(cash_flow) as sum from user_flow where trade_status = 1')->row()->sum;
    	$idsumday=$this->db->get_where('investor_detail',array('start_time >'=>strtotime('-1 day')))->result_array(); // 查询图表数据
        $idsummonth=$this->db->get_where('investor_detail',array('start_time >'=>strtotime('-1 month')))->result_array(); // 查询图表数据
        $idsumyear=$this->db->get_where('investor_detail',array('start_time >'=>strtotime('-1 year')))->result_array(); // 查询图表数据
        $idsumday1=$this->db->query('select sum(flow) as sum from play where play_time >"'.date('Y-m-d H:i:s',strtotime('-1 day')).'"')->row()->sum;
        $idsummonth1=$this->db->query('select sum(flow) as sum from play where play_time >"'.date('Y-m-d H:i:s',strtotime('-1 month')).'"')->row()->sum; // 查询图表数据
        $idsumyear1=$this->db->query('select sum(flow) as sum from play where play_time >"'.date('Y-m-d H:i:s',strtotime('-1 year')).'"')->row()->sum; // 查询图表数据
        $idsumday2=$this->db->get_where('user_flow',array('cash_time >'=>date('Y-m-d H:i:s',strtotime('-1 day')),'trade_status'=>1))->result_array(); // 统计一天的取现笔数
        $idsummonth2=$this->db->get_where('user_flow',array('cash_time >'=>date('Y-m-d H:i:s',strtotime('-1 month')),'trade_status'=>1))->result_array(); // 统计一个月的取现笔数
        $idsumyear2=$this->db->get_where('user_flow',array('cash_time >'=>date('Y-m-d H:i:s',strtotime('-1 year')),'trade_status'=>1))->result_array(); // 统计一年的取现笔数
    	

    	$data['lishi'] = $this->db->limit(10)->order_by("id","desc")->get_where('investor_detail',array('symbol'=>"XAU"))->result_array(); // 查询历史交易


        $data['idsumday']=count($idsumday);
        $data['idsummonth']=count($idsummonth);
        $data['idsumyear']=count($idsumyear);
        $data['idsumday1']=$idsumday1;
        $data['idsummonth1']=$idsummonth1;
        $data['idsumyear1']=$idsumyear1;
        $data['idsumday2']=count($idsumday2);
        $data['idsummonth2']=count($idsummonth2);
        $data['idsumyear2']=count($idsumyear2);
    	$data['usernum']=count($customer_list);
        $data['customer_list']=$customer_list;
        $data['customer_paiming']=$customer_paiming;
        $data['customer_list1']=$customer_list1;
        $data['customer_list2']=$customer_list2;
        $data['customer_list3']=$customer_list3;
        $data['customer_list4']=$customer_list4;
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
            set_cookie('adminid',$username,time()+3600);
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
        if (count($data['lishi'])>0) {
            for ($i=0; $i <count($data['lishi']); $i++) { 
                $data['lishi'][$i]['trade_status'] = $this->is_trade_status($data['lishi'][$i]['trade_status']);
            }
        }
    	$this->load->view('admin/flow_index.html',$data);//前端在某个地方输出$username  
    }
    function count_index(){
    	$data['lishi'] = $this->db->limit(50)->order_by("id","desc")->get_where('share')->result_array(); // 查询历史交易
        $data['lishi1'] =json_encode($data['lishi']);
        var_dump(json_encode($data['lishi']));
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
        $data=$this->input->post();
		$condition['id'] =1;  
		$updata['extra'] =$data['extra'];
        $updata['value'] =$data['value'];
		$this->db->where($condition)->update("config",$updata); // 执行更新语
        redirect('admin/system_index');
    }        
}