<?php
class  Index extends CI_Controller{
    
  public function __construct(){
        parent::__construct();
        $this->load->model('index_model','index');
    }
    //首页
    public function index(){
        $this->load->view('index.html');

        //$content = file_get_contents("http://hq.sinajs.cn/list=sh601006");
        //echo $content;
        
    }
}