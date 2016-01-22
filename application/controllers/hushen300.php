<?php
class hushen300 extends MY_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->helper('cookie');
        $this->load->library('session');
    }
  

    //判断cookie中是否有username,没有就是游客,看看游客有多少流量
    function index(){
        $this->load->database();
         


        if(!get_cookie('sessonid')){//手动生成唯一的id，来唯一记录该游客
             
            $uid=$this->uuid();
            set_cookie('sessonid',$uid,0);
             
            //echo $uid;
        }

        $username=get_cookie('username')?get_cookie('username'):'游客';
        $flow=get_cookie('flow')?get_cookie('flow'):'0M';

        $data['username']=$username;
        $data['flow']=$flow;

        $this->load->view('hushen300.html',$data);//前端在某个地方输出$username,$flow；
         
    }
    //这里设置游客有多少流量，此时用户可能没有注册;玩了游戏的游客才会被记录到游客表中
    function setFlow(){
        $this->load->database();
        //存入cookie中

        $yk_id=get_cookie('sessionid');//获取游客id
        set_cookie('flow','100M');

        $count=$this->db->where('sessionid',$yk_id)->from('user_session')->count_all_results();//插入之前先查查游客表该游客是否被记录了

        $session_data=array(
            'sessionid'=>$yk_id,
            
        );
        if($count>0){//游客已经存入表中，只是更新
            unset($session_data['sessionid']);
            $this->db->where('sessionid',$yk_id)->update('user_session',$session_data);
        }else{
            $this->db->insert('user_session',$session_data);
        }
//         if(get_cookie('username')){//如果用户已经注册，则还要存入用户表
//             $this->db->where('username',$username)->update('user_info',array('flow'=>'100M'));
//         }
        echo 'success';
    }

    //用户注册的地方，假设用户表中有这几个字段，用户名，密码，确认密码，手机号,验证码
    function register(){
        $this->load->database();
        $data=$this->input->post();
        $userinfo['username']=$data['username'];
        $userinfo['password']=md5($data['password']);
        //$userinfo['email']=$data['email'];
        $userinfo['phone']=$data['phone'];
        $userinfo['checkCode']=$data['checkCode'];
        $userinfo['time']=date('Y-m-d H:i:s');
        //将用户名到cookie中,千万不要将密码写入cookie中，这样容易导致信息泄露
        if(!get_cookie('username') || get_cookie('username')!==$data['username']){//cookie中不存在用户名，或者存在的用户名与上传的用户名不同，就新增或者更新用户名
            set_cookie('username',$data['username'],0);
        }

        //$userinfo['repassword']=md5($data['repassword']);//不要传重复输入的密码，这是在前端验证的
        //取出该用户cookie中的流量数据，如果cookie中没有，就设置为0M
        //$userinfo['flow']=get_cookie('flow')?get_cookie('flow'):'0M';]
        $sms_code=$this->session->userdata('sms_code');
        if($sms_code!=$data['checkCode']){
            echo json_encode(array('success'=>false,'info'=>'短信验证码不对,请重新输入'));
            return;
        }
        $ret=$this->db->insert('user_info',$userinfo);
        if($ret){
            echo json_encode(array('success'=>true,'info'=>'注册成功'));
        }else{
            echo json_encode(array('success'=>false,'info'=>'注册失败'));
        }
    }
// 检验用户名和手机号是否重复注册
    public function check(){
        $data=$this->input->post();

        if(isset($data['username'])){
            $ret=$this->db->get_where('user_info',array('username'=>$data['username']))->result_array();
            $field='用户名';
        }elseif(isset($data['phone'])){
            $ret=$this->db->get_where('user_info',array('phone'=>$data['phone']))->result_array();
            $field='手机号';
        }

        if(count($ret)>0){
            echo json_encode(array('success'=>false,'info'=>$field.'已注册，请重新输入'));
            return;
        }else{
            echo json_encode(array('success'=>true,'info'=>$field.'正常'));
        }
    }
    //生成唯一id
    function uuid($prefix = '')

    {

        $chars = md5(uniqid(mt_rand(), true));

        $uuid     = substr($chars,0,8) . '-';

        $uuid .= substr($chars,8,4) . '-';

        $uuid .= substr($chars,12,4) . '-';

        $uuid .= substr($chars,16,4) . '-';

        $uuid .= substr($chars,20,12);

        return $prefix . $uuid;

    }
    //图片验证
    public function img(){
        //载入验证码辅助函数
        $this->load->helper('captcha');
        $speed = 'sfljlwjqrljlfafasdfasldfj1231443534507698';
        $word="";
        for($i=0;$i<4;$i++){
            $word .=$speed[mt_rand(0,strlen($speed)-1)];
        }
        //配置项
        $vals=array(
            'word'=>'$word',
            'img_path'=>'./captcha/',
            'img_url'=>base_url().'captcha/',
            'img_width'=> 80,
            'img_height'=>25,
            'expiration'=>60
        );
        //创建验证码
        $cap = create_captcha($vals);
        $_SESSION['code'] = $cap['word'];
        echo $cap['image'];
        //print_r($cap);
        /*if(!isset($_SESSION)){
         session_start();
        }*/


        /*$data['captcha'] = $cap['image'];

        $this->load->view('hushen300.html',$data);*/

    }
    //调用短信接口
    public function send_sms(){
        $this->load->model('phone_model','phone');
        $phone=$this->input->post('phone');
        //$phone='15074716900';
        //$MessageContent='手机测试';
        //生成验证码
        $code = rand(1000,9999);
        $this->session->set_userdata('sms_code',$code);//动态生成的短信验证码存入session中，后面注册验证时要用
        //短信内容
        //$date=date('Y年m月d日',time());
        $MessageContent ='您本次验证码为'.$code.'，如需退订回复TD。';
        $data = array(
            'phone' => $phone,
            'MessageContent' => $MessageContent
        );
        var_dump($data);
        $ret=$this->phone->send($data);
        //echo $ret;
        if(preg_match('/^result=0.*/i',$ret)){
            echo json_encode(array('success'=>true,'info'=>'发送成功'));
            return;
        }else{
            echo json_encode(array('success'=>false,'info'=>$ret));
        }
    }
    //测试短信接口用
    public function test_sms(){
        $this->load->model('phone_model','phone');
        $data = array(
            'phone' => '15817375365',
            'MessageContent' => '您本次验证码为12345678如需退订回复TD。',
        );
        echo $this->phone->send($data);
    }
    /* 数据添加 @ohyeah */
    public function data_add(){
        $data = array(
                'current' =>$_POST['current'],
                'time'      => time(),
            );
        // $data1=$this->db->get_where('data_source',array('data_date' =>$_POST['data_date'],'data_time' =>$_POST['data_time']))->result_array();
        // if(count($data1)==0){
            $h = intval(date("Hi")); 
            if (($h < 1500 && $h > 930)||($h < 1130 && $h > 1300)&&((date('w') != 6)||(date('w') != 0))) {
                $this->db->insert('data_source',$data);
            } else {
                echo json_encode(array('success'=>false,'info'=>'现在处于休市状态！'));
            }
        // }

    }
    /* 会员投资（下单） @ohyeah */
    public function investor_detail_add(){
        $data = array(
                'start_time'   => time(),
                'and_time'     => strtotime("+60 seconds"),
                'capital'      => $_POST['capital'],
                'duration'     => 60,
                'add_ip'       => $_SERVER["REMOTE_ADDR"],
                'invest_type'  => $_POST['invest_type'],
                'status'       => 1,
                'investor_uid' => get_cookie('id'),
                'current'      => 1,
                'symbol'       => 'CFIFZ5'
            );
        if($this->db->insert('investor_detail',$data)){
            echo json_encode($data);
        }
    }
    /* 查询历史交易 @ohyeah */
    public function lishi_list(){
        $data = $this->db->get("investor_detail")->result_array();
        $row = '';
        foreach ($data as $key => $val)
        {
            $row .='编号：'.$val['id'].'金额:'.$val['capital'].'开始:'.date("Y-m-d H:i:s",$val['start_time']).'结束:'.date("Y-m-d H:i:s",$val['and_time']).'结果：'.$val['result'];
        }
        $row .= '';
        echo json_encode(array('result' => true,'a' =>$row));
    }
    //沪深300走势线iframe显示页面
    function hushen_link(){
        $list=$this->db->get('data_source')->result_array();
        foreach($list as $k=>$v){
            $Kdata[$k] =$v['current'];
            $data_date[$k] ='"'.date('Y-m-d H:i:s',$v['time']).'"';
            $result['data_date'] = implode(',', $data_date);
            $result['ipdata'] = implode(',', $Kdata);
        }
        $this->load->view('hushen_link.html',$result);//前端在某个地方输出$username,$flow；
    }
    //交易历史iframe显示页面
    function lishi_html_list(){
        $data['lishi'] = $this->db->limit(20)->order_by("id","desc")->get_where('investor_detail',array('symbol'=>"CFIFZ5"))->result_array();


        // $this->load->library('pagination');
        // $config['base_url'] = 'http://example.com/index.php/test/page/';
        // $config['total_rows'] = 200;
        // $config['per_page'] = 20;
        // $this->pagination->initialize($config);//序列化
        // echo $this->pagination->create_links();//生成分页导航

        $result=$this->shuying();

        $this->load->view('lishi_html_list.html',$data);//前端在某个地方输出$username,$flow；
         
    }
    // 验证输赢
    function shuying(){
        $data=$this->db->get_where('investor_detail',array('result'=>""))->result_array();


        for ($i=0; $i < count($data); $i++) { 

            $list=$this->db->get_where('data_source',array('time >'=>$data[$i]['and_time']))->result_array();

            if (count($list)>0){

				if (intval($data[$i]['current'])==1) {
				    if ($data[$i]['current']>$list[0]['current']) {
				        $a='赢';
				    }
				    else if ($data[$i]['current']<$list[0]['current']) {
				        $a='输';
				    }
				}
				if (intval($data[$i]['current'])==0) {
				    if ($data[$i]['current']<$list[0]['current']) {
				        $a='赢';
				    }
				    else if ($data[$i]['current']>$list[0]['current']) {
				        $a='输';
				    }
				}

                $condition['id'] =$data[$i]['id'];//更新id
                $map['result'] =$a; //更新内容
                $this->db->where($condition)->update("investor_detail",$map);
            }
        }
    }
}
