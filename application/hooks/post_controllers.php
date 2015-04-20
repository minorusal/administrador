<?php
if (!defined( 'BASEPATH')) exit('No direct script access allowed'); 
class check_session extends Base_Controller
{
	private $ci;
	public function __construct(){
		$this->ci =& get_instance();

	}	

	public function check_activity(){	
		$interseccion = null;
		$ajax = $this->ci->ajax_post(false);

		if($this->ci->uri->total_segments() != 1 && $this->ci->uri->segment(1) != 'login' ){
			if($this->ci->uri->segment(1) != 'login' ){
				if(!$this->ci->session->userdata('is_logged')){
					
					if($ajax){
						$msg = $this->ci->lang_item("msg_seesion_destroy");
						echo json_encode(alertas_tpl('error', $msg ,false));
						exit;
					}else{
						redirect(base_url('login'));
					}
				}
			}
			$uri_login= array('authentication','valindando' );
			$uri_secment  = $this->ci->uri->segment_array();
			$interseccion = array_intersect($uri_login,$uri_secment);

			if(!is_null($interseccion)){
				if(!$this->ci->session->userdata('is_logged') && $ajax == false){
					redirect(base_url('login'));
				}
			}
		
		}
		$dominio = $this->ci->session->userdata('dominio');
		$this->ci->load_database($dominio);
		
		
	}
}