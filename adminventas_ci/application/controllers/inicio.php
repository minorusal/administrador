<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class inicio extends Base_Controller {

	private $modulo;
	private $submodulo;
	private $seccion;
	private $view_content;
	private $path;
	private $icon;

	private $offset, $limit_max;
	private $tab, $tab1;

	private $titulo, $mensaje, $template;
	
	public function __construct(){
		parent::__construct();
		$this->modulo 			= 'inicio';
	}



	public function index(){

	
		$this->load_view('inicio');	
	}
}