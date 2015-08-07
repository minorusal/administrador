<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH.'third_party/PHPMailer/PHPMailerAutoload.php');

class mailing extends PHPMailer{
	
	public $SMTPDebug      = 0;
	public $Host           = 'smtp.gmail.com';
	public $Port           = '465';
	public $SMTPSecure     = 'ssl';
	public $SMTPAuth       = true;
	public $Mailer         = 'smtp';
	public $Username       = 'isolutionappsmx@gmail.com';
	public $Password       = '1Sd3v3l0p3r.';

	public $ContentType    = 'text/html';
	public $From           = '';
	public $FromName       = '';
	public $Subject        = '';
	public $Body           = '';
	public $AltBody        = '';
	
	public $Hostname       = '';
	

	
	
	public $Debugoutput    = '';
	public $to             = '';
	public $cc             = '';
	public $bcc            = '';
	public $ReplyTo        = '';
	public $all_recipients = '';
	
	public function __construct(){
		parent::__construct();
	}
	

}
