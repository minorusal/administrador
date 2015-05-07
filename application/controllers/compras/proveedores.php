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

	private function uri_cl_principal($pag = ''){
		return $this->uri_modulo.$this->uri_submodulo.'/'.$pag;
	}

	public function index(){
		//$view_listado_articulo    = $this->listado_articulos($offset);
		$contenidos_tab           = $this->agregar_proveedor();//$view_listado_articulo;

		$data['titulo_seccion']   = $this->lang_item("seccion");
		$data['titulo_submodulo'] = $this->lang_item("submodulo");
		$data['icon']             = 'fa fa-users';
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),1,$contenidos_tab);
		
		$js['js'][]     = array('name' => 'proveedores', 'dirname' => 'compras');
		$this->load_view($this->uri_view_principal(), $data, $js);

	}
	public function listado_proveedores(){

	}

	public function agregar_proveedor(){
		$uri_view       = $this->uri_modulo.$this->uri_submodulo.'/proveedores_save';
		$lts_entidades  = dropdown_tpl(		$this->proveedores_model->get_entidades('','','',false), 
										'' ,
										'id_administracion_entidad', 
										array('clave_corta','entidad'),
										'lts_entidades', 
										'requerido'
									);
		$btn_save   = form_button(array('class'   => 'btn btn-primary',
										'name'    => 'save_proveedor',
										'onclick' => "send_form_ajax('".$this->uri_cl_principal('insert_proveedor')."','mensajes');", 
										'content' => $this->lang_item("btn_guardar")));

		$btn_reset  = form_button(array('class'   => 'btn btn-primary',
										'name'    => 'reset',
										'value'   => 'reset',
										'onclick' => 'clean_formulario()',
										'content' => $this->lang_item("btn_limpiar")));

		$lbl['lbl_rsocial']    = $this->lang_item('lbl_rsocial');
		$lbl['lbl_nombre']     = $this->lang_item('lbl_nombre');
		$lbl['lbl_clv']        = $this->lang_item('lbl_clv');
		$lbl['lbl_rfc']        = $this->lang_item('lbl_rfc');
		$lbl['lbl_calle']      = $this->lang_item('lbl_calle');
		$lbl['lbl_num_int']    = $this->lang_item('lbl_num_int');
		$lbl['lbl_num_ext']    = $this->lang_item('lbl_num_ext');
		$lbl['lbl_colonia']    = $this->lang_item('lbl_colonia');
		$lbl['lbl_municipio']  = $this->lang_item('lbl_municipio');
		$lbl['lbl_entidad']    = $this->lang_item('lbl_entidad');
		$lbl['lbl_cp']         = $this->lang_item('lbl_cp');
		$lbl['lbl_telefono']   = $this->lang_item('lbl_telefono');
		$lbl['lbl_email']      = $this->lang_item('lbl_email');
		$lbl['lbl_contacto']   = $this->lang_item('lbl_contacto');
		$lbl['lbl_comentario'] = $this->lang_item('lbl_comentario');
		$lbl['dropdown_entidad'] = $lts_entidades;
		$lbl['button_save']    = $btn_save;
		$lbl['button_reset']   = $btn_reset;

		if($this->ajax_post(false)){
				echo json_encode($this->load_view_unique($uri_view , $lbl, true));
		}else{
			return $this->load_view_unique($uri_view , $lbl, true);
		}
	}

	public function insert_proveedor(){
		$jsonData = array(	'result'  => $this->ajax_post('comentario'),
							'functions' => array( 'clean_formulario' => '' ));
		echo json_encode($jsonData);
	}
}