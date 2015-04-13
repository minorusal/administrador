<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Catalogos extends Base_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->removeCache();
		if(!$this->session->userdata('is_logged')){
			redirect('login');
		}
		$dominio = $this->session->userdata('dominio');
		$this->load_database($dominio);
	}
	
	public function content_tabs(){
		$config_tab['names']    = array('nuevo articulo', 'listado articulos', 'detalle'); 
		$config_tab['links']    = array('nuevo', 'articulos', 'otro'); 
		$config_tab['action']   = array('new_art','content', 'other');

		return $config_tab;
	}

	public function articulos($offset = 0){
		
		$tabs        = $this->input->post('tabs');
		$limit       = 5;
		$uri_string  = 'inventario/catalogos/';
		$uri_segment = $this->uri_segment(); 
		$load_model  = $this->load->model('inventario/Catalogos_model');
		$lts_content = $this->Catalogos_model->get_articulos($limit, $offset);
		$total_rows  = $this->Catalogos_model->get_total_articulos();
		$url         = base_url($uri_string.'articulos/');
		$paginador   = $this->pagination_bootstrap->paginator_generate($total_rows, $url, $limit, $uri_segment);
		

		$tbl_plantilla = array ('table_open'  => '<table class="table table-bordered responsive ">');
		$this->table->set_heading('ID','Articulo', 'clave corta','ID','Articulo', 'clave corta');
		$this->table->set_template($tbl_plantilla);
		$tabla = $this->table->generate($lts_content);
	  	

		$data_tab['tabla']     = $tabla;
		$data_tab['paginador'] = $paginador;
		$data_tab['item_info'] = $this->pagination_bootstrap->showing_items($limit, $offset, $total_rows);

		
		$view = $this->load_view_unique($uri_string.'articulos', $data_tab, true);
		
		
		if(!$tabs){
			$data['titulo']  = 'Articulos';
			$data['tabs']    = tabbed_tpl($this->content_tabs(),base_url($uri_string),2,$view); 
			$this->load_view($uri_string.'catalogos', $data);
		}else{
			echo json_encode($view);
		}

		
	}

	

	
}