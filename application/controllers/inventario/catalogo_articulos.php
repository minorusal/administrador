<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Catalogo_Articulos extends Base_Controller {
	
	var $uri_string  = 'inventario/catalogos/';

	public function __construct(){
		parent::__construct();
		$this->removeCache();
		if(!$this->session->userdata('is_logged')){
			redirect('login');
		}
		$dominio = $this->session->userdata('dominio');
		$this->load_database($dominio);
	}
	
	public function config_tabs(){
		$config_tab['names']    = array('nuevo articulo', 'listado articulos', 'detalle'); 
		$config_tab['links']    = array('nuevo', 'articulos', 'otro'); 
		$config_tab['action']   = array('load_content_tab','redirect', 'load_content_tab');

		return $config_tab;
	}

	public function articulos($offset = 0){
		
		$tabs        = $this->input->post('tabs');
		$uri_string  = $this->uri_string;
		$limit       = 5;
		$uri_segment = $this->uri_segment(); 
		$load_model  = $this->load->model('inventario/Catalogos_model');
		$lts_content = $this->Catalogos_model->get_articulos($limit, $offset);
		$total_rows  = $this->Catalogos_model->get_total_articulos();
		$url         = base_url($uri_string.'articulos/');
		$paginador   = $this->pagination_bootstrap->paginator_generate($total_rows, $url, $limit, $uri_segment);
		

		$tbl_plantilla = array ('table_open'  => '<table class="table table-bordered responsive ">');
		$this->table->set_heading('ID','Articulo', 'clave corta','ID','Articulo', 'clave');
		$this->table->set_template($tbl_plantilla);
		$tabla = $this->table->generate($lts_content);
	  	

		$data_tab['tabla']     = $tabla;
		$data_tab['paginador'] = $paginador;
		$data_tab['item_info'] = $this->pagination_bootstrap->showing_items($limit, $offset, $total_rows);

		
		$view = $this->load_view_unique($uri_string.'articulos/listado', $data_tab, true);
		
		
		if(!$tabs){
			$data['titulo']  = 'Articulos';
			$data['tabs']    = tabbed_tpl($this->config_tabs(),base_url($uri_string),2,$view); 
			$this->load_view($uri_string.'catalogos', $data);
		}else{
			echo json_encode($view);
		}
		
	}

	public function nuevo_articulo(){

		$uri_string  = $this->uri_string;
		$view = $this->load_view_unique($uri_string.'articulos/new', '', true);

		echo json_encode($view);
	//	$data['titulo']  = 'Agregar Articulos';
	//	$data['tabs']    = tabbed_tpl($this->content_tabs(),base_url($uri_string),1,$view); 
	//	$this->load_view($uri_string.'catalogos', $data);
	}


	

	

	
}