<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class catalogo_articulos extends Base_Controller {
	
	var $uri_string  = 'inventario/catalogos/';

	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('is_logged')){
			redirect('login');
		}
		$dominio = $this->session->userdata('dominio');
		$this->load_database($dominio);
		$load_model  = $this->load->model('inventario/catalogos_model');
	}
	
	public function config_tabs(){
		$config_tab['names']    = array('nuevo articulo', 'listado articulos', 'detalle'); 
		$config_tab['links']    = array('agregar_articulo', 'articulos', 'detalle_articulo'); 
		$config_tab['action']   = array('load_content_tab','redirect', '');

		return $config_tab;
	}

	public function articulos($offset = 0){
		
		$post        = $this->ajax_post(false);
		$filtro      = $this->ajax_post('filtro');
		$tabs        = $this->ajax_post('tabs');
		
		$uri_string  = $this->uri_string;
		$limit       = 5;
		$uri_segment = $this->uri_segment(); 
		$lts_content = (!$filtro)?$this->catalogos_model->get_articulos($limit, $offset) : $this->catalogos_model->filtrar_articulos($filtro);
		$total_rows  = (!$filtro)?$this->catalogos_model->get_total_articulos(): count($lts_content);
		$url         = base_url($uri_string.'articulos/');
		$paginador   = $this->pagination_bootstrap->paginator_generate($total_rows, $url, $limit, $uri_segment);
		
		foreach ($lts_content as $value) {
			
			$atrr = array(
							'href' => '#',
						  	'onclick' => 'detalle_articulo('.$value['id_cat_articulo'].')'
					);

			$tbl_data[] = array('id' => $value['id_cat_articulo'],
								'articulo' => tool_tips_tpl($value['articulo'], 'Click para Editar', 'right' , $atrr),
								'clave_corta' => $value['clave_corta'],
								'descripcion' => $value['descripcion']);
		}

		$tbl_plantilla = array ('table_open'  => '<table class="table table-bordered responsive ">');
		$this->table->set_heading('id','articulo', 'clave corta','descripcion');
		$this->table->set_template($tbl_plantilla);
		$tabla = $this->table->generate($tbl_data);
		if($total_rows>0){
			$data_tab['tabla'] = $tabla;
		}else{
			$msg = '<strong>Info!</strong><br>No se encontraron coincidencias.';
			
			$data_tab['tabla'] = alertas_tpl('', $msg ,false);
		}
		
		$data_tab['paginador'] = $paginador;
		$data_tab['item_info'] = $this->pagination_bootstrap->showing_items($limit, $offset, $total_rows);

		$view = $this->load_view_unique($uri_string.'articulos/listado', $data_tab, true);
		
		if(!$post){
			$data['titulo']  = 'Articulos';
			$data['tabs']    = tabbed_tpl($this->config_tabs(),base_url($uri_string),2,$view);

			$data_includes['js'][] = array('name' => 'catalogos', 'dirname' => 'inventario');

			$this->load_view($uri_string.'catalogos', $data, $data_includes);
		}else{
			sleep(1);
			echo json_encode($view);
		}
	}

	public function agregar_articulo(){

		$articulo    = $this->ajax_post('articulo');
		$clave_corta = $this->ajax_post('clave_corta');
		$descripcion = ($this->ajax_post('descripcion')=='')? 'Sin Descripción' : $this->ajax_post('descripcion');
		if($articulo){
			$data_insert = array('articulo'   => text_format_tpl($articulo),
								 'clave_corta'=> text_format_tpl($clave_corta), 
								 'descripcion'=> text_format_tpl($descripcion),
								 'id_usuario' => $this->session->userdata('id_usuario'),
								 'timestamp'  => $this->timestamp());
			
			$insert = $this->catalogos_model->insert_articulos($data_insert);
			if($insert){
				echo 1;
			}else{
				$msg = '<strong>Advertencia!</strong><br>La clave asignada ya se ha proporcionado a otro articulo, porfavor intente con una clave diferente';
				echo json_encode(alertas_tpl('', $msg ,false));
			}
			sleep(1);
		}else{
			$uri_string  = $this->uri_string;
			$view = $this->load_view_unique($uri_string.'articulos/agregar_articulo', '', true);

			echo json_encode($view);
		}
		
	}


	public function detalle_articulo(){
		$uri_string  = $this->uri_string;
		$id_articulo    = $this->ajax_post('id_articulo');
		$result = $this->catalogos_model->detalle_articulos($id_articulo);

		foreach ($result as  $value) {
			$data_detalle['articulo']         = $value['articulo']; 
			$data_detalle['clave_corta']      = $value['clave_corta'];
			$data_detalle['timestamp']        = $value['timestamp'];
			$data_detalle['descripcion']      = $value['descripcion'];
			$data_detalle['usuario_registro'] = $value['id_usuario'];
		}
		$view = $this->load_view_unique($uri_string.'articulos/detalle_articulo', $data_detalle, true);
		echo json_encode($view);
	}

	

	
}