<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class menus extends Base_Controller{

	private $modulo;
	private $submodulo;
	private $view_content;
	private $path;
	private $icon;
	private $offset, $limit_max;
	private $tab, $tab1, $tab2, $tab3;

	public function __construct(){
		parent::__construct();
		$this->modulo 			= 'nutricion';
		$this->submodulo		= 'menus';
		$this->icon 			= 'fa fa-cutlery'; 
		$this->path 			= $this->modulo.'/'.$this->seccion.'/'; 
		$this->view_content 	= 'content';
		$this->limit_max		= 10;
		$this->offset			= 0;
		// DB Model
		$this->load->model($this->modulo.'/'.$this->seccion.'_model','db_model');
		// Diccionario
		$this->lang->load($this->modulo.'/'.$this->seccion,"es_ES");
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
		return 'exito';
	}

}