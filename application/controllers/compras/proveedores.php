<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class proveedores extends Base_Controller { 

	var $uri_modulo     = 'compras/';
	var $uri_submodulo  = 'proveedores';
	var $view_content   = 'content';
	
	public function __construct(){
		parent::__construct();
		$this->load->model($this->uri_modulo.'proveedores_model');
		$this->lang->load("compras/proveedores","es_ES");
	}

	public function index(){
		$data['titulo']  = 'inicio';
		$data['mensaje'] = 'hola bienvenido';

		$this->load_view('inicio', $data);
	}
}