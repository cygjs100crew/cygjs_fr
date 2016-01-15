<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Phone_model extends CI_Model{
	private $_apiUrl = 'http://gd.ums86.com:8899/sms/Api/Send.do'; // 发送短信接口地址
	public $SpCode='214877';//企业编号
	public $LoginName='admin';//账号
	public $Password='CYGJS100';//密码
	public $MessageContent;//发送内容
	public $UserNumber;//手机号码(多个号码用”,”分隔)，最多1000个号码
	public $SerialNumber='';//流水号，20位数字，每个请求流水号要求唯一,可不填
	public $ScheduleTime='';//预约发送时间，格式:yyyyMMddHHmmss,如‘20090901010101’，立即发送请填空（预约时间要写当前时间5分钟之后的时间，若预约时间少于5分钟，则为立即发送。）
	public $ExtendAccessNum='';//接入号扩展号（默认不填)
	public $f='';//提交时检测方式
	public $errorMsg;//发送错误时记录错误
	
	/**
	 * 发送短信
	 * @return boolean
	 */
	 //初始化
    public function __construct()
    {
        parent::__construct();
    }
	
	public function send($data) {
		$params = array(
				"SpCode" => $this->SpCode,
				"LoginName" => $this->LoginName,
				"Password" => $this->Password,
				"MessageContent" => iconv("UTF-8", "GB2312//IGNORE", $data['MessageContent']),
				"UserNumber" => $data['phone'],
				"SerialNumber" => $this->SerialNumber,
				"ScheduleTime" => $this->ScheduleTime,
				"ExtendAccessNum" => $this->ExtendAccessNum,
				"f" => $this->f,
		);
		$data1 = http_build_query($params);
		$res = iconv('GB2312', 'UTF-8//IGNORE', $this->_httpClient($data1));
		return $res;
		/*$resArr = array();
        parse_str($res, $resArr);
	
		if (!empty($resArr) && $resArr["result"] == 0) return true;
		else {
			if (empty($this->errorMsg)) $this->errorMsg = isset($resArr["description"]) ? $resArr["description"] : '未知错误';
			return false;
		}*/
	}
	
	
	/**
	 * POST方式访问接口
	 * @param string $data
	 * @return mixed
	 */
	private function _httpClient($data) {
		try {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$this->_apiUrl);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			$res = curl_exec($ch);
			curl_close($ch);
			return $res;
		} catch (Exception $e) {
			$this->errorMsg = $e->getMessage();
			return false;
		}
	}
}
