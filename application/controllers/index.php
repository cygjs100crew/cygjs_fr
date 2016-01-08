<?php
class  Index extends CI_Controller{
    
  public function __construct(){
        parent::__construct();
        $this->load->model('index_model','index');
    }
    //首页
    public function index(){
        $this->load->view('index.html');

        $content = file_get_contents("http://hq.sinajs.cn/list=sh601006");
        $content=explode(',',$content);
        //var_dump($content[31]);
     
        
    }
    public function get(){
        
        $_SESSION;
        $this->session->set_userdata('name','张三');
        //var_dump($data);
      //echo $data["__ci_last_regenerate"];
       //echo $data['name'];
      
     }
    public  function a(){
        
       // $data=$this->session->userdata('name');
        //var_dump($data);
        //$this->db->insert('user_info',$data);
        $data=array(
            'name'=>'张三',
            'email'=>'zhanshan@qq.com',
            'time'=>'20160107',
            'password'=>'123456'
        );
        $this->db->insert('user_info',$data);
        
    }
}