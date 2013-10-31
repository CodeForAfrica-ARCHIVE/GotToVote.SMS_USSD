<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sms_functions extends CI_Model {
		
		public function clean($sms){
			$sms = $this->trim_spaces($sms);
			$sms = $this->caps($sms);			
			return $sms;
		}
		public function trim_spaces($sms){
			$sms = trim($sms);
			return $sms;			
		}
		public function caps($sms){
			$sms = strtolower($sms);
			$sms = ucfirst($sms);
			return $sms;		
		}
		public function dblog($message, $number){
			//check if there is active session
			$this->db->select("*");
			$this->db->from("sessions");
			$this->db->where("sess_number", $number);
			$query = $this->db->get();
			$total = $query->num_rows();
			
			$query = $query->result_array();
			$query = $query[0];	
			if($total>0){
			//check session level	
				$level = $query['sess_level'];
							
			}else{
			//start new session
				$this->db->query("insert into sessions(sess_number, sess_level, sess_current_screen, sess_started)values('$number', '1', '1', now())");				
			}
		}
		public function get_level($number){
			$this->db->select("*");
			$this->db->from("sessions");
			$this->db->where("sess_number", $number);
			$query = $this->db->get();
			
			$query = $query->result_array();
			$query = $query[0];	
			$level = $query['sess_level'];
			return $level;	
		}
		public function get_screen($number){
			$this->db->select("*");
			$this->db->from("sessions");
			$this->db->where("sess_number", $number);
			$query = $this->db->get();
			
			$query = $query->result_array();
			$query = $query[0];	
			$screen = $query['sess_current_screen'];
			return $screen;	
		}
	
}
?>
