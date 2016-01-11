<?php
class hushen300 extends CI_Controller{
    public function __construct(){
        parent::__construct();
        
    }
    //k线图
    public function index(){



    	//$list   = M('ip_liuliang');
       // $list =$list->select();
        $list=$this->db->get('cy_ip_liuliang')->result_array();
        foreach($list as $k=>$v){
            // $timedata[$k] = date('Y-m-d',$v['time']);
            $timedata[$k] = $v['timedata'];
            $ipdata[$k] = $v['ipdata'];
            $pudata[$k] = $v['pudata'];
            $pvdata[$k] = $v['pvdata'];
            $result['ipdata'] = implode(',', $ipdata);
            $result['pudata'] = implode(',', $pudata);
            $result['pvdata'] = implode(',', $pvdata);
            $result['timedata'] = "'".implode("','", $timedata)."'";
          
        }

       // $this->assign('conn', $result);
        $this->load->view('hushen300.html',$result);
        var_dump($result);





        $this->load->view('hushen300.html');
    }
    public function data_add(){
    	$data = array(
                'open' => $_POST['open'],
                'close' => $_POST['close'],
                'current' => $_POST['current'],
                'high' => $_POST['high'],
                'low' => $_POST['low'],
                'data_date' => $_POST['data_date'],
                'data_time' => $_POST['data_time'],
            );
        $this->db->insert('data_source',$data); //把数据增加到data_source表中
    }
        public function tt(){

        	// if( ){

        	// }



        	echo "今天是 " . date("Y-m-d h:i:s") .strtotime("2016-01-11 14:07:46").date("Y-m-d H:i:s",1452495932); "";
    }
}