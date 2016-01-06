<?php
class Kxian extends CI_Controller{
    public function __construct(){
        parent::__construct();
        
    }
    //k线图
    public function index(){
        $this->load->view('kxian.html');
    }
}