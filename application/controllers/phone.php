<?php
class Phone extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->library('session');
        $this->load->model('phone_model');

    }
    //发送短信验证码
    public function send(){
        $phone = $this->input->get_post('phone');
        if(empty($phone)){
            echo json_encode(0);
            exit();
        }
        $code = rand(1000,9999);
        $this->session->set_userdata('phone_code', $code);
        $MessageContent = '验证码为：'.$code.'，该验证码30分钟内有效，请正确填写。';		//短信内容
        $data = array(
            'phone' => $phone,
            'code' => $code,
            'MessageContent' => $MessageContent,);
        $result = $this->phone_model->send($data);
        if ($result){
            echo json_encode(1);
        }else{
            echo json_encode(0);
        }
    }
    public function query(){
        echo json_encode($this->phone_model->query());
    }
}