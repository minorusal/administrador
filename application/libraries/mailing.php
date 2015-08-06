<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class mailing extends CI_Email{

	var $protocol  = 'smtp';
    var $smtp_host = 'ssl://smtp.gmail.com';
    var $smtp_port =  465;
    var $smtp_user = 'isolutionappsmx@gmail.com';
    var $smtp_pass = '1Sd3v3l0p3r.';
    var $mailtype  = 'html';

    private $ci;
	
	public function __construct(){
		$this->ci =& get_instance();
	}

	public function set_config(){
		
		$config['protocol']  = $this->protocol;
	    $config['smtp_host'] = $this->smtp_host;
	    $config['smtp_port'] = $this->smtp_port;
	    $config['smtp_user'] = $this->smtp_user;
	    $config['smtp_pass'] = $this->smtp_pass;
	    $config['mailtype']  = $this->mailtype;
	    $config['newline']   = $this->newline;
	}
	public function send_mail($send_params = array(), $confing = array()){

	}	
}