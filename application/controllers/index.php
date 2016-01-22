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

      // $content = file_get_contents("http://g1.iexchange.bz/iExchange/TradingConsole/inner/QuotationService.asmx");
      //  $content=explode(',',$content);
       // var_dump($content);
       
       
    }
   
     public function share(){
         $username=get_cookie('username')?get_cookie('username'):'';
         $sessionid=get_cookie('sessionid')?get_cookie('sessionid'):'';
        // $flow=get_cookie('flow')?get_cookie('flow'):'';
         if($username){
             $this->db->where('coustomer_id',$username);
         }
         if($sessionid){
             $this->db->where('visitor_id',$sessionid);
         }
         $today0=date('Y-m-d');//今天凌晨
         $tommorrow0=date('Y-m-d',strtotime('+1 day'));//明天凌晨
         $share_num=$this->db->where('share_time >='.$today0.' and share_time<='.$tommorrow0)->from('share')->count_all_results();//先统计今天次数，后面再插入本次数据
         if($share_num>3){
             echo json_encode(array('success'=>false,'info'=>'每天仅限三次分享获取流量'));
             
         }else{
             echo json_encode(array('success'=>true,'info'=>'本次分享获取流量1M,今天还有'.(3-$share_num-1).'次机会分享获取流量'));//不要忘了减去本次的次数
            }
             //这里还要处理流量
           $usercount=$this->db->where('coustomer_id',$username)->from('share')->count_all_results();//统计分享表中有没有这个用户
           $sessioncount=$this->db->where('visitor_id',$sessionid)->from('share')->count_all_results();//统计分享表中有没有这个游客

           $flow1=$this->db->where('coustomer_id',$username)->where('flow')->from('share')->result_array();//查出分享表中用户id的流量
           $flow2=$this->db->where('visitor_id',$sessionid)->where('flow')->from('share')->result_array();//查出分享表中游客id的流量

          if($usercount>0 && $share_num>3){//有这个用户并且分享次数超过3次
             
              $this->db->where('coustomer_id',$username)->update('share',array('flow'=>$flow1+3));//取出以前记录的流量加上现在最多可得的流量来更新用户的流量
           }else{ if($sessioncount && $share_num>3){//有这个游客id并且分享超过3次
               
                     $this->db->where('visitor_id',$sessionid)->update('share',array('flow'=>$flow2+3));//取出以前记录的流量加上现在最多可得的流量来更新游客的流量
                    }
                    if($usercount>0 && $share_num<3){//有这个用户且分享次数小于3次
                        $this->db->where('coustomer_id',$username)->updata('share',array('flow'=>$flow1+$share_num+1));
                    }
                    if($sessioncount && $share_num<3){//有这个游客id并且分享小于3次
                          $this->db->where('visitor_id',$sessionid)->update('share',array('flow'=>$flow2+$share_num+1));
                    }
               }
              
        
         $data=array(
             'coustomer_id'=>$username,
             'visitor_id'=>$sessionid,
             'share_time'=>date('Y-m-d H:i:s')
         );
         $res=$this->db->insert('share',$data);
     
     }
    
}