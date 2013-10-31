<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SMS extends CI_Controller {
		 public function __construct(){
		  	parent::__construct();
		  	$this->load->model('sms_functions');
		 }
		public function index(){
			$sms = $_GET['message'];
			$number = $_GET['number'];
			
			if(trim($sms)==""){
				$counties = $this->config->item('counties');
				$c = '';
				foreach($counties as $k=>$v){
					$c .= $k.":".$v."\n"; 				
				}
				$this->send_response("Reply with a county number:\n".$c, $number);			
			}else{	
				$sms = $this->sms_functions->clean($sms);
			}	
		}
		public function send_response($message, $number){
			/*
			$api_url = $this->config->item('api_url');
			$api_key = $this->config->item('api_key');
			$request_url = $api_url."?key=".$api_key."&message=".$message."&number=".$number;
			redirect($request_url);
			*/
			//For testing purposes
			print $message;
		}			
	}
?>
