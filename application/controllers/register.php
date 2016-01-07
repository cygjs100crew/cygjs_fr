<?php
class Register extends CI_Controller{
    public function __construct(){
        parent::__construct();
    }
    //注册页面
   public function index(){
       $this->load->view('register.html');
    } 
}