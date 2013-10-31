<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SMS_functions extends CI_Model {
		
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
			$sms = strtolower($sms):
			$sms = ucfirst($sms);
			return $sms;		
		}	
}
?>
