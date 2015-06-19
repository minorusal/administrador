<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class servicios extends Base_Controller
{
	private $modulo;
	private $submodulo;
	private $seccion;
	private $view_content;
	private $path;
	private $icon;

	private $offset, $limit_max;
	private $tab, $tab1, $tab2, $tab3;

	public function __construct()
	{
		parent::__construct();
		$this->modulo 			= 'administracion';
		$this->submodulo		= 'catalogos';
		$this->seccion          = 'servicios';
		$this->icon 			= 'fa fa-phone-square'; 
		$this->path 			= $this->modulo.'/'.$this->seccion.'/'; 
		$this->view_content 	= 'content';
		$this->limit_max		= 10;
		$this->offset			= 0;
		// Tabs
		$this->tab1 			= 'agregar';
		$this->tab2 			= 'listado';
		$this->tab3 			= 'detalle';
		// DB Model
		$this->load->model($this->modulo.'/'.$this->seccion.'_model','db_model');
		$this->load->model('administracion/sucursales_model','db_model2');
		// Diccionario
		$this->lang->load($this->modulo.'/'.$this->seccion,"es_ES");
	}

	public function config_tabs()
	{
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

	private function uri_view_principal()
	{
		return $this->modulo.'/'.$this->view_content;
	}

	public function index()
	{
		$tabl_inicial 			  = 2;
		$view_listado    		  = $this->listado();	
		$contenidos_tab           = $view_listado;
		$data['titulo_seccion']   = $this->lang_item($this->seccion);
		$data['titulo_submodulo'] = $this->lang_item("titulo_submodulo");
		$data['icon']             = $this->icon;
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);	
		
		$js['js'][]  = array('name' => $this->seccion, 'dirname' => $this->modulo);
		$this->load_view($this->uri_view_principal(), $data, $js);
	}

	public function listado($offset=0)
	{
		// Crea tabla con listado de elementos capturados 
		$seccion 		= '/listado';
		$tab_detalle	= $this->tab3;	
		$limit 			= $this->limit_max;
		$uri_view 		= $this->modulo.$seccion;
		$url_link 		= $this->path.'listado';
		$filtro      	= ($this->ajax_post('filtro')) ? $this->ajax_post('filtro') : "";
		$sqlData = array(
			 'buscar'      	=> $filtro
			,'offset' 		=> $offset
			,'limit'      	=> $limit
		);
		$uri_segment  = $this->uri_segment(); 
		$total_rows	  = count($this->db_model->db_get_data($sqlData));
		$sqlData['aplicar_limit'] = false;
		$list_content = $this->db_model->db_get_data($sqlData);
		$url          = base_url($url_link);
		$paginador    = $this->pagination_bootstrap->paginator_generate($total_rows, $url, $limit, $uri_segment, array('evento_link' => 'onclick', 'function_js' => 'load_content', 'params_js'=>'1'));
		if($total_rows){
			foreach ($list_content as $value)
			{
			// Evento de enlace
				$atrr = array(
								'href' => '#',
							  	'onclick' => $tab_detalle.'('.$value['id_administracion_servicio'].')'
						);

				// Datos para tabla
				$tbl_data[] = array('id'            => $value['id_administracion_servicio'],
									'servicio'      => tool_tips_tpl($value['servicio'], $this->lang_item("tool_tip"), 'right' , $atrr),
									'clave_corta'   => $value['clave_corta'],
									'descripcion'   => $value['descripcion'],
									'inicio'        => $value['inicio'],
									'final'         => $value['final']
									);
			}

			// Plantilla
			$tbl_plantilla = array ('table_open'  => '<table class="table table-bordered responsive ">');
			// Titulos de tabla
			$this->table->set_heading(	$this->lang_item("id"),
										$this->lang_item("lbl_servicio"),
										$this->lang_item("lbl_clave_corta"),
										$this->lang_item("lbl_descripcion"),
										$this->lang_item("lbl_inicio"),
										$this->lang_item("lbl_final"));
			// Generar tabla
			$this->table->set_template($tbl_plantilla);
			$tabla = $this->table->generate($tbl_data);
			$buttonTPL = array( 'text'   => $this->lang_item("btn_xlsx"), 
								'iconsweets' => 'iconsweets-excel',
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
		if($this->ajax_post(false))
		{
			echo json_encode( $this->load_view_unique($uri_view , $tabData, true));
		}
		else
		{
			return $this->load_view_unique($uri_view , $tabData, true);
		}
	}

	public function detalle()
	{
		$id_servicio                 = $this->ajax_post('id_servicio');
		$detalle  	                 = $this->db_model->get_orden_unico_servicio($id_servicio);
		//print_debug(substr($detalle[0]['inicio'], 0,5));
		$seccion 	                 = 'detalle';
		$tab_detalle                 = $this->tab3;
		$sqlData = array(
			 'buscar'      	 => ''
			,'offset' 		 => 0
			,'limit'      	 => 0
		);
		$sucursales_array     = array(
					 'data'     => $this->db_model2->db_get_data($sqlData)
					,'value' 	=> 'id_sucursal'
					,'text' 	=> array('sucursal')
					,'name' 	=> "lts_sucursales"
					,'class' 	=> "requerido"
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
	    $usuario_name	                   = text_format_tpl($usuario_registro[0]['name'],"u");
	    $tabData['val_usuarios_registro']  = $usuario_name;

        if($detalle[0]['edit_id_usuario'])
        {
        	$usuario_registro                   = $this->users_model->search_user_for_id($detalle[0]['edit_id_usuario']);
        	$usuario_name 				        = text_format_tpl($usuario_registro[0]['name'],"u");
        	$tabData['val_ultima_modificacion'] = sprintf($this->lang_item('val_ultima_modificacion', false), $this->timestamp_complete($detalle[0]['edit_timestamp']), $usuario_name);
        }
        else
        {
        	$usuario_name = '';
    		$tabData['val_ultima_modificacion'] = $this->lang_item('lbl_sin_modificacion', false);
        }

        $tabData['button_save']           = $btn_save;
        $tabData['registro_por']    	= $this->lang_item("registro_por",false);
      	$tabData['usuario_registro']	= $usuario_name;
        									   #administracion/catalogos/sucursales/sucursales_detalle	
		$uri_view   				  = $this->modulo.'/'.$this->seccion.'/'.$this->seccion.'_'.$seccion;
		echo json_encode( $this->load_view_unique($uri_view ,$tabData, true));
	}

	public function actualizar()
	{	//print_debug(time_to_decimal($this->ajax_post('inicio')));
		$incomplete  = $this->ajax_post('incomplete');
		$mayor  = $this->ajax_post('mayor');
		
		if($incomplete>0)
		{
			$msg            = $this->lang_item("msg_campos_obligatorios",false);

			$json_respuesta = array(
						 'id' 		=> 0
						,'contenido'=> alertas_tpl('error', $msg ,false)
						,'success' 	=> false
				);
		}
		if($mayor == 'false'){
			$msg            = $this->lang_item("msg_horainicio_mayor",false);

			$json_respuesta = array(
						 'id' 		=> 0
						,'contenido'=> alertas_tpl('error', $msg ,false)
						,'success' 	=> false
				);
		}
		else
		{
			$sqlData = array(
						 'id_administracion_servicio' => $this->ajax_post('id_servicio')
						,'servicio' 		          => $this->ajax_post('servicio')
						,'inicio'			          => $this->ajax_post('inicio')
						,'final'			          => $this->ajax_post('final')
						,'id_sucursal'				  => $this->ajax_post('id_sucursal')
						,'clave_corta' 				  => $this->ajax_post('clave_corta')
						,'descripcion'				  => $this->ajax_post('descripcion')
						,'edit_timestamp'			  => $this->timestamp()
						,'edit_id_usuario'			  => $this->session->userdata('id_usuario')
						);
			$insert = $this->db_model->db_update_data($sqlData);
			
			if($insert)
			{
				$msg = $this->lang_item("msg_insert_success",false);
				$json_respuesta = array(
						 'id' 		=> 1
						,'contenido'=> alertas_tpl('success', $msg ,false)
						,'success' 	=> true
				);
			}
			else
			{
				$msg = $this->lang_item("msg_horario_empalmado",false);
				$json_respuesta = array(
						 'id' 		=> 0
						,'contenido'=> alertas_tpl('', $msg ,false)
						,'success' 	=> false
				);
			}
		}
		echo json_encode($json_respuesta);
	}

	public function agregar()
	{
		$seccion = $this->modulo.'/'.$this->seccion.'/'.$this->seccion.'_save';
		$btn_save = form_button(array('class'=>'btn btn-primary', 'name'=>'save_puesto', 'onclick'=>'agregar()','content'=>$this->lang_item("btn_guardar")));
		$btn_reset = form_button(array('class'=>'btn btn_primary', 'name'=>'reset','onclick'=>'clean_formulario()','content'=>$this->lang_item('btn_limpiar')));

		$sqlData = array(
			 'buscar'      	 => ''
			,'offset' 		 => 0
			,'limit'      	 => 0
		);
		$sucursales_array     = array(
					 'data'     => $this->db_model2->db_get_data($sqlData)
					,'value' 	=> 'id_sucursal'
					,'text' 	=> array('sucursal')
					,'name' 	=> "lts_sucursales"
					,'class' 	=> "requerido"
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
		//print_debug($this->ajax_post(false));
		$incomplete  = $this->ajax_post('incomplete');
		
		
		if($incomplete>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			echo json_encode(alertas_tpl('error', $msg ,false));
			
		}else{
			$id_sucursal = 	 $this->ajax_post('id_sucursal');
			$ajax_inicio  =  $this->ajax_post('inicio');
			$ajax_termino =  $this->ajax_post('termino');
			
			$servicios = $this->db_model->db_get_data_x_sucursal($id_sucursal);
			
			$check_times = $this->check_times_ranges($ajax_inicio,$ajax_termino, $servicios);
			if($check_times['response']){
				'insert'
			}else{
				
			}
			echo json_encode($check_times['msg']);

		}
	}


	public function check_times_ranges($incio, $termino, $times = array()){


		$mk_inicio       =  explode(':', $incio);
		$mk_termino      =  explode(':', $termino);
		$mk_inicio       =  mktime($mk_inicio[0],  $mk_inicio[1],  0,0,0,0);
		$mk_termino      =  mktime($mk_termino[0], $mk_termino[1],0,0,0,0);

		if($mk_inicio==$mk_termino){
				$msg = 'La hora de inicio no puede ser igual a la de terminbo';
				$response = false;
		}else{
			if($mk_inicio>$mk_termino){
				$msg = 'La hora de inicio no puede ser mayor a la de termibno';
				$response = false;
			}else{
				$contador = 0;
				if(is_array($times)){
					foreach ($times as $item) {
						if($this->validar_rango( $item['inicio'] , $item['final'], $incio)){
							$contador++;
						}
						if($this->validar_rango( $item['inicio'] , $item['final'], $termino)){
							$contador++;
						}
					}
					if($contador>0){
						$msg = 'el horario se empalma';
						$response = false;
					}else{
						$msg = 'exito se registra horario';
						$response = true;
					}
				}else{
					$msg = 'exito se registra horario fgd';
					$response = true;
				}
			}
		}

		return array('response'=> $response, 'msg' =>  $msg);
	}
	public function validar_rango($inicio, $fin, $value){
	    $inicio  = strtotime($inicio);
	    $fin     = strtotime($fin);
	    $value   = strtotime($value);
	    return (($value >= $inicio) && ($value <= $fin));
	}
}