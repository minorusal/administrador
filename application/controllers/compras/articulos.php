<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class articulos extends Base_Controller { 
	
	var $uri_segment  = 'compras/';
	var $uri_string   = 'articulos';
	var $view_content = 'content';
	
	public function __construct(){
		parent::__construct();
		$this->load->model($this->uri_segment.'articulos_model');
		$this->load->model($this->uri_segment.'catalogos_model');
		$this->lang->load("compras/articulos","es_ES");
	}

	public function config_tabs(){
		$config_tab['names']    = array($this->lang_item("agregar_articulo"), 
										$this->lang_item("listado_articulos"), 
										$this->lang_item("detalle_articulo")
								); 
		$config_tab['links']    = array('agregar_articulo', 
										'index', 
										'detalle_articulo'
										); 
		$config_tab['action']   = array('load_content_tab',
										'load_content_tab', 
										''
										);
		$config_tab['attr']     = array('','', array('style' => 'display:none'));

		return $config_tab;
	}

	private function uri_view(){
		$uri  = $this->uri_segment.$this->view_content; 
		return $uri;
	}

	public function index(){
		
		$presentaciones = dropdown_tpl($this->catalogos_model->get_presentaciones('','',false),'id_cat_presentaciones', array('clave_corta', 'presentaciones'));
		$lineas         = dropdown_tpl($this->catalogos_model->get_lineas('','',false),'id_cat_linea', array('clave_corta','linea'));
		$um             = dropdown_tpl($this->catalogos_model->get_um('','',false),'id_cat_um', array('clave_corta','um'));
		$marcas         = dropdown_tpl($this->catalogos_model->get_marcas('','',false),'id_cat_marcas', array('clave_corta','marcas'));

		
		$data['titulo_seccion']   = $this->lang_item("articulos");
		$data['titulo_submodulo'] = $this->lang_item("titulo_submodulo");
		$data['icon']             = 'fa fa-cubes';
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url($this->uri_string()),1,"$marcas<br>$presentaciones<br>$lineas<br>$um");
		
		$this->load_view($this->uri_view(), $data);
	}



}