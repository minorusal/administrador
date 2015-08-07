<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class sign_up extends Base_Controller {

	public function index(){
		if($this->ajax_get('token')){
			echo $this->generate_token();
		}else{
			redirect(base_url('login'));
		}
		
	}

}

function encrypt($string, $key) {
   $result = '';
   for($i=0; $i<strlen($string); $i++) {
      $char = substr($string, $i, 1);
      $keychar = substr($key, ($i % strlen($key))-1, 1);
      $char = chr(ord($char)+ord($keychar));
      $result.=$char;
   }
   return base64_encode($result);
}