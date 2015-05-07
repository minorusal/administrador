<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ordenes extends Base_Controller { 
	
	var $uri_modulo     = 'compras/';
	var $uri_submodulo  = 'ordenes';
	var $view_content   = 'content';
	public $Path 		= 'compras/ordenes/';
	
	public function __construct(){
		parent::__construct();
		$this->load->model($this->uri_modulo.'ordenes_model');
		// $this->load->model($this->uri_modulo.'articulos_model');
		// $this->load->model($this->uri_modulo.'catalogos_model');
		$this->lang->load("compras/ordenes","es_ES");
	}

	public function config_tabs(){
		$pagina =(is_numeric($this->uri_segment_end()) ? $this->uri_segment_end() : "");
		$config_tab['names']    = array(
										 $this->lang_item("ordenes_agregar")
										,$this->lang_item("ordenes_listado")
										,$this->lang_item("ordenes_detalle")
								); 
		$config_tab['links']    = array(
										 $this->Path.'ordenes_agregar'
										,$this->Path.'ordenes_listado/'.$pagina
										,'ordenes_detalle'
								); 
		$config_tab['action']   = array(
										 'load_content'
										,'load_content'
										,''
								);
		$config_tab['attr']     = array('','', array('style' => 'display:none'));

		return $config_tab;
	}

	private function uri_view_principal(){
		return $this->uri_modulo.$this->view_content;
	}

	public function index($offset = 0){
		$tabl_inicial 			  = 2;
		$view_listado    		  = $this->ordenes_listado($offset);		
		$contenidos_tab           = $view_listado;
		$data['titulo_seccion']   = $this->lang_item("titulo_seccion");
		$data['titulo_submodulo'] = $this->lang_item("titulo_submodulo");
		$data['icon']             = 'fa fa-archive';
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);
		
		$js['js'][]     = array('name' => 'ordenes', 'dirname' => 'compras');
		$this->load_view($this->uri_view_principal(), $data, $js);
	}

	public function ordenes_listado($offset = 0){
		$data_tab_2  	= "";
		$limit 			= 5;
		$uri_view 		= $this->uri_modulo.'/listado';
		$sqlData = array(
			 'buscar'      	=> ($this->ajax_post('filtro')) ? $this->ajax_post('filtro') : ""
			,'uri_view'   	=> $uri_view
			,'limit'      	=> $limit
			,'offset' 		=> $offset
			,'aplicar_limit'=> false
		);
		$uri_segment = $this->uri_segment(); 
		$lts_content = $this->ordenes_model->get_ordenes($sqlData);
		$total_rows  = count($this->ordenes_model->get_ordenes($sqlData));
		$url         = base_url($sqlData['uri_view']);
		$paginador   = $this->pagination_bootstrap->paginator_generate($total_rows, $url, $sqlData['limit'], $uri_segment, array('evento_link' => 'onclick', 'function_js' => 'load_content', 'params_js'=>'1'));

		if($total_rows>0){
			foreach ($lts_content as $value) {
				$atrr = array(
								'href' => '#',
							  	'onclick' => 'ordenes_detalle('.$value['id_compras_orden'].')'
						);
				
				$tbl_data[] = array('id'             => $value['id_compras_orden'],
									'orden_num'      => tool_tips_tpl($value['orden_num'], $this->lang_item("tool_tip"), 'right' , $atrr),
									'razon_social'   => $value['razon_social'],
									'descripcion'    => tool_tips_tpl($value['descripcion'], $this->lang_item("tool_tip"), 'right' , $atrr)
									);
			}

			$tbl_plantilla = array ('table_open'  => '<table class="table table-bordered responsive ">');
		
			$this->table->set_heading(	$this->lang_item("id"),
										$this->lang_item("orden_num"),
										$this->lang_item("razon_social"),
										$this->lang_item("descripcion"));
			$this->table->set_template($tbl_plantilla);
			$tabla = $this->table->generate($tbl_data);
		}else{
			$msg   = $this->lang_item("msg_query_null");
			$tabla = alertas_tpl('', $msg ,false);
		}
			$data_tab_2['filtro']    = (isset($filtro) && $filtro!="") ? sprintf($this->lang_item("msg_query_search"),$total_rows , $filtro) : "";
			$data_tab_2['tabla']     = $tabla;
			$data_tab_2['paginador'] = $paginador;
			$data_tab_2['item_info'] = $this->pagination_bootstrap->showing_items($limit, $offset, $total_rows);

			if($this->ajax_post(false)){
				echo json_encode( $this->load_view_unique($uri_view , $data_tab_2, true));
			}else{
				return $this->load_view_unique($uri_view , $data_tab_2, true);
			}
	}

		public function ordenes_detalle(){
		$data_tab_3 ='';
		$id_compras_orden = $this->ajax_post('id_compras_orden');
		$ordenes_detalle  = $this->ordenes_model->get_orden_unico($id_compras_orden);
		// dump_var($ordenes_detalle);
		$btn_save       = form_button(array('class'=>"btn btn-primary",'name' => 'update_orden' , 'onclick'=>'update_orden()','content' => $this->lang_item("btn_guardar") ));
		
		$data_tab_3['id_compras_orden']	 = $id_compras_orden;
		$data_tab_3['orden_num']   		 = $this->lang_item("orden_num",false);
		$data_tab_3['orden_num_value']	 = $ordenes_detalle[0]['orden_num'];
        $data_tab_3['razon_social'] 	 = $this->lang_item("razon_social",false);
		$data_tab_3['razon_social_value']= $ordenes_detalle[0]['razon_social'];
        $data_tab_3['descripcion']       = $this->lang_item("descripcion",false);
        $data_tab_3['descripcion_value'] = $ordenes_detalle[0]['descripcion'];
        $data_tab_3['fecha_registro']    = $this->lang_item("fecha_registro",false);
        $data_tab_3['timestamp']         = $ordenes_detalle[0]['timestamp'];
        $data_tab_3['button_save']       = $btn_save;
        
        $this->load_database('global_system');
        $this->load->model('users_model');
        
        $usuario_registro               = $this->users_model->search_user_for_id($ordenes_detalle[0]['id_usuario']);
        $data_tab_3['registro_por']    	= $this->lang_item("registro_por",false);
        $data_tab_3['usuario_registro'] = text_format_tpl($usuario_registro[0]['name'],"u");
		$uri_view    = $this->uri_modulo.$this->uri_submodulo.'/ordenes_detalle';
		echo json_encode( $this->load_view_unique($uri_view ,$data_tab_3, true));
	}

	// public function agregar_orden(){
		
	// 	$uri_view       = $this->uri_modulo.$this->uri_submodulo.'/agregar_orden';
	// 	$presentaciones = dropdown_tpl($this->catalogos_model->get_presentaciones('','','',false), '' ,'id_cat_presentaciones', array('clave_corta', 'presentaciones'),"lts_presentaciones", "requerido");
	// 	$lineas         = dropdown_tpl($this->catalogos_model->get_lineas('','',false), '' ,'id_cat_linea', array('clave_corta','linea'),"lts_lineas", "requerido");
	// 	$um             = dropdown_tpl($this->catalogos_model->get_um('','',false), '' ,'id_cat_um', array('clave_corta','um'),"lts_um", "requerido");
	// 	$marcas         = dropdown_tpl($this->catalogos_model->get_marcas('','',false), '' ,'id_cat_marcas', array('clave_corta','marcas'),"lts_marcas", "requerido");
	// 	$btn_save       = form_button(array('class'=>"btn btn-primary",'name' => 'save_orden','onclick'=>'insert_orden()' , 'content' => $this->lang_item("btn_guardar") ));
	// 	$btn_reset      = form_button(array('class'=>"btn btn-primary",'name' => 'reset','value' => 'reset','onclick'=>'clean_formulario()','content' => $this->lang_item("btn_limpiar")));

 //        $data_tab_1['nombre_orden']   = $this->lang_item("nombre_orden",false);
 //        $data_tab_1['cvl_corta']         = $this->lang_item("cvl_corta",false);
 //        $data_tab_1['marca']             = $this->lang_item("marca",false);
 //        $data_tab_1['presentacion']      = $this->lang_item("presentacion",false);
 //        $data_tab_1['linea']             = $this->lang_item("linea",false);
 //        $data_tab_1['um']                = $this->lang_item("um",false);
 //        $data_tab_1['descripcion']       = $this->lang_item("descripcion",false);
 //        $data_tab_1['list_marca']        = $marcas;
 //        $data_tab_1['list_presentacion'] = $presentaciones;
 //        $data_tab_1['list_linea']        = $lineas;
 //        $data_tab_1['list_um']           = $um;
 //        $data_tab_1['button_save']       = $btn_save;
 //        $data_tab_1['button_reset']      = $btn_reset;

 //        if($this->ajax_post(false)){
	// 			echo json_encode($this->load_view_unique($uri_view , $data_tab_1, true));
	// 	}else{
	// 		return $this->load_view_unique($uri_view , $data_tab_1, true);
	// 	}
	// }


	// public function insert_orden(){
	// 	$incomplete  = $this->ajax_post('incomplete');
	// 	if($incomplete>0){
	// 		$msg = $this->lang_item("msg_campos_obligatorios",false);
	// 		echo json_encode('0|'.alertas_tpl('error', $msg ,false));
	// 	}else{
	// 		$orden     = $this->ajax_post('orden');
	// 		$clave_corta  = $this->ajax_post('clave_corta');
	// 		$presentacion = $this->ajax_post('presentacion');
	// 		$linea        = $this->ajax_post('linea');
	// 		$um           = $this->ajax_post('um');
	// 		$marca        = $this->ajax_post('marca');
	// 		$descripcion  = ($this->ajax_post('descripcion')=='')? $this->lang_item("sin_descripcion") : $this->ajax_post('descripcion');
	// 		$data_insert = array('ordenes' => $orden,
	// 							 'clave_corta'=> $clave_corta, 
	// 							 'descripcion'=> $descripcion,
	// 							 'id_cat_linea'=> $linea,
	// 							 'id_cat_marcas'=> $marca,
	// 							 'id_cat_presentaciones'=> $presentacion,
	// 							 'id_cat_um'=> $um,
	// 							 'id_usuario' => $this->session->userdata('id_usuario'),
	// 							 'timestamp'  => $this->timestamp());
	// 		$insert = $this->ordenes_model->insert_orden($data_insert);

	// 		if($insert){
	// 			$msg = $this->lang_item("msg_insert_success",false);
	// 			echo json_encode('1|'.alertas_tpl('success', $msg ,false));
	// 		}else{
	// 			$msg = $this->lang_item("msg_err_clv",false);
	// 			echo json_encode('0|'.alertas_tpl('', $msg ,false));
	// 		}
	// 	}
	// }


	// public function update_orden(){
	// 	$arrayData = array(
	// 			'descripcion' => $this->ajax_post('descripcion')
	// 		);
	// 	echo json_encode($arrayData);
	// }

	public function update_orden(){
		$incomplete  = $this->ajax_post('incomplete');
		if($incomplete>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			echo json_encode('0|'.alertas_tpl('error', $msg ,false));
		}else{
			$sqlData = array(
						 'id_compras_orden'	=> $this->ajax_post('id_compras_orden')
						,'orden_num' 		=> $this->ajax_post('orden_num')
						// ,'razon_social' 	=> $this->ajax_post('razon_social')
						,'descripcion'		=> $this->ajax_post('descripcion')
						);
			$insert = $this->ordenes_model->update_orden($sqlData);
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