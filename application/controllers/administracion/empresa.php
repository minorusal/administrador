<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class empresa extends Base_Controller {

	private $modulo;
	private $submodulo;
	private $seccion;
	//private $view_content;
	private $path;
	private $icon;

	private $offset, $limit_max;
	private $tab, $tab1;

	private $titulo, $mensaje, $template;
	
	public function __construct(){
		parent::__construct();
		$this->modulo 			= 'administracion';
		$this->submodulo 		= 'empresa';
		$this->icon 			= 'fa fa-building-o'; 
		$this->template 		= 'content';
		$this->path 			= $this->modulo.'/'.$this->submodulo.'/'; #administracion/entidades
		$this->view_content 	= 'contentInfo';
		$this->limit_max		= 5;
		$this->offset			= 0;
		$this->titulo 			= 'Empresa';
		$this->mensaje 			= '<h4>Bienvenido al sistema AdminVentas.</h4> <br/>Por favor seleccione una opción del menú lateral.';
		// Tabs
		$this->tab1 			= 'inicio';
		$this->load->model($this->modulo.'/'.$this->submodulo.'s_model','db_model');
		// Diccionario
		//$this->lang->load($this->modulo.'/'.$this->seccion,"es_ES");
	}

	public function config_tabs(){
		$tab_1 	= $this->tab1;
		$path  	= $this->path;
		// Nombre de Tabs
		$config_tab['names']    = array( $this->titulo ); 
		// Href de tabs
		$config_tab['links']    = array($path.$tab_1 ); 
		// Accion de tabs
		$config_tab['action']   = array('');
		// Atributos 
		$config_tab['attr']     = array('');
		return $config_tab;
	}

	public function index(){

		$tabl_inicial 			  = 1;
		$contenidos_tab           = $this->mensaje;
		$data['titulo_seccion']   = $this->titulo;
		$data['titulo_submodulo'] = 'Bienvenido';
		$data['icon']             = $this->icon;
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);	
		
		$this->load_view($this->template, $data);	
	}
}