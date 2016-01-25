<?php
class  Index extends MY_Controller{
    
  public function __construct(){
        parent::__construct();
        $this->load->model('index_model','index');
    }
    //首页
    public function index(){
		$wx_param=array(
			'appId'=>'wxed2d1da1f9023761',
			'appSecret'=>'452f7ea20e7d0ecadd38acef8664ceec'
		);
		$this->load->library('jssdk',$wx_param);
		$signPackage = $this->jssdk->GetSignPackage();
		$data['signPackage']= $signPackage;
        $this->load->view('index.html',$data);
       
    }
   
     public function share(){
		 $perFlow=$this->config->item('flow_per_share');
		 $total_share=$this->config->item('sharetime_per_day');
         $customer_id=get_cookie('customerId');
		 $this->db->where('coustomer_id',$coustomer_id);
         $today0=date('Y-m-d');//今天凌晨
         $tommorrow0=date('Y-m-d',strtotime('+1 day'));//明天凌晨
         $share_num=$this->db->where('share_time >='.$today0.' and share_time<='.$tommorrow0)->from('share')->count_all_results();//先统计今天次数，后面再插入本次数据
		 $data=array(
             'coustomer_id'=>$customer_id,
             'share_time'=>date('Y-m-d H:i:s'),
         );
         if($share_num>3){
             echo json_encode(array('success'=>false,'info'=>'每天仅限三次分享获取流量'));
			 $data['flow']=0;   
         }else{
             echo json_encode(array('success'=>true,'info'=>'本次分享获取流量'.$perFlow.'M,今天还有'.($total_share-$share_num-1).'次机会分享获取流量'));//不要忘了减去本次的次数\
			 $data['flow']=$perFlow;
         }

         $res=$this->db->insert('share',$data);    
     }
    
}