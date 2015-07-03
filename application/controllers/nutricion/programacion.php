<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class programacion extends Base_Controller{
	private $modulo;
	private $submodulo;
	private $seccion;
	private $view_content;
	private $path;
	private $icon;
	private $view_modal;
	private $offset, $limit_max;
	private $tab, $tab1, $tab2, $tab3;

	public function __construct(){
		parent::__construct();
		$this->modulo 			= 'nutricion';
		$this->seccion		    = 'programacion';
		$this->icon 			= 'fa fa-calendar'; 
		$this->path 			= $this->modulo.'/'.$this->seccion.'/'; 
		$this->view_content 	= 'content';
		$this->tab1 			= 'config_programacion';
		$this->tab2 			= 'ver_calendario';

		$this->load->model($this->modulo.'/'.$this->seccion.'_model','db_model');
		$this->lang->load($this->modulo.'/'.$this->seccion,"es_ES");
	}
	public function config_tabs(){
		$tab_1 	= $this->tab1;
		$tab_2 	= $this->tab2;
		$path  	= $this->path;
		$config_tab['names']         = array($this->lang_item($tab_1) ,$this->lang_item($tab_2)); 
		$config_tab['links']         = array($path.$tab_1 ,$path.$tab_2); 
		$config_tab['action']        = array('','');
		$config_tab['attr']          = array('','');
		$config_tab['style_content'] = array('','');
		return $config_tab;
	}
	private function uri_view_principal(){
		return $this->modulo.'/'.$this->view_content;
	}

	function index(){
		$tabl_inicial 			  = 1;
		$view_agregar    		  = $this->form_config_programacion();	
		$contenidos_tab           = $view_agregar;
		$data['titulo_seccion']   = $this->lang_item($this->seccion);
		$data['titulo_submodulo'] = $this->lang_item("titulo_submodulo");
		$data['icon']             = $this->icon;
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);	
		
		$js['js'][]  = array('name' => $this->seccion, 'dirname' => $this->modulo);
		$this->load_view($this->uri_view_principal(), $data, $js);
	}

	function form_config_programacion(){
		$uri_view = $this->modulo.'/'.$this->seccion.'/config_programacion';
		return $this->load_view_unique($uri_view , '', true);
	}

}