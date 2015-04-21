<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class inicio extends Base_Controller {
	
	public function __construct(){
		parent::__construct();
	}

	public function index(){
		$data['titulo']  = 'inicio';
		$data['mensaje'] = 'hola bienvenido';

		$this->load_view('inicio', $data);
	
	}
}