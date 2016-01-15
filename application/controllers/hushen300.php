<?php
class hushen300 extends CI_Controller{
    public function __construct(){
        parent::__construct();
        
    }
    //k线图
    public function index(){



    	//$list   = M('ip_liuliang');
       // $list =$list->select();
        $list=$this->db->get('data_source')->result_array();
        // foreach($list as $k=>$v){
        //     // $timedata[$k] = date('Y-m-d',$v['time']);
        //     $timedata[$k] = $v['timedata'];
        //     $ipdata[$k] = $v['ipdata'];
        //     $pudata[$k] = $v['pudata'];
        //     $pvdata[$k] = $v['pvdata'];
        //     $result['ipdata'] = implode(',', $ipdata);
        //     $result['pudata'] = implode(',', $pudata);
        //     $result['pvdata'] = implode(',', $pvdata);
        //     $result['timedata'] = "'".implode("','", $timedata)."'";
          
        // }

        foreach($list as $k=>$v){

            // $Kdata[$k] ='['.$v['open'].','.$v['close'].','.$v['high'].','.$v['low'].']';
            $Kdata[$k] =$v['current'];
            $data_date[$k] ='"'.$v['data_date'].$v['data_time'].'"';

            $result['data_date'] = implode(',', $data_date);
            $result['ipdata'] = implode(',', $Kdata);
       
        }


       // $this->assign('conn', $result);
        $this->load->view('hushen300.html',$result);
        // var_dump($result['ipdata']);
    }
    public function data_add(){
    	$data = array(
                'open' => 1,
                'close' => 1,
                'current' => 1,
                'high' => 1,
                'low' => 1,
                'data_date' => 1,
                'data_time' => 1
            );
        $data1=$this->db->get_where('data_source',array('data_date' => 2111112,'data_time' =>1))->result_array();
        if(count($data1)==0){
            // $list->add($data);
            echo "成功";
            //把数据增加到data_source表中
        }
        var_dump($data1);
        echo "失败";
        
        // $this->db->insert('data_source',$data); //把数据增加到data_source表中
    }
        public function tt(){

        	// if( ){

        	// }

            echo "成功";


        	// echo "今天是 " . date("Y-m-d h:i:s") .strtotime("2016-01-11 14:07:46").date("Y-m-d H:i:s",1452495932); "";
    }
}