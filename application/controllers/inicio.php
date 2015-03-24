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
		$data['titulo']  = 'inicio';
		$data['mensaje'] = 'hola bienvenido';

		$this->load->view('includes/header'); 
		$this->parser->parse('inicio', $data);
		$this->load->view('includes/footer'); 
	
	}
}