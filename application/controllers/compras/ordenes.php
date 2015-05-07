<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ordenes extends Base_Controller { 

	private $modulo;
	private $submodulo;
	private $uri_modulo;
	private $uri_submodulo;
	private $view_content;
	private $path;
	private $icon;

	private $offset, $limit_max;
	private $tab, $tab1, $tab2, $tab3;
	
	public function __construct(){
		parent::__construct();
		$this->modulo 			= 'compras';
		$this->submodulo		= 'ordenes';
		$this->icon 			= 'fa fa-archive'; #Icono de modulo
		$this->uri_modulo 		= $this->modulo.'/';
		$this->uri_submodulo 	= $this->submodulo;
		$this->path 			= $this->modulo.'/'.$this->submodulo.'/';
		$this->view_content 	= 'content';
		$this->limit_max		= 5;
		$this->offset			= 0;
		// Tabs
		$this->tab1 			= 'agregar';
		$this->tab2 			= 'listado';
		$this->tab3 			= 'detalle';
		// DB Model
		$this->load->model($this->uri_modulo.$this->submodulo.'_model','db_model');
			// $this->load->model($this->uri_modulo.'articulos_model');
			// $this->load->model($this->uri_modulo.'catalogos_model');
		// Diccionario
		$this->lang->load($this->uri_modulo.$this->submodulo,"es_ES");
	}

	public function config_tabs(){
		$tab_1 	= $this->tab1;
		$tab_2 	= $this->tab2;
		$tab_3 	= $this->tab3;
		$path  	= $this->path;
		$pagina =(is_numeric($this->uri_segment_end()) ? $this->uri_segment_end() : "");
		// Nombre de Tabs
		$config_tab['names']    = array(
										 $this->lang_item($tab_1)
										,$this->lang_item($tab_2)
										,$this->lang_item($tab_3)
								); 
		// Href de tabs
		$config_tab['links']    = array(
										 $path.$tab_1
										,$path.$tab_2.'/'.$pagina
										,$tab_3
								); 
		// Accion de tabs
		$config_tab['action']   = array(
										 'load_content'
										,'load_content'
										,''
								);
		// Atributos 
		$config_tab['attr']     = array('','', array('style' => 'display:none'));
		return $config_tab;
	}

	private function uri_view_principal(){
		return $this->uri_modulo.$this->view_content;
	}

	public function index(){
		$tabl_inicial 			  = 2;
		$view_listado    		  = $this->listado();		
		$contenidos_tab           = $view_listado;
		$data['titulo_seccion']   = $this->lang_item("titulo_seccion");
		$data['titulo_submodulo'] = $this->lang_item("titulo_submodulo");
		$data['icon']             = $this->icon;
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);	
		$js['js'][]  = array('name' => $this->submodulo, 'dirname' => $this->modulo);
		$this->load_view($this->uri_view_principal(), $data, $js);
	}

	public function listado($offset=0){
		$seccion 		= 'listado';
		$tab_detalle	= $this->tab3;
		$limit 			= $this->limit_max;
		$uri_view 		= $this->uri_modulo.$seccion;
		$url_link 		= $this->path.$seccion;		
		$sqlData = array(
			 'buscar'      	=> ($this->ajax_post('filtro')) ? $this->ajax_post('filtro') : ""
			,'offset' 		=> $offset
			,'limit'      	=> $limit
		);
		$uri_segment  = $this->uri_segment(); 
		$total_rows	  = count($this->db_model->db_get_total_rows($sqlData));
		$sqlData['aplicar_limit'] = true;	
		$list_content = $this->db_model->db_get_data($sqlData);
		$url          = base_url($url_link);
		$paginador    = $this->pagination_bootstrap->paginator_generate($total_rows, $url, $limit, $uri_segment, array('evento_link' => 'onclick', 'function_js' => 'load_content', 'params_js'=>'1'));

		if($total_rows>0){
			foreach ($list_content as $value) {
				// Evento de enlace
				$atrr = array(
								'href' => '#',
							  	'onclick' => $tab_detalle.'('.$value['id_compras_orden'].')'
						);
				// Datos para tabla
				$tbl_data[] = array('id'             => $value['id_compras_orden'],
									'orden_num'      => tool_tips_tpl($value['orden_num'], $this->lang_item("tool_tip"), 'right' , $atrr),
									'razon_social'   => $value['razon_social'],
									'descripcion'    => tool_tips_tpl($value['descripcion'], $this->lang_item("tool_tip"), 'right' , $atrr)
									);
			}
			// Plantilla
			$tbl_plantilla = array ('table_open'  => '<table class="table table-bordered responsive ">');
			// Titulos de tabla
			$this->table->set_heading(	$this->lang_item("id"),
										$this->lang_item("orden_num"),
										$this->lang_item("razon_social"),
										$this->lang_item("descripcion"));
			// Generar tabla
			$this->table->set_template($tbl_plantilla);
			$tabla = $this->table->generate($tbl_data);
		}else{
			$msg   = $this->lang_item("msg_query_null");
			$tabla = alertas_tpl('', $msg ,false);
		}
			$tabData['filtro']    = (isset($filtro) && $filtro!="") ? sprintf($this->lang_item("msg_query_search"),$total_rows , $filtro) : "";
			$tabData['tabla']     = $tabla;
			$tabData['paginador'] = $paginador;
			$tabData['item_info'] = $this->pagination_bootstrap->showing_items($limit, $offset, $total_rows);

			if($this->ajax_post(false)){
				echo json_encode( $this->load_view_unique($uri_view , $tabData, true));
			}else{
				return $this->load_view_unique($uri_view , $tabData, true);
			}
	}

		public function detalle(){
		$seccion 		= 'detalle';
		$tab_detalle	= $this->tab3;
		$id_compras_orden = $this->ajax_post('id_compras_orden');
		$detalle  		= $this->db_model->get_orden_unico($id_compras_orden);
		$btn_save       = form_button(array('class'=>"btn btn-primary",'name' => 'actualizar' , 'onclick'=>'actualizar()','content' => $this->lang_item("btn_guardar") ));
		$tabData['id_compras_orden']	= $id_compras_orden;
		$tabData['orden_num']   		= $this->lang_item("orden_num",false);
		$tabData['orden_num_value']	 	= $detalle[0]['orden_num'];
        $tabData['razon_social'] 	 	= $this->lang_item("razon_social",false);
		$tabData['razon_social_value']	= $detalle[0]['razon_social'];
        $tabData['descripcion']       	= $this->lang_item("descripcion",false);
        $tabData['descripcion_value'] 	= $detalle[0]['descripcion'];
        $tabData['fecha_registro']    	= $this->lang_item("fecha_registro",false);
        $tabData['timestamp']         	= $detalle[0]['timestamp'];
        $tabData['button_save']       	= $btn_save;
        
        $this->load_database('global_system');
        $this->load->model('users_model');
        
        $usuario_registro               = $this->users_model->search_user_for_id($detalle[0]['id_usuario']);
        $tabData['registro_por']    	= $this->lang_item("registro_por",false);
        $tabData['usuario_registro']	= text_format_tpl($usuario_registro[0]['name'],"u");
		$uri_view   					= $this->path.$this->submodulo.'_'.$seccion;
		echo json_encode( $this->load_view_unique($uri_view ,$tabData, true));
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
	// 		$insert = $this->db_model->insert_orden($data_insert);

	// 		if($insert){
	// 			$msg = $this->lang_item("msg_insert_success",false);
	// 			echo json_encode('1|'.alertas_tpl('success', $msg ,false));
	// 		}else{
	// 			$msg = $this->lang_item("msg_err_clv",false);
	// 			echo json_encode('0|'.alertas_tpl('', $msg ,false));
	// 		}
	// 	}
	// }

	public function actualizar(){
		$incomplete  = $this->ajax_post('incomplete');
		if($incomplete>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			$json_respuesta = array(
						 'id' 		=> 0
						,'contenido'=> alertas_tpl('error', $msg ,false)
						,'success' 	=> false
				);

		}else{
			$sqlData = array(
						 'id_compras_orden'	=> $this->ajax_post('id_compras_orden')
						,'orden_num' 		=> $this->ajax_post('orden_num')
						// ,'razon_social' 	=> $this->ajax_post('razon_social')
						,'descripcion'		=> $this->ajax_post('descripcion')
						);
			$insert = $this->db_model->db_update_data($sqlData);
			if($insert){
				$msg = $this->lang_item("msg_insert_success",false);
				$json_respuesta = array(
						 'id' 		=> 1
						,'contenido'=> alertas_tpl('success', $msg ,false)
						,'success' 	=> true
				);
			}else{
				$msg = $this->lang_item("msg_err_clv",false);
				$json_respuesta = array(
						 'id' 		=> 0
						,'contenido'=> alertas_tpl('', $msg ,false)
						,'success' 	=> false
				);
			}
		}
		echo json_encode($json_respuesta);
	}
}