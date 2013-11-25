<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SMS extends CI_Controller {
		 public function __construct(){
		  	parent::__construct();
		  	$this->load->model('sms_functions');
		 }
		public function index(){
			$sms = $_GET['message'];
			$number = $_GET['number'];	
			$sms = trim($sms);
			//if message = More, retain level and load more
			
			if(strtoupper($sms)=="00"){
				$level = $this->sms_functions->get_level($number);
				$screen = $this->sms_functions->get_screen($number);
				$this->next_screen($number, $level, $screen);		
					
			}elseif($sms=="0"){
				$level = $this->sms_functions->lower_level($number);
				$screen = $this->sms_functions->get_screen($number);
				$this->previous_screen($number, $level, $screen);
			}
			else{
				if($sms==""){
					$this->load_counties($number, 1);	
						
				}elseif(is_numeric($sms)){
					$level = $this->sms_functions->get_level($number);
					$screen = $this->sms_functions->get_screen($number);
					//first update level
					if($level<4){
						$level = $level + 1;
					}
					$this->sms_functions->update_level($level, $number);	
					if($level==2){
						$this->load_districts($number, $screen, $sms);					
					}elseif($level==3){
						$this->load_wards($number, $screen, $sms);
					}elseif($level==4){
						//note last ward
						$this->db->query("update sessions set sess_last_ward='$sms' where sess_number='$number'");
						$this->load_centers($number, $screen, $sms);
					}
				}else{
				print $sms;
					$this->send_response("Request is not understood!\n0:Back", $number, $screen);
				}	
			}
		}
		public function load_districts($number, $screen, $code){
			$query = "SELECT%20*%20FROM%20".$this->config->item('gft_table')."%20WHERE%20County_Code='".$code."'";
			$this->db->query("update sessions set sess_last_county='$code' where sess_number='$number'");
			$request_url = $this->config->item('gft_url').$query."&key=".$this->config->item('gft_key');
			$result = get_object_vars(json_decode(file_get_contents($request_url)));

			$result = $result['rows'];
			$added = array();
			$c = '';
			foreach($result as $r){
				if(!in_array($r[3], $added)){
					$added[] = $r[3];
					$c .= $r[3].':'.$r[4]."\n";
				}
			}
			$this->send_response("Reply with a district number:\n".$c."0:Back", $number, $screen);
		}
		public function load_wards($number, $screen, $code){
			$query = "SELECT%20*%20FROM%20".$this->config->item('gft_table')."%20WHERE%20Const_Code='".$code."'";
			$this->db->query("update sessions set sess_last_const='$code' where sess_number='$number'");
			$request_url = $this->config->item('gft_url').$query."&key=".$this->config->item('gft_key');
			$result = get_object_vars(json_decode(file_get_contents($request_url)));
			
			$result = $result['rows'];
			$added = array();
			$c = '';
			foreach($result as $r){
				if(!in_array($r[5], $added)){
					$added[] = $r[5];
					$c .= $r[5].':'.$r[6]."\n";
				}
			}
			$this->send_response("Reply with a ward number:\n".$c."0:Back", $number, $screen);
		}
		public function load_centers($number, $newscreen, $code){
			$query = "SELECT%20*%20FROM%20".$this->config->item('gft_table')."%20WHERE%20C_Ward_Code='".$code."'";
			$request_url = $this->config->item('gft_url').$query."&key=".$this->config->item('gft_key');
			$result = get_object_vars(json_decode(file_get_contents($request_url)));

			$result = $result['rows'];
			$added = array();
			$c = '';
			$i = 1 ;
			$skip = ($newscreen-1)*5;
			$totalshown=1;
			foreach($result as $r){
				if(($i>$skip)&&($totalshown<6)){
					if(!in_array($r[7], $added)){
						$added[] = $r[7];
						$c .= $r[8]."\n";
						$totalshown++;
					}
				}
			}
			if($totalshown=='1'){
				$c .= "(No more results to show!)\n";			
			}
			$this->send_response($c."0:Back\n00:More", $number, $newscreen);
		}
		public function next_screen($number, $level, $screen){
			$newscreen = $screen + 1;
			if($level==1){
				$this->load_counties($number, $newscreen);			
			}elseif($level==4){
				$code = $this->sms_functions->last_ward($number);
				$this->load_centers($number, $newscreen, $code);
			}		
		}
		public function previous_screen($number, $level, $screen){
			$newscreen = $screen;
			if($level==1){
				$this->load_counties($number, $newscreen);			
			}elseif($level==2){
				$code = $this->sms_functions->last_county($number);
				$this->load_districts($number, $newscreen, $code);
			}elseif($level==3){
				$code = $this->sms_functions->last_const($number);
				$this->load_wards($number, $newscreen, $code);
			}elseif($level==4){
				$code = $this->sms_functions->last_ward($number);
				$this->load_centers($number, $newscreen, $code);
			}		
		}
		public function load_counties($number, $newscreen){
			$counties = $this->config->item('counties');
			$c = '';
			$i = 1 ;
			$skip = ($newscreen-1)*5;
			$totalshown=1;
			foreach($counties as $k=>$v){
				
				if(($i>$skip)&&($totalshown<6)){				
					$c .= $k.":".$v."\n";
					$totalshown++;
				} 
				$i++;				
			}
			if($totalshown=='1'){
				$c .= "(No more results to show!)\n";			
			}
			$this->send_response("Reply with a county number:\n".$c."\n00:More", $number, $newscreen);	
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
