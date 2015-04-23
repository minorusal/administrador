<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class articulos extends Base_Controller { 
	
	var $uri_string   = 'compras/articulos';
	var $view_content = 'compras/content';
	
	public function __construct(){
		parent::__construct();
		$this->lang->load("compras/articulos","es_ES");
	}

	public function index(){
		$data['titulo_seccion']   = $this->lang_item("articulos");
		$data['titulo_submodulo'] = $this->lang_item("titulo_submodulo");
		$this->load_view($this->view_content);
	}
}