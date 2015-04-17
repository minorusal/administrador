<?php
if (!defined( 'BASEPATH')) exit('No direct script access allowed'); 
class check_session extends Base_Controller
{
	private $ci;
	public function __construct(){
		$this->ci =& get_instance();

	}	

	public function check_activity(){	
		
		

		$ajax = $this->ci->ajax_post(false);
			if($this->ci->uri->segment(1) != 'login' ){
				if(!$this->ci->session->userdata('is_logged')){
					
					if($ajax){
						$msg = '<strong>Advertencia!</strong><br>Su sesion ha finalizado por favor inicie sesion nuevamente, gracias.';
						echo json_encode(alertas_tpl('', $msg ,false));
						exit;
					
					}else{
						redirect(base_url('login'));
					}
				}
			}
		$dominio = $this->ci->session->userdata('dominio');
		$this->ci->load_database($dominio);
		/*if($this->ci->uri->segment(1) == 'login' && $this->ci->session->userdata('is_logged') == true){

            redirect(base_url('inicio'));
 
        }else if($this->ci->session->userdata('is_logged') == false && $this->ci->uri->segment(1) != 'login')
        {
        	redirect(base_url('login'));
 
        }*/
	}
}