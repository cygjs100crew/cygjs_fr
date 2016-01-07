<?php
class Login extends CI_Controller{
    public function __construct(){
        parent::__construct();

    }
    //登入页面
   public function index(){
       $this->load->view('login.html');
   }
}