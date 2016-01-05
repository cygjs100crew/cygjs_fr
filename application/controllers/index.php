<?php
class  Index extends CI_Controller{
    
  public function __construct(){
        parent::__construct();
        $this->load->model('index_model','index');
    }
    public function index(){
        $this->load->view('index.html');
    }
}