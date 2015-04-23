<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class proveedores extends Base_Controller { 

	public function index(){
		$data['titulo']  = 'inicio';
		$data['mensaje'] = 'hola bienvenido';

		$this->load_view('inicio', $data);
	}
}