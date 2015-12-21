<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class horarios_servicio extends Base_Controller{
	private $modulo;
	private $seccion;
	private $view_content;
	private $path;
	private $icon;

	private $offset, $limit_max;
	private $tab, $tab1, $tab2, $tab3;

	public function __construct(){
		parent::__construct();
		$this->modulo        = 'sucursales';
		$this->seccion       = 'horarios_servicio';
		$this->icon          = 'fa fa-clock-o'; 
		$this->path          = $this->modulo.'/'.$this->seccion.'/'; 
		$this->view_content  = 'content';
		$this->limit_max     = 10;
		$this->offset        = 0;
		// Tabs
		$this->tab1          = 'agregar';
		$this->tab2          = 'listado';
		$this->tab3          = 'detalle';
		// DB Model
		$this->load->model($this->modulo.'/'.$this->seccion.'_model','db_model');
		$this->load->model($this->modulo.'/listado_sucursales_model','db_model2');
		// Diccionario
		$this->lang->load($this->modulo.'/'.$this->seccion,"es_ES");
	}

	public function config_tabs(){
		$tab_1   = $this->tab1;
		$tab_2   = $this->tab2;
		$tab_3   = $this->tab3;
		$path    = $this->path;
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
		return $this->modulo.'/'.$this->view_content;
	}

	public function index(){
		$tabl_inicial          = 2;
		$view_listado          = $this->listado();   
		$contenidos_tab           = $view_listado;
		$data['titulo_seccion']   = $this->lang_item('titulo_seccion');
		$data['titulo_modulo']    = $this->lang_item("titulo_submodulo");
		$data['icon']             = $this->icon;
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);   
		
		$js['js'][]  = array('name' => $this->seccion, 'dirname' => $this->modulo);
		$this->load_view($this->uri_view_principal(), $data, $js);
	}

	public function listado($offset=0){
		$seccion       = '/listado';
		$tab_detalle   = $this->tab3; 
		$limit         = $this->limit_max;
		$uri_view      = $this->modulo.$seccion;
		$url_link      = $this->path.'listado';
		$filtro        = ($this->ajax_post('filtro')) ? $this->ajax_post('filtro') : "";

		$sqlData = array(
			 'buscar'         => $filtro
			,'offset'      => $offset
			,'limit'       => $limit
		);
		$uri_segment  = $this->uri_segment(); 
		$total_rows   = count($this->db_model->db_get_data($sqlData));
		$sqlData['aplicar_limit'] = false;
		$list_content = $this->db_model->db_get_data($sqlData);
		$url          = base_url($url_link);
		$paginador    = $this->pagination_bootstrap->paginator_generate($total_rows, $url, $limit, $uri_segment, array('evento_link' => 'onclick', 'function_js' => 'load_content', 'params_js'=>'1'));
		if($total_rows){
			foreach ($list_content as $value){
			// Evento de enlace
				$atrr = array(
								'href' => '#',
								'onclick' => $tab_detalle.'('.$value['id_administracion_servicio'].')'
						);
				// Datos para tabla
				$tbl_data[] = array('id'            => $value['id_administracion_servicio'],
									'servicio'      => tool_tips_tpl($value['servicio'], $this->lang_item("tool_tip"), 'right' , $atrr),
									'clave_corta'   => $value['cv_servicio'],
									'sucursal'      => $value['sucursal'],
									'descripcion'   => $value['descripcion'],
									'inicio'        => $value['inicio'],
									'final'         => $value['final']
									);
			}
			// Plantilla
			$tbl_plantilla = set_table_tpl();
			// Titulos de tabla
			$this->table->set_heading(  $this->lang_item("lbl_id"),
										$this->lang_item("lbl_servicio"),
										$this->lang_item("lbl_clave_corta"),
										$this->lang_item("lbl_sucursal"),
										$this->lang_item("lbl_descripcion"),
										$this->lang_item("lbl_inicio"),
										$this->lang_item("lbl_final"));
			// Generar tabla
			$this->table->set_template($tbl_plantilla);
			$tabla = $this->table->generate($tbl_data);
			$buttonTPL = array( 'text'       => $this->lang_item("btn_xlsx"), 
								'iconsweets' => 'fa fa-file-excel-o',
								'href'       => base_url($this->path.'export_xlsx?filtro='.base64_encode($filtro))
								);          
		}else{
			$buttonTPL = "";
			$msg   = $this->lang_item("msg_query_null");
			$tabla = alertas_tpl('', $msg ,false);
		}
		$tabData['filtro']    = (isset($filtro) && $filtro!="") ? sprintf($this->lang_item("msg_query_search",false),$total_rows , $filtro) : "";
		$tabData['tabla']     = $tabla;
		$tabData['export']    = button_tpl($buttonTPL);
		$tabData['paginador'] = $paginador;
		$tabData['item_info'] = $this->pagination_bootstrap->showing_items($limit, $offset, $total_rows);
		if($this->ajax_post(false)){
			echo json_encode( $this->load_view_unique($uri_view , $tabData, true));
		}else{
			return $this->load_view_unique($uri_view , $tabData, true);
		}
	}

	public function detalle(){
		$id_servicio                 = $this->ajax_post('id_servicio');
		$detalle                     = $this->db_model->get_orden_unico_servicio($id_servicio);
		$seccion                     = 'detalle';
		$tab_detalle                 = $this->tab3;
		$sqlData = array(
			 'buscar'       => ''
			,'offset'       => 0
			,'limit'        => 0
		);
		$sucursales_array     = array(
					 'data'     => $this->db_model2->db_get_data($sqlData)
					,'value'    => 'id_sucursal'
					,'text'     => array('sucursal')
					,'name'     => "lts_sucursales"
					,'class'    => "requerido"
					,'selected' => $detalle[0]['id_sucursal']
					);
		$sucursales                        = dropdown_tpl($sucursales_array);
		$btn_save                          = form_button(array('class'=>"btn btn-primary",'name' => 'actualizar' , 'onclick'=>'actualizar()','content' => $this->lang_item("btn_guardar") ));   
		$tabData['id_servicio']            = $id_servicio;
		$tabData["lbl_servicio"]           = $this->lang_item("lbl_servicio");
		$tabData["lbl_clave_corta"]        = $this->lang_item("lbl_clave_corta");
		$tabData["lbl_inicio"]             = $this->lang_item("lbl_inicio");
		$tabData["lbl_final"]              = $this->lang_item("lbl_final");
		$tabData["lbl_sucursal"]           = $this->lang_item("lbl_sucursal");
		$tabData["lbl_descripcion"]        = $this->lang_item("lbl_descripcion");

		$tabData['txt_servicio']           = $detalle[0]['servicio'];
		$tabData['txt_clave_corta']        = $detalle[0]['clave_corta'];
		$tabData['timepicker1']            = substr($detalle[0]['inicio'], 0,5);
		$tabData['timepicker2']            = substr($detalle[0]['final'], 0,5);
		$tabData["list_sucursal"]          = $sucursales;

		$tabData['txt_descripcion']        = $detalle[0]['descripcion'];
		
		$tabData['lbl_ultima_modificacion'] = $this->lang_item('lbl_ultima_modificacion', false);
		$tabData['val_fecha_registro']      = $detalle[0]['timestamp'];
		$tabData['lbl_fecha_registro']      = $this->lang_item('lbl_fecha_registro', false);
		$tabData['lbl_usuario_registro']    = $this->lang_item('lbl_usuario_registro', false);
		  
		$this->load_database('global_system');
		$this->load->model('users_model');

		$usuario_registro                  = $this->users_model->search_user_for_id($detalle[0]['id_usuario']);
		$usuario_name                      = text_format_tpl($usuario_registro[0]['name'],"u");
		$tabData['val_usuarios_registro']  = $usuario_name;

		if($detalle[0]['edit_id_usuario']){
			$usuario_registro                   = $this->users_model->search_user_for_id($detalle[0]['edit_id_usuario']);
			$usuario_name                   = text_format_tpl($usuario_registro[0]['name'],"u");
			$tabData['val_ultima_modificacion'] = sprintf($this->lang_item('val_ultima_modificacion', false), $this->timestamp_complete($detalle[0]['edit_timestamp']), $usuario_name);
		}else{
			$usuario_name = '';
			$tabData['val_ultima_modificacion'] = $this->lang_item('lbl_sin_modificacion', false);
		}

		$tabData['button_save']       = $btn_save;
		$tabData['registro_por']      = $this->lang_item("registro_por",false);
		$tabData['usuario_registro']  = $usuario_name;
												
		$uri_view   				  = $this->modulo.'/'.$this->seccion.'/horarios_servicio_detalle';
		echo json_encode( $this->load_view_unique($uri_view ,$tabData, true));
	}

	public function actualizar(){
		$objData    = $this->ajax_post('objData');
		if($objData['incomplete']>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			echo json_encode(array(  'success'=>'false', 'mensaje' => alertas_tpl('error', $msg ,false)));
		}else{
			$id_servicio  =  $objData['id_servicio'];
			$id_sucursal  =  $objData['lts_sucursales'];
			$ajax_inicio  =  $objData['timepicker1'];
			$ajax_termino =  $objData['timepicker2'];
			$servicios    =  $this->db_model->db_get_data_x_sucursal($id_sucursal,$id_servicio);
			$check_times  =  $this->check_times_ranges($ajax_inicio,$ajax_termino, $servicios);
			
			if($check_times['response']){
				$sqlData = array(
					 'id_administracion_servicio'   => $id_servicio
					,'servicio'                   => $objData['txt_servicio']
					,'clave_corta'                => $objData['txt_clave_corta']
					,'descripcion'                => $objData['txt_descripcion']
					,'inicio'                     => $ajax_inicio
					,'final'                      => $ajax_termino
					,'id_sucursal'                => $id_sucursal
					,'edit_timestamp'             => $this->timestamp()
					,'edit_id_usuario'            => $this->session->userdata('id_usuario')
					);
				
				$insert = $this->db_model->db_update_data($sqlData);

				if($insert){
					$msg = $this->lang_item("msg_update_success",false);
					echo json_encode(array(  'success'=>'true', 'mensaje' => $msg ));
				}
				else{
					$msg = $this->lang_item("msg_err_clv",false);
					echo json_encode( array( 'success'=>'false', 'mensaje' =>alertas_tpl('', $msg ,false)));
				}
			}else{
				$msg = $this->lang_item("msg_horario_empalmado",false);
				echo json_encode( array( 'success'=>'false', 'mensaje' =>alertas_tpl('', $msg ,false)));
			}
		}
	}

	public function agregar(){
		$seccion       = $this->modulo.'/'.$this->seccion.'/horarios_servicio_save';
		$btn_save = form_button(array('class'=>'btn btn-primary', 'name'=>'save_puesto', 'onclick'=>'agregar()','content'=>$this->lang_item("btn_guardar")));
		$btn_reset = form_button(array('class'=>'btn btn_primary', 'name'=>'reset','onclick'=>'clean_formulario()','content'=>$this->lang_item('btn_limpiar')));

		$sqlData = array(
			 'buscar'          => ''
			,'offset'       => 0
			,'limit'        => 0
		);
		$sucursales_array     = array(
					 'data'     => $this->db_model2->db_get_data($sqlData)
					,'value'    => 'id_sucursal'
					,'text'  => array('sucursal')
					,'name'  => "lts_sucursales"
					,'class'    => "requerido"
					);
		$sucursales                      = dropdown_tpl($sucursales_array);
		$tab_1["lbl_servicio"]           = $this->lang_item("lbl_servicio");
		$tab_1["lbl_clave_corta"]        = $this->lang_item("lbl_clave_corta");
		$tab_1["lbl_inicio"]             = $this->lang_item("lbl_inicio");
		$tab_1["lbl_final"]              = $this->lang_item("lbl_final");
		$tab_1["lbl_sucursal"]           = $this->lang_item("lbl_sucursal");
		$tab_1["lbl_descripcion"]        = $this->lang_item("lbl_descripcion");

		$tab_1["list_sucursal"]          = $sucursales;

		$tab_1['button_save'] = $btn_save;
		$tab_1['button_reset'] = $btn_reset;

		if($this->ajax_post(false))
		{
			echo json_encode($this->load_view_unique($seccion,$tab_1,true));
		}
		else
		{
			return $this->load_view_unique($seccion, $tab_1, true);
		}
	}

	public function insert_servicio(){
		$objData    = $this->ajax_post('objData');
		if($objData['incomplete']>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			echo json_encode( array( 'success'=>'false', 'mensaje' => alertas_tpl('error', $msg ,false)));
		}else{
			$id_sucursal  =   $objData['lts_sucursales'];
			$ajax_inicio  = $objData['timepicker1'];
			$ajax_termino = $objData['timepicker2'];
			$servicios    = $this->db_model->db_get_data_x_sucursal($id_sucursal);

			$check_times  = $this->check_times_ranges($ajax_inicio,$ajax_termino, $servicios);
			if($check_times['response']){
				$sqlData = array(
					 'servicio'    => $objData['txt_servicio']
					,'clave_corta' => $objData['txt_clave_corta']
					,'descripcion' => $objData['txt_descripcion']
					,'inicio'      => $ajax_inicio
					,'final'       => $ajax_termino
					,'id_sucursal' => $id_sucursal
					,'id_usuario'  => $this->session->userdata('id_usuario')
					,'timestamp'   => $this->timestamp()
					);
				$insert = $this->db_model->db_insert_data($sqlData);
				if($insert){
					$$msg = $this->lang_item("msg_insert_success",false);
					echo json_encode(array(  'success'=>'true', 'mensaje' => $msg));
				}else{
					$msg = $this->lang_item("msg_err_clv",false);
					echo json_encode( array( 'success'=>'false', 'mensaje' =>alertas_tpl('', $msg ,false)));
				}
			}else{
				$msg = $this->lang_item("msg_horario_empalmado",false);
				echo json_encode( array( 'success'=>'false', 'mensaje' =>alertas_tpl('', $msg ,false)));
			}
		}
	}

	public function export_xlsx($offset=0){
		$filtro      = ($this->ajax_get('filtro')) ?  base64_decode($this->ajax_get('filtro') ): "";
		
		$limit       = $this->limit_max;
		$sqlData     = array(
			 'buscar'         => $filtro
			,'offset'      => $offset
			,'limit'       => $limit
		);
		//print_debug($sqlData);
		$lts_content = $this->db_model->db_get_data($sqlData);
		if(count($lts_content)>0){
			foreach ($lts_content as $value){
				
				$set_data[] = array(
									 $value['servicio']
									,$value['cv_servicio']
									,$value['sucursal']
									,$value['descripcion']
									,$value['inicio']
									,$value['final']
									);
			}
			
			$set_heading = array(
									 $this->lang_item("lbl_servicio")
									,$this->lang_item("lbl_clave_corta")
									,$this->lang_item("lbl_sucursal")
									,$this->lang_item("lbl_descripcion")
									,$this->lang_item("lbl_inicio")
									,$this->lang_item("lbl_final")
									);
		}

		$params = array(  'title'   => $this->lang_item("lbl_excel"),
							'items'   => $set_data,
							'headers' => $set_heading
						);
		
		$this->excel->generate_xlsx($params);
	}
}