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
			if(strtoupper($message)=="M"){
				$level = $this->sms_functions->get_level($number);
				$screen = $this->sms_functions->get_screen($screen);
				$this->sms_functions->next_screen($number, $level, $screen);			
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
			$i = 5 ;
			foreach($counties as $k=>$v){
								
				if($i<($newscreen*5)){					
					$c .= $k.":".$v."\n";
				} 
				$i++;				
			}
			$this->send_response("Reply with a county number:\n".$c."\nM:More", $number);	
		}
		public function send_response($message, $number){
			$this->sms_functions->dblog($message, $number);			
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
