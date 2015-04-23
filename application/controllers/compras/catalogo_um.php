<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class catalogo_um extends Base_Controller {
	
	var $uri_string   = 'compras/catalogos/';
	var $view_content =  'compras/content';
	public function __construct(){
		parent::__construct();
		$load_model  = $this->load->model('compras/catalogos_model');
		$this->lang->load("compras/catalogos","es_ES");
	}
	
	public function config_tabs(){
		$config_tab['names']    = array($this->lang_item("nueva_um", false), $this->lang_item("listado_um"), $this->lang_item("detalle_um")); 
		$config_tab['links']    = array('agregar_um', 'um', 'detalle_um'); 
		$config_tab['action']   = array('load_content_tab','redirect', '');
		$config_tab['attr']     = array('','', array('style' => 'display:none'));

		return $config_tab;
	}

	public function um($offset = 0){
		
		$post        = $this->ajax_post(false);
		$filtro      = $this->ajax_post('filtro');
		$tabs        = $this->ajax_post('tabs');
		$view_content= $this->view_content;
		$uri_string  = $this->uri_string;
		$limit       = 5;
		$uri_segment = $this->uri_segment(); 
		$lts_content = (!$filtro)?$this->catalogos_model->get_um($limit, $offset) : $this->catalogos_model->filtrar_um($filtro);
		$total_rows  = (!$filtro)?$this->catalogos_model->get_total_um(): count($lts_content);
		$url         = base_url($uri_string.'um/');
		$paginador   = $this->pagination_bootstrap->paginator_generate($total_rows, $url, $limit, $uri_segment);
		
		if($total_rows>0){
			foreach ($lts_content as $value) {
				
				$atrr = array(
								'href' => '#',
							  	'onclick' => 'detalle_um('.$value['id_cat_um'].')'
						);

				$tbl_data[] = array('id' => $value['id_cat_um'],
									'um' => tool_tips_tpl($value['um'], $this->lang_item("tool_tip"), 'right' , $atrr),
									'clave_corta' => $value['clave_corta'],
									'descripcion' => $value['descripcion']);
			}

			$tbl_plantilla = array ('table_open'  => '<table class="table table-bordered responsive ">');
		
			$this->table->set_heading($this->lang_item("id"),$this->lang_item("um"),$this->lang_item("cvl_corta"),$this->lang_item("descripcion"));
			$this->table->set_template($tbl_plantilla);
			$tabla = $this->table->generate($tbl_data);
		
			$data_tab['tabla'] = $tabla;
		}else{
			$msg = $this->lang_item("msg_searh_fail", false);
			
			$data_tab['tabla'] = alertas_tpl('', $msg ,false);
		}
		
		$data_tab['paginador'] = $paginador;
		$data_tab['item_info'] = $this->pagination_bootstrap->showing_items($limit, $offset, $total_rows);

		$view = $this->load_view_unique($uri_string.'um/listado_um', $data_tab, true);
		
		if(!$post){

			$data['titulo_seccion']   = $this->lang_item("um");
			$data['titulo_submodulo'] = $this->lang_item("titulo_submodulo");
			$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url($uri_string),2,$view);
			$data['icon']             = 'iconfa-book';
			$data_includes['js'][]    = array('name' => 'catalogo_um', 'dirname' => 'compras');

			$this->load_view($view_content, $data, $data_includes);
		}else{
			echo json_encode($view);
		}
	}

	public function agregar_um(){
		
		if($this->ajax_post('ajax')){
			$um       = $this->ajax_post('um');
			$clave_corta = $this->ajax_post('clave_corta');
			if(($um)&&($clave_corta)){
				$descripcion = ($this->ajax_post('descripcion')=='')? $this->lang_item("sin_descripcion") : $this->ajax_post('descripcion');

				$data_insert = array('um'      => text_format_tpl($um),
									 'clave_corta'=> text_format_tpl($clave_corta), 
									 'descripcion'=> text_format_tpl($descripcion),
									 'id_usuario' => $this->session->userdata('id_usuario'),
									 'timestamp'  => $this->timestamp());
				
				$insert = $this->catalogos_model->insert_um($data_insert);
				if($insert){
					$msg = $this->lang_item("msg_update_success",false);
					echo json_encode(alertas_tpl('success', $msg ,false));
				}else{
					$msg = $this->lang_item("msg_err_clv",false);
					echo json_encode(alertas_tpl('', $msg ,false));
				}

			}else{
				$msg = $this->lang_item("msg_campos_obligatorios",false);
				echo json_encode(alertas_tpl('error', $msg ,false));
			}
			
		}else{
			$uri_string  = $this->uri_string;


			$data["nombre_um"]    = $this->lang_item("um", false);
			$data["cvl_corta"]       = $this->lang_item("cvl_corta");
			$data["descrip"]         = $this->lang_item("descripcion");
			$data["guardar"]         = $this->lang_item("btn_guardar");
			$data["limpiar"]         = $this->lang_item("btn_limpiar");

			$view = $this->load_view_unique($uri_string.'um/agregar_um', $data, true);

			echo json_encode($view);
		}
	}


	public function detalle_um(){

		$uri_string  = $this->uri_string;
		$id_um = $this->ajax_post('id_um');
		$detalles    = $this->catalogos_model->detalle_um($id_um);
		
		$data_detalle['id_um']         = $id_um;
		$data_detalle['um']            = $detalles[0]['um']; 
		$data_detalle['clave_corta']      = $detalles[0]['clave_corta'];
		$data_detalle['timestamp']        = $detalles[0]['timestamp'];
		$data_detalle['descripcion']      = $detalles[0]['descripcion'];

		$data_detalle["nombre_um"]     = $this->lang_item("um");
		$data_detalle["cvl_corta"]        = $this->lang_item("cvl_corta");
		$data_detalle["descrip"]          = $this->lang_item("descripcion");
		$data_detalle["registro_por"]     = $this->lang_item("registro_por");
		$data_detalle["fecha_registro"]   = $this->lang_item("fecha_registro");
		$data_detalle["guardar"]          = $this->lang_item("btn_guardar");

		$this->load_database('global_system');
		$load_model        = $this->load->model('users_model');
		$usuario_registro  = $this->users_model->search_user_for_id($detalles[0]['id_usuario']);
		$data_detalle['usuario_registro'] = text_format_tpl($usuario_registro[0]['name']);
		
		$view = $this->load_view_unique($uri_string.'um/detalle_um', $data_detalle, true);
		echo json_encode($view);
	}


	public function actualizar_um(){


		$um    = $this->ajax_post('um');
		$clave_corta = $this->ajax_post('clave_corta');
			if(($um)&&($clave_corta)){

				$id_um = $this->ajax_post('id_um');
				
				$descripcion = ($this->ajax_post('descripcion')=='')? $this->lang_item("sin_descripcion") : $this->ajax_post('descripcion');
				
				$data_update = array('um'   => text_format_tpl($um),
									 'clave_corta'=> text_format_tpl($clave_corta), 
									 'descripcion'=> text_format_tpl($descripcion));
				
				$update = $this->catalogos_model->update_um($data_update, $id_um);
				if($update){
					$msg = $this->lang_item("msg_update_success",false);
					echo json_encode(alertas_tpl('success', $msg ,false));
				}else{
					$msg = $this->lang_item("msg_err_clv",false);
					echo json_encode(alertas_tpl('', $msg ,false));
				}
			}else{
				$msg = $this->lang_item("msg_campos_obligatorios",false);
				echo json_encode(alertas_tpl('error', $msg ,false));
			}

		
	}

	
}