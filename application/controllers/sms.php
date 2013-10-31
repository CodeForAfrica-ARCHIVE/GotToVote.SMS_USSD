<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SMS extends CI_Controller {
		
		public function index(){
			$sms = $_GET['message'];
			$number = $_GET['number'];
			
			if(trim($sms)==""){
				$this->send_response("Please respond with the name of your county, district or ward", $$number);			
			}else{
				$this->load->model('sms_functions');	
				$sms = $this->SMS_functions->clean($sms);
			}	
		}
		public function send_response(){
			//$request_url = api_url()."?key=".api_key()."&message=".$message."&number=".$number;
			print "dsfa";			
			print api_url();
			//redirect();		
		}			
	}

?>
