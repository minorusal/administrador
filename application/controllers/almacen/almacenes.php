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
		$config_tab['links']    = array('almacen/almacenes/agregar_almacen', 
										'almacen/almacenes/listado_almacenes/'.$pagina, 
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
		
		$js['js'][]     = array('name' => 'almacenes', 'dirname' => 'almacen');
		$this->load_view($this->uri_view_principal(), $data, $js);

	}
	public function listado_almacenes($offset = 0)
	{
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
									//'clave_corta'    => $value['clave_corta'],
									'clave_corta'    => tool_tips_tpl($value['clave_corta'], $this->lang_item("tool_tip"), 'right' , $atrr),
									'descripcion'    => $value['descripcion']);
			}

			$tbl_plantilla = array ('table_open'  => '<table class="table table-bordered responsive ">');
		
			$this->table->set_heading(	$this->lang_item("id"),
										$this->lang_item("almacenes"),
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

	public function detalle_almacenes(){

		$uri_view              = $this->uri_modulo.$this->uri_submodulo.'/'.$this->uri_seccion.'/almacenes_edit';
		$id_almacen            = $this->ajax_post('id_almacen');
		$detalle_almacenes     = $this->catalogos_model->get_almacenes_unico($id_almacen);
		$btn_save              = form_button(array('class'=>"btn btn-primary",'name' => 'update_almacenes' , 'onclick'=>'update_almacenes()','content' => $this->lang_item("btn_guardar") ));
		
		$data_tab_3['id_almacen']            = $id_almacen;
        $data_tab_3["nombre_almacen"]        = $this->lang_item("Nombre almacen");
		$data_tab_3["cvl_corta"]        	 = $this->lang_item("cvl_corta");
		$data_tab_3["descrip"]         	     = $this->lang_item("descripcion");
		$data_tab_3["registro_por"]    	     = $this->lang_item("registro_por");
		$data_tab_3["fecha_registro"]        = $this->lang_item("fecha_registro");
        $data_tab_3['almacen']               = $detalle_almacenes[0]['almacenes'];
		$data_tab_3['clave_corta']           = $detalle_almacenes[0]['clave_corta'];
        $data_tab_3['descripcion']           = $detalle_almacenes[0]['descripcion'];
        $data_tab_3['timestamp']             = $detalle_almacenes[0]['timestamp'];
        $data_tab_3['button_save']           = $btn_save;
        
        $this->load_database('global_system');
        $this->load->model('users_model');
        	
        $usuario_registro               = $this->users_model->search_user_for_id($detalle_almacenes[0]['id_usuario']);
        $data_tab_3['usuario_registro'] = text_format_tpl($usuario_registro[0]['name'],"u");
		echo json_encode( $this->load_view_unique($uri_view ,$data_tab_3, true));
	}

	public function agregar_almacen(){
		
		$uri_view       = $this->uri_modulo.$this->uri_submodulo.'/'.$this->uri_seccion.'/almacenes_save';
		$btn_save       = form_button(array('class'=>"btn btn-primary",'name' => 'save_almacen','onclick'=>'insert_almacen()' , 'content' => $this->lang_item("btn_guardar") ));
		$btn_reset      = form_button(array('class'=>"btn btn-primary",'name' => 'reset','value' => 'reset','onclick'=>'clean_formulario()','content' => $this->lang_item("btn_limpiar")));

		$data_tab_1["nombre_almacenes"] = $this->lang_item("nombre_almacenes");
		$data_tab_1["cvl_corta"]             = $this->lang_item("cvl_corta");
		$data_tab_1["descrip"]               = $this->lang_item("descripcion");

        $data_tab_1['button_save']       = $btn_save;
        $data_tab_1['button_reset']      = $btn_reset;

        if($this->ajax_post(false)){
				echo json_encode($this->load_view_unique($uri_view , $data_tab_1, true));
		}else{
			return $this->load_view_unique($uri_view , $data_tab_1, true);
		}
	}

	public function insert_almacen(){
		$incomplete  = $this->ajax_post('incomplete');
		if($incomplete>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			echo json_encode('0|'.alertas_tpl('error', $msg ,false));
		}else{
			
			$almacen = $this->ajax_post('almacen');
			$clave_corta  = $this->ajax_post('clave_corta');
			$descripcion  = ($this->ajax_post('descripcion')=='')? $this->lang_item("sin_descripcion") : $this->ajax_post('descripcion');
			$data_insert = array('almacen' => $presentacion,
								 'clave_corta'    => $clave_corta, 
								 'descripcion'    => $descripcion,
								 'id_usuario'     => $this->session->userdata('id_usuario'),
								 'timestamp'      => $this->timestamp());
			$insert = $this->catalogos_model->insert_almacen($data_insert);

			if($insert){
				$msg = $this->lang_item("msg_insert_success",false);
				echo json_encode('1|'.alertas_tpl('success', $msg ,false));
			}else{
				$msg = $this->lang_item("msg_err_clv",false);
				echo json_encode('0|'.alertas_tpl('', $msg ,false));
			}
		}
	}
}