<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SMS extends CI_Controller {
		 public function __construct(){
		  	parent::__construct();
		  	$this->load->model('sms_functions');
		 }
		public function index(){
			$sms = $_GET['message'];
			$number = $_GET['number'];	
			
			//if message = More, retain level and load more
			if(strtoupper($sms)=="M"){
				$level = $this->sms_functions->get_level($number);
				$screen = $this->sms_functions->get_screen($number);
				$this->next_screen($number, $level, $screen);			
			}else{
				if(trim($sms)==""){
					$this->load_counties($number, 1);	
						
				}else{	
					$sms = $this->sms_functions->clean($sms);
				}	
			}
		}
		public function next_screen($number, $level, $screen){
			$newscreen = $screen + 1;
			if($level==1){
				$this->load_counties($number, $newscreen);			
			}		
		}
		public function load_counties($number, $newscreen){
			$counties = $this->config->item('counties');
			$c = '';
			$i = 1 ;
			$skip = ($newscreen-1)*5;
			$totalshown=0;
			foreach($counties as $k=>$v){
				
					if(($i>$skip)&&($totalshown<6)){				
						$c .= $k.":".$v."\n";
					$totalshown++;
					
				} 
				$i++;				
			}
			$this->send_response("Reply with a county number:\n".$c."\nM:More", $number, $newscreen);	
		}
		public function send_response($message, $number, $newscreen){
			$this->sms_functions->dblog($message, $number, $newscreen);			
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
