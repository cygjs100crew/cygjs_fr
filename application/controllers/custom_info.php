<?php
class Custom_info extends CI_Controller{
    public function __construct(){
        parent::__construct();
    }
    public function index(){
        $this->load->view('custom_info.html');
    }
}