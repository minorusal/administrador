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
		if( $this->ci->uri->segment(1) != 'login' ){
			if(!$this->ci->session->userdata('is_logged')){
				if($ajax){
					$msg = $this->ci->lang_item("msg_seesion_destroy");
					echo json_encode(alertas_tpl('error', $msg ,false));
					exit;
				}else{
					redirect(base_url('login'));
				}
			}
		}else{
			$uri_login= array('authentication','valindando' );
			$uri_segment  = $this->ci->uri->segment_array();
			$interseccion = array_intersect($uri_login,$uri_segment);
			if(!empty($interseccion)){
				if(!$this->ci->session->userdata('is_logged') && $ajax == false){
					redirect(base_url('login'));
				}
			}
		}
		$dominio = $this->ci->session->userdata('dominio');
		$this->ci->load_database($dominio);	
	}

	public function check_sites_availables(){
		$uri = "";
		if($this->ci->session->userdata('is_logged')){
			$ajax             = $this->ci->ajax_post(false);
			$uri_string       = $this->query_uri_string();
			$sites_availables = $this->ci->session->userdata('sites_availables');
			$sites_availables[] = 'default_controller';
			$sites_availables[] = 'inicio';
			$sites_availables[] = 'logout';
			$sites_availables[] = 'login';
			$sites_availables[] = 'imprimir_ticket/test';
			$sites_availables[] = '404_override'; 
			if(!in_array($uri_string, $sites_availables)){
				if($uri_string==''){
					redirect(base_url('inicio'));
				}else{
					if(!$ajax){
						if(!mb_strstr($this->ci->uri_segment_end(),'export_')){
							if(!mb_strstr($this->ci->uri_segment_end(),'import_')){
								redirect(base_url('404_override'));				
							}
						}
					}
				}
			}
		}
	}

	private function query_uri_string(){
		$new_uri     = "";
		$uri_string  = $this->ci->uri->uri_string();
		$uri_string  = explode('/', $uri_string);
		foreach($uri_string as $value){
			if(!is_numeric($value)){
				$new_uri .= $value.'/';
			}
		}
		return trim($new_uri, '/');
	}
	
}