<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Catalogos extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->removeCache();
		if(!$this->session->userdata('is_logged')){
			redirect('login');
		}
	}
	public function articulos(){
		$data['titulo'] = 'catalogo de articulos';
		
		$this->load->view('includes/header'); 
		$this->parser->parse('inventario/catalogos', $data);
		$this->load->view('includes/footer'); 
	}

	
}