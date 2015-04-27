<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class articulos extends Base_Controller { 
	
	var $uri_modulo     = 'compras/';
	var $uri_submodulo  = 'articulos';
	var $view_content   = 'content';
	
	
	public function __construct(){
		parent::__construct();
		$this->load->model($this->uri_modulo.'articulos_model');
		$this->load->model($this->uri_modulo.'catalogos_model');
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

	private function uri_view_principal(){
		$uri  = $this->uri_modulo.$this->view_content; 
		return $uri;
	}

	public function index(){
		$uri_view       = $this->uri_modulo.$this->uri_submodulo;
		$presentaciones = dropdown_tpl($this->catalogos_model->get_presentaciones('','',false),'id_cat_presentaciones', array('clave_corta', 'presentaciones'));
		$lineas         = dropdown_tpl($this->catalogos_model->get_lineas('','',false),'id_cat_linea', array('clave_corta','linea'));
		$um             = dropdown_tpl($this->catalogos_model->get_um('','',false),'id_cat_um', array('clave_corta','um'));
		$marcas         = dropdown_tpl($this->catalogos_model->get_marcas('','',false),'id_cat_marcas', array('clave_corta','marcas'));

		$btn_save  = form_button(array('class'=>"btn btn-primary",'name' => 'save_articulo' , 'content' => $this->lang_item("btn_guardar") ));
		$btn_reset = form_button(array('class'=>"btn btn-primary",'name' => 'reset' ,'content' => $this->lang_item("btn_limpiar")));


        $data_tab['nombre_articulo']   = $this->lang_item("nombre_articulo",false);
        $data_tab['cvl_corta']         = $this->lang_item("cvl_corta",false);
        $data_tab['marca']             = $this->lang_item("marca",false);
        $data_tab['list_marca']        = $marcas;
        $data_tab['presentacion']      = $this->lang_item("presentacion",false);
        $data_tab['list_presentacion'] = $presentaciones;
        $data_tab['linea']             = $this->lang_item("linea",false);
        $data_tab['list_linea']        = $lineas;
        $data_tab['um']                = $this->lang_item("um",false);
        $data_tab['list_um']           = $um;
        $data_tab['descripcion']       = $this->lang_item("descripcion",false);
        $data_tab['button_save']       = $btn_save;
        $data_tab['button_reset']      = $btn_reset;
		
		$view_aagregar_articulo   = $view = $this->load_view_unique($uri_view , $data_tab, true);

		$data['titulo_seccion']   = $this->lang_item("articulos");
		$data['titulo_submodulo'] = $this->lang_item("titulo_submodulo");
		$data['icon']             = 'fa fa-cubes';
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url($this->uri_string()),1,$view_aagregar_articulo);
		
		$js['js'][]     = array('name' => 'articulos', 'dirname' => 'compras');
		$this->load_view($this->uri_view_principal(), $data, $js);
	}

	public function guardar_articulo(){

	}

}