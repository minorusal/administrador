<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inicio extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->removeCache();
		if(!$this->session->userdata('is_logged')){
			redirect('login');
		}
	}
	public function index(){
		$data['mensaje']      = 'hola bienvenido';
		$data['main_content'] = 'inicio';
		$this->load->view('includes/template', $data); 	
	}
}