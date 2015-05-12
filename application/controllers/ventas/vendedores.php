<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class vendedores extends Base_Controller { 
	var $uri_modulo     = 'ventas/';
	var $uri_submodulo  = 'vendedores/';
	var $view_content   = 'content';
	var $view_listado   = 'listado';

	public function __construct(){
		parent::__construct();
		$this->load->model($this->uri_modulo.'vendedores_model');
		$this->lang->load("ventas/vendedores","es_ES");
	}
	public function config_tabs(){
		$pagina = (is_numeric($this->uri_segment_end()) ? $this->uri_segment_end() : "");
		$config_tab['names']    = array($this->lang_item("agregar_cliente"), 
										$this->lang_item("listado_cliente"),
										$this->lang_item("detalle_cliente")); 
		$config_tab['links']    = array('ventas/clientes/agregar_clientes', 
										'ventas/clientes/listado_clientes/'.$pagina,
										'detalle_cliente'); 
		$config_tab['action']   = array('load_content',
										'load_content',
										'');
		$config_tab['attr']     = array('','', array('style' => 'display:none'));

		return $config_tab;
	}

	private function uri_view_principal(){
		return $this->uri_modulo.$this->view_content;
	}

	public function index(){
		$view_listado_clientes 	= 'contenido';

		$data['titulo_seccion']   = $this->lang_item("vendedores");
		$data['titulo_submodulo'] = $this->lang_item("titulo_submodulo");
		$data['icon']             = 'fa fa-users';
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),2,$view_listado_clientes);
		
		$js['js'][]     = array('name' => 'clientes', 'dirname' => 'ventas');
		$this->load_view($this->uri_view_principal(), $data, $js);
	}
}
?>