<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class almacenes extends Base_Controller 
{
		var $uri_modulo     = 'almacen/';
		var $uri_submodulo  = 'catalogos';
		var $uri_seccion    = 'almacenes';
		var $view_content   = 'content';

	public function __construct(){
		parent::__construct();
		$this->load->model($this->uri_modulo.'catalogos_model');
		$this->lang->load("almacen/catalogos","es_ES");
	}

	public function config_tabs(){
		$pagina =(is_numeric($this->uri_segment_end()) ? $this->uri_segment_end() : "");
		$config_tab['names']    = array($this->lang_item("nuevo_almacenes"), 
										$this->lang_item("listado_almacenes"), 
										$this->lang_item("detalle_almacenes")
								); 
		$config_tab['links']    = array('compras/almacenes/nuevo_almacenes', 
										'compras/almacenes/listado_almacenes/'.$pagina, 
										'detalle_almacenes'
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

	public function index($offset = 0){

		$view_listado_almacen    = $this->listado_almacenes($offset);
		$contenidos_tab           = $view_listado_almacen;

		$data['titulo_seccion']   = $this->lang_item("almacenes");
		$data['titulo_submodulo'] = $this->lang_item("titulo_submodulo");
		$data['icon']             = 'fa fa-database';
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),2,$contenidos_tab);
		
		$js['js'][]     = array('name' => 'articulos', 'dirname' => 'compras');
		$this->load_view($this->uri_view_principal(), $data, $js);

	}
	public function listado_almacenes($offset = 0){
		$data_tab_2  = "";
		$filtro      = ($this->ajax_post('filtro')) ? $this->ajax_post('filtro') : "";
		$uri_view    = $this->uri_modulo.'/listado';
		$limit       = 5;
		$uri_segment = $this->uri_segment(); 
		$lts_content = $this->catalogos_model->get_almacenes($limit, $offset, $filtro);

		$total_rows  = count($this->catalogos_model->get_almacenes($limit, $offset, $filtro, false));
		$url         = base_url($this->uri_modulo.$this->uri_submodulo.'/'.$this->uri_seccion.'/listado_almacenes');
		$paginador   = $this->pagination_bootstrap->paginator_generate($total_rows, $url, $limit, $uri_segment, array('evento_link' => 'onclick', 'function_js' => 'load_content', 'params_js'=>'1'));
	
		if($total_rows>0){
			foreach ($lts_content as $value) {
				$atrr = array(
								'href' => '#',
							  	'onclick' => 'detalle_almacenes('.$value['id_almacen_almacenes'].')'
						);
	
				$tbl_data[] = array('id'             => $value['id_almacen_almacenes'],
									//'presentaciones' => tool_tips_tpl($value['presentacion'], $this->lang_item("tool_tip"), 'right' , $atrr),
									'clave_corta'    => $value['clave_corta'],
									'descripcion'    => $value['descripcion']);
			}

			$tbl_plantilla = array ('table_open'  => '<table class="table table-bordered responsive ">');
		
			$this->table->set_heading(	$this->lang_item("id"),
										//$this->lang_item("presentaciones"),
										$this->lang_item("cvl_corta"),
										$this->lang_item("descripcion"));
			$this->table->set_template($tbl_plantilla);
			$tabla = $this->table->generate($tbl_data);
		}else{
			$msg   = $this->lang_item("msg_query_null");
			$tabla = alertas_tpl('', $msg ,false);
		}
			$data_tab_2['filtro']    = ($filtro!="") ? sprintf($this->lang_item("msg_query_search"),$total_rows , $filtro) : "";
			$data_tab_2['tabla']     = $tabla;
			$data_tab_2['paginador'] = $paginador;
			$data_tab_2['item_info'] = $this->pagination_bootstrap->showing_items($limit, $offset, $total_rows);

			if($this->ajax_post(false)){
				echo json_encode( $this->load_view_unique($uri_view , $data_tab_2, true));
			}else{
				return $this->load_view_unique($uri_view , $data_tab_2, true);
			}
	}
}