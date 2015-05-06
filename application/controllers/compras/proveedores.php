<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class proveedores extends Base_Controller { 

	var $uri_modulo     = 'compras/';
	var $uri_submodulo  = 'proveedores';
	var $view_content   = 'content';
	var $view_listado   = 'listado';

	public function __construct(){
		parent::__construct();
		$this->load->model($this->uri_modulo.'proveedores_model');
		$this->lang->load("compras/proveedores","es_ES");
	}

	public function config_tabs(){
		$pagina = (is_numeric($this->uri_segment_end()) ? $this->uri_segment_end() : "");
		$config_tab['names']    = array($this->lang_item("agregar_proveedor"), 
										$this->lang_item("listado_proveedor"), 
										$this->lang_item("detalle_proveedor")
								); 
		$config_tab['links']    = array('compras/proveedores/agregar_proveedor', 
										'compras/proveedores/listado_proveedor/'.$pagina, 
										''
										); 
		$config_tab['action']   = array('load_content',
										'load_content', 
										''
										);
		$config_tab['attr']     = array('','', array('style' => 'display:none'));

		return $config_tab;
	}

	private function uri_view_principal(){
		return $this->uri_modulo.$this->view_content;
	}

	public function index(){
		//$view_listado_articulo    = $this->listado_articulos($offset);
		$contenidos_tab           = 'tab';//$view_listado_articulo;

		$data['titulo_seccion']   = $this->lang_item("proveedores");
		$data['titulo_submodulo'] = $this->lang_item("titulo_submodulo");
		$data['icon']             = 'fa fa-users';
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),2,$contenidos_tab);
		
		$js['js'][]     = array('name' => 'articulos', 'dirname' => 'compras');
		$this->load_view($this->uri_view_principal(), $data, $js);

	}
}