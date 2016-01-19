<?php
class Hushen300_model extends CI_Model{

	const TBL = investor_detail;
	public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
	

    public function index($data){
        
     // $this->db->insert('user_info',$data);
    }	
    public function lishi_list($data) {
    	$this->db->get(self::TBL);
    	return $query->result_array();
	}
}
