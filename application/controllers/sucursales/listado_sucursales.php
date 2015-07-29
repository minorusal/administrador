<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class listado_sucursales extends Base_Controller{
	private $modulo;
	private $seccion;
	private $view_content;
	private $path;
	private $icon;

	private $offset, $limit_max;
	private $tab, $tab1, $tab2, $tab3;

	public function __construct(){
		parent::__construct();
		$this->modulo 			= 'sucursales';
		$this->seccion          = 'listado_sucursales';
		$this->icon 			= 'fa fa-sitemap'; 
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
		$this->load->model('administracion/formas_de_pago_model','forma_pago');
		$this->load->model('administracion/entidades_model','db_model2');
		$this->load->model('administracion/regiones_model','regiones');
		// Diccionario
		$this->lang->load($this->modulo.'/'.$this->seccion,"es_ES");
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
		return $this->modulo.'/'.$this->view_content;
	}

	public function index(){
		$tabl_inicial 			  = 2;
		$view_listado    		  = $this->listado();	
		$contenidos_tab           = $view_listado;
		$data['titulo_seccion']   = $this->lang_item('lbl_seccion');
		$data['titulo_modulo']    = $this->lang_item('lbl_submodulo');
		$data['icon']             = $this->icon;
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);	
		
		$js['js'][]  = array('name' => $this->seccion, 'dirname' => $this->modulo);
		$this->load_view($this->uri_view_principal(), $data, $js);
	}

	public function listado($offset=0){
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
			foreach ($list_content as $value){
				// Evento de enlace
				$atrr = array(
								'href' => '#',
							  	'onclick' => $tab_detalle.'('.$value['id_sucursal'].')'
						);
				$btn_acciones['ficha'] 	= '<span style="color:blue;"  class="ico_acciones ico_articulos fa fa-search-plus" onclick="sucursales('.$value['id_sucursal'].')" title="'.$this->lang_item("lbl_ficha_tecnica").'"></span>';
				$btn_acciones['excel']      = '<span id="ico-excel_'.$value['id_sucursal'].'" class="ico_acciones ico_excel fa fa-file-text" onclick="window.location.href=\''.base_url($this->path.'export_sucursal_xlsx?filtro='.base64_encode($value['id_sucursal'])).'\';" title="'.$this->lang_item("reporte").'"></span>';
				$acciones = implode('&nbsp;&nbsp;&nbsp;',$btn_acciones);
				// Datos para tabla
				$tbl_data[] = array('id'                => $value['id_sucursal'],
									'sucursal'          => tool_tips_tpl($value['sucursal'], $this->lang_item("tool_tip"), 'right' , $atrr),
									'clave_corta'       => $value['cv_sucursal'],
									'horario_atencion'  => $value['inicio'].' a '.$value['final'],
									'regiones'          => $value['region'],
									'entidad'  			=> $value['entidad'],
									'direccion'         => tool_tips_tpl($value['direccion'], $this->lang_item("tool_tip"), 'right' , $atrr),
									'acciones'			=> $acciones
									);
			}
			// Plantilla
			$tbl_plantilla = set_table_tpl();
			// Titulos de tabla
			$this->table->set_heading(	$this->lang_item("lbl_id"),
										$this->lang_item("lbl_sucursal"),
										$this->lang_item("lbl_clave_corta"),
										$this->lang_item("lbl_horario_atencion"),
										$this->lang_item("lbl_region"),
										$this->lang_item("lbl_entidad"),
										$this->lang_item("lbl_direccion"),
										$this->lang_item("lbl_acciones"));
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
		if($this->ajax_post(false)){
			echo json_encode( $this->load_view_unique($uri_view , $tabData, true));
		}else{
			return $this->load_view_unique($uri_view , $tabData, true);
		}
	}

	public function detalle(){
		$id_sucursal                 = $this->ajax_post('id_sucursal');
		$detalle  	                 = $this->db_model->get_orden_unico_sucursal($id_sucursal);
		if($detalle[0]['factura'] == 0){
			$checked_fact = '';
		}else{
			$checked_fact = 'checked';
		}
		foreach ($detalle as $value){
			$id_pago[]  = $value['id_sucursales_esquema_pago'];
			$id_venta[] = $value['id_sucursales_esquema_venta'];
			$id_fpago[] = $value['id_forma_pago'];
		}
		$seccion 	                 = 'detalle';
		$tab_detalle                 = $this->tab3;
		$sqlData        = array(
			 'buscar'      	=> ''
			,'offset' 		=> 0
			,'limit'      	=> 0
		);

		$regiones_array = array(
					'data'			 => $this->regiones->db_get_data($sqlData)
					,'value' 	     => 'id_administracion_region'
					,'text' 	     => array('region')
					,'name' 	     => "lts_regiones"
					,'class' 	     => "requerido"
					,'selected'      => $detalle[0]['id_region']
			);
		$regiones                    = dropdown_tpl($regiones_array);
		$entidades_array = array(
					 'data'			 => $this->db_model2->get_entidades_default($sqlData)
					,'value' 	     => 'id_administracion_entidad'
					,'text' 	     => array('entidad')
					,'name' 	     => "lts_entidades"
					,'class' 	     => "requerido"
					,'selected'      => $detalle[0]['id_entidad']
					);
		$entidades                         = dropdown_tpl($entidades_array);
		$esquema_pago_array  = array(
						'data'		=> $this->db_model->get_esquema_pago($sqlData)
						,'value' 	=> 'id_sucursales_esquema_pago'
						,'text' 	=> array('clave_corta','esquema_pago')
						,'name' 	=> "lts_esquema_pago"
						,'class' 	=> "requerido"
						,'selected' => $id_pago
					);
		$list_esquema_pago  = multi_dropdown_tpl($esquema_pago_array);

		$esquema_venta_array  = array(
						 'data'		=> $this->db_model->get_esquema_venta($sqlData)
						,'value' 	=> 'id_sucursales_esquema_venta'
						,'text' 	=> array('clave_corta','esquema_venta')
						,'name' 	=> "lts_esquema_venta"
						,'class' 	=> "requerido"
						,'selected' => $id_venta
					);
		$list_esquema_venta  = multi_dropdown_tpl($esquema_venta_array);

		$forma_pago_array  = array(
						 'data'		=> $this->forma_pago->db_get_data($sqlData)
						,'value' 	=> 'id_forma_pago'
						,'text' 	=> array('clave_corta','forma_pago')
						,'name' 	=> "lts_forma_pago"
						,'class' 	=> "requerido"
						,'selected' => $id_fpago
					);
		$list_forma_pago  = multi_dropdown_tpl($forma_pago_array);
		$btn_save                          = form_button(array('class'=>"btn btn-primary",'name' => 'actualizar' , 'onclick'=>'actualizar()','content' => $this->lang_item("btn_guardar") ));   
        $tabData['id_sucursal']            = $id_sucursal;
        $tabData["nombre_sucursal"]        = $this->lang_item("nombre_sucursal");
		$tabData["cvl_corta"]              = $this->lang_item("clave_corta");
		$tabData["r_social"]               = $this->lang_item("rs");
		$tabData["r_f_c"]                  = $this->lang_item("rfc");
		$tabData["lbl_email"]              = $this->lang_item("lbl_email");
		$tabData["lbl_encargado"]          = $this->lang_item("lbl_encargado");
		$tabData['lbl_esquema_pago']       = $this->lang_item('lbl_esquema_pago');
		$tabData['lbl_esquema_venta']      = $this->lang_item('lbl_esquema_venta');
		$tabData['lbl_forma_pago']         = $this->lang_item('lbl_forma_pago');
		$tabData['lbl_comprobante_factura']= $this->lang_item('lbl_comprobante_factura');
		$tabData["dir"]                    = $this->lang_item("direccion");
		$tabData["lbl_telefono"]           = $this->lang_item("lbl_telefono");
		$tabData["lbl_inicio"]             = $this->lang_item("lbl_inicio");
		$tabData["lbl_final"]              = $this->lang_item("lbl_final");
		$tabData["list_entidad"]           = $entidades;
		$tabData["list_region"]            = $regiones;
		$tabData["list_esquema_pago"]      = $list_esquema_pago;
		$tabData["list_esquema_venta"]     = $list_esquema_venta;
		$tabData['list_forma_pago']        = $list_forma_pago;
		$tabData["timepicker1"]            = $detalle[0]['inicio'];
		$tabData["timepicker2"]            = $detalle[0]['final'];
		$tabData["lbl_entidad"]            = $this->lang_item("lbl_entidad");
		$tabData["lbl_region"]             = $this->lang_item("lbl_region");
		$tabData['factura'] 			   = $checked_fact;
        $tabData['sucursal']               = $detalle[0]['sucursal'];
		$tabData['clave_corta']            = $detalle[0]['clave_corta'];
        $tabData['razon_social']           = $detalle[0]['razon_social'];
        $tabData['rfc']                    = $detalle[0]['rfc'];
        $tabData['email']                  = $detalle[0]['email'];
		$tabData['encargado']              = $detalle[0]['encargado'];
        $tabData['direccion']              = $detalle[0]['direccion'];
        $tabData['telefono']               = $detalle[0]['telefono'];
        $tabData['lbl_ultima_modificacion'] = $this->lang_item('lbl_ultima_modificacion', false);
        $tabData['val_fecha_registro']     = $detalle[0]['timestamp'];
		$tabData['lbl_fecha_registro']     = $this->lang_item('lbl_fecha_registro', false);
		$tabData['lbl_usuario_registro']    = $this->lang_item('lbl_usuario_registro', false);
        
        $this->load_database('global_system');
        $this->load->model('users_model');

        $usuario_registro                  = $this->users_model->search_user_for_id($detalle[0]['id_usuario']);
	    $usuario_name	                   = text_format_tpl($usuario_registro[0]['name'],"u");
	    $tabData['val_usuarios_registro']  = $usuario_name;

        if($detalle[0]['edit_id_usuario']){
        	$usuario_registro                   = $this->users_model->search_user_for_id($detalle[0]['edit_id_usuario']);
        	$usuario_name 				        = text_format_tpl($usuario_registro[0]['name'],"u");
        	$tabData['val_ultima_modificacion'] = sprintf($this->lang_item('val_ultima_modificacion', false), $this->timestamp_complete($detalle[0]['edit_timestamp']), $usuario_name);
        }else{
        	$usuario_name = '';
    		$tabData['val_ultima_modificacion'] = $this->lang_item('lbl_sin_modificacion', false);
        }

        $tabData['button_save']         = $btn_save;
        $tabData['registro_por']    	= $this->lang_item("registro_por",false);
      	$tabData['usuario_registro']	= $usuario_name;
        									   	
		$uri_view   				  = $this->modulo.'/'.$this->seccion.'/listado_sucursales_detalle';
		echo json_encode( $this->load_view_unique($uri_view ,$tabData, true));
	}

	public function sucursales(){
		$forma 			= '';
		$pago  			= '';
		$venta 			= '';
		$id_sucursal    = $this->ajax_post('id_sucursal');
		$fpago 			= $this->db_model->get_forma_pago($id_sucursal);
		$epago 			= $this->db_model->get_esquemas_pago($id_sucursal);
		$eventa 		= $this->db_model->get_esquemas_venta($id_sucursal);
		$detalle  	    = $this->db_model->get_orden_unico_sucursal($id_sucursal);
		$total_rows     =count($detalle);
		
		if($fpago && $epago && $eventa){
			foreach ($fpago as $key => $value) {
				$forma_pago[]   = $value['forma_pago'];
				$forma .= '-'.$forma_pago[$key].'<br>';	
			}
			foreach ($epago as $key => $value) {
				$esquema_pago[]   = $value['esquema_pago'];
				$pago .= '-'.$esquema_pago[$key].'<br>';	
			}
			foreach ($eventa as $key => $value) {
				$esquema_venta[]   = $value['esquema_venta'];
				$venta .= '-'.$esquema_venta[$key].'<br>';	
			}
		}else{
			$fpago  = "";
			$epago  = "";
			$eventa = "";
		}
		//print_debug($forma);
		$tbl_data[] = array('id'                => $detalle[0]['id_sucursal'],
							'nombre'            => '<strong>'.$this->lang_item("lbl_sucursal").':</strong>',
							'nombre_bd'         => $detalle[0]['sucursal'],
							'clave_corta'       => '<strong>'.$this->lang_item("lbl_clave_corta").':</strong>',
							'clave_corta_bd'    => $detalle[0]['clave_corta']);
		
		$tbl_data[] = array('id'                => $detalle[0]['id_sucursal'],
							'atencion'       	=> '<strong>'.$this->lang_item("lbl_horario_atencion").':</strong>',
							'atencion_bd'    	=> $detalle[0]['inicio'].' a '.$detalle[0]['final'],
							'forma_pago'        => '<strong>'.$this->lang_item("lbl_forma_pago").':</strong>',
							'forma_pago_bd'     => $forma
							);

		$tbl_data[] = array('id'                => $detalle[0]['id_sucursal'],
							'esquema_pago'      => '<strong>'.$this->lang_item("lbl_esquema_pago").':</strong>',
							'esquema_pago_bd'   => $pago,
							'esquema_venta'     => '<strong>'.$this->lang_item("lbl_esquema_venta").':</strong>',
							'esquema_venta_bd'  => $venta
							);

		$tbl_data[] = array('id'           => $detalle[0]['id_sucursal'],
							'factura'      => '<strong>'.$this->lang_item("lbl_comprobante_factura").':</strong>',
							'factura_bd'   => ($detalle[0]['factura']==1)?'si':'no',
							'r_social'     => '<strong>'.$this->lang_item("rs").':</strong>',
							'r_social_bd'  => $detalle[0]['razon_social']
							);
		$tbl_data[] = array('id'          => $detalle[0]['id_sucursal'],
							'region'      => '<strong>'.$this->lang_item("lbl_region").':</strong>',
							'region_bd'   => $detalle[0]['region'],
							'entidad'     => '<strong>'.$this->lang_item("lbl_entidad").':</strong>',
							'entidad_bd'  => $detalle[0]['entidad']
							);

		$tbl_data[] = array('id'       => $detalle[0]['id_sucursal'],
							'rfc'      =>'<strong>'. $this->lang_item("rfc").':</strong>',
							'rfc_bd'   => $detalle[0]['rfc'],
							'email'    => '<strong>'.$this->lang_item("lbl_email").':</strong>',
							'email_bd' => $detalle[0]['email']
							);

		$tbl_data[] = array('id'        => $detalle[0]['id_sucursal'],
							'encargado' => '<strong>'.$this->lang_item("lbl_encargado").':</strong>',
							'rfc_bd'    => $detalle[0]['encargado'],
							'email'     => '<strong>'.$this->lang_item("lbl_telefono").':</strong>',
							'email_bd'  => $detalle[0]['telefono']
							);
		$tbl_data[] = array('id'           => $detalle[0]['id_sucursal'],
							'direccion'    => '<strong>'.$this->lang_item("direccion").':</strong>',
							'direccion_bd' => $detalle[0]['direccion'],
							'vacio'        => '<strong>'.$this->lang_item("lbl_nada").':</strong>',
							'vacio_bd'     => $this->lang_item("lbl_nada")
							);
		// Plantilla
		$tbl_plantilla = set_table_tpl();
		// Titulos de tabla
		$this->table->set_heading(	$this->lang_item("lbl_id"),
									$this->lang_item("lbl_informacion_general"),
									$this->lang_item("lbl_nada"),
									$detalle[0]['sucursal'],
									$this->lang_item("lbl_nada")
									);
		// Generar tabla
		$this->table->set_template($tbl_plantilla);
		$tabla = $this->table->generate($tbl_data);
		$buttonTPL 		= array( 'text'       => $this->lang_item("btn_xlsx"), 
							     'iconsweets' => 'iconsweets-excel',
							     'href'       => base_url($this->path.'export_sucursal_xlsx?filtro='.base64_encode($detalle[0]['id_sucursal']))
								);
		
		$tabData['export']  = button_tpl($buttonTPL);
		$tabData['lbl_resumen'] = $this->lang_item('lbl_resumen');
		$tabData['tabla']   = $tabla;
		$uri_view   	    = $this->modulo.'/'.$this->seccion.'/ficha_sucursales_detalle';
		echo json_encode( $this->load_view_unique($uri_view ,$tabData, true));
	}

	public function actualizar(){
		$objData  	= $this->ajax_post('objData');
		if($objData['incomplete']>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			echo json_encode(array(  'success'=>'false', 'mensaje' => alertas_tpl('error', $msg ,false)));
		}else{
			$ajax_inicio  =  $objData['timepicker1'];
			$ajax_termino =  $objData['timepicker2'];
			
			$check_times  =  $this->check_time_longer($ajax_inicio,$ajax_termino);
			if($check_times['response']){

				$sqlData = array(
						 'id_sucursal'	 => $objData['id_sucursal']
						,'sucursal'      => $objData['sucursal']
						,'clave_corta' 	 => $objData['clave_corta']
						,'razon_social'	 => $objData['razon_social']
						,'rfc'			 => $objData['rfc']
						,'email'	     => $objData['email']
						,'encargado'	 => $objData['encargado']
						,'inicio'	     => $ajax_inicio
						,'final'	     => $ajax_termino
						,'factura'	     => ($objData['factura']=="true")?1:0
						,'id_region'	 => $objData['lts_regiones']
						,'id_entidad'	 => $objData['lts_entidades']
						,'telefono'		 => $objData['telefono']
						,'direccion'	 => $objData['direccion']
						,'edit_timestamp'	=> $this->timestamp()
						,'edit_id_usuario'	=> $this->session->userdata('id_usuario')
						);
				$insert = $this->db_model->db_update_data($sqlData);
				
					$arr_pago  = explode(',',$objData['lts_esquema_pago']);
					$pago     = $this->db_model->delete_pago($objData['id_sucursal']);
					
					if(!empty($arr_pago)){
						$sqlData = array();
						foreach ($arr_pago as $key => $value){
							$sqlData = array(
								 'id_sucursal'       => $objData['id_sucursal']
								,'id_esquema_pago'   => $value
								,'id_usuario'   => $this->session->userdata('id_usuario')
								,'timestamp'    => $this->timestamp()
								);
							$insert_pago = $this->db_model->db_update_data_pago($sqlData);
						}
					}

					$arr_venta  = explode(',',$objData['lts_esquema_venta']);
					$venta      = $this->db_model->delete_venta($objData['id_sucursal']);
					
					if(!empty($arr_venta)){
						$sqlData = array();
						foreach ($arr_venta as $key => $value){
							$sqlData = array(
								 'id_sucursal'       => $objData['id_sucursal']
								,'id_esquema_venta'  => $value
								,'id_usuario'   => $this->session->userdata('id_usuario')
								,'timestamp'    => $this->timestamp()
								);
							$insert_venta = $this->db_model->db_update_data_venta($sqlData);
						}
					}

					$arr_fpago  = explode(',',$objData['lts_forma_pago']);
					$fpago      = $this->db_model->delete_fpago($objData['id_sucursal']);
					
					if(!empty($arr_fpago)){
						$sqlData = array();
						foreach ($arr_fpago as $key => $value){
							$sqlData = array(
								 'id_sucursal'       => $objData['id_sucursal']
								,'id_forma_pago'  => $value
								,'id_usuario'   => $this->session->userdata('id_usuario')
								,'timestamp'    => $this->timestamp()
								);
							$insert_fpago = $this->db_model->db_update_data_fpago($sqlData);
						}
					}
				if($insert){
					$msg = $this->lang_item("msg_update_success",false);
					echo json_encode(array(  'success'=>'true', 'mensaje' => $msg ));
				}else{
					$msg = $this->lang_item("msg_err_clv",false);
					echo json_encode( array( 'success'=>'false', 'mensaje' =>alertas_tpl('', $msg ,false)));
				}
			}else{
				$msg = $this->lang_item("msg_horainicio_mayor",false);
				echo json_encode( array( 'success'=>'false', 'mensaje' =>alertas_tpl('', $msg ,false)));
			}
		}
	}

	public function agregar()
	{							
		$seccion       = $this->modulo.'/'.$this->seccion.'/listado_sucursales_save';
		$sqlData        = array(
			 'buscar'      	=> ''
			,'offset' 		=> 0
			,'limit'      	=> 0
		);
		$regiones_array = array(
			      'data'   => $this->regiones->db_get_data($sqlData)
			     ,'value'  => 'id_administracion_region'
			     ,'text'   => array('region')
			     ,'name'   => 'lts_regiones'
			     ,'class'  => 'requerido'
			);
		$regiones = dropdown_tpl($regiones_array);
		$entidades_array = array(
					 'data'			 => $this->db_model2->get_entidades_default($sqlData)
					,'value' 	  => 'id_administracion_entidad'
					,'text' 	  => array('entidad')
					,'name' 	  => "lts_entidades"
					,'class' 	  => "requerido"
					);
		$entidades    = dropdown_tpl($entidades_array);

		$esquema_pago_array  = array(
						'data'		=> $this->db_model->get_esquema_pago($sqlData)
						,'value' 	=> 'id_sucursales_esquema_pago'
						,'text' 	=> array('clave_corta','esquema_pago')
						,'name' 	=> "lts_esquema_pago"
						,'class' 	=> "requerido"
					);
		$list_esquema_pago  = multi_dropdown_tpl($esquema_pago_array);

		$esquema_venta_array  = array(
						 'data'		=> $this->db_model->get_esquema_venta($sqlData)
						,'value' 	=> 'id_sucursales_esquema_venta'
						,'text' 	=> array('clave_corta','esquema_venta')
						,'name' 	=> "lts_esquema_venta"
						,'class' 	=> "requerido"
					);
		$list_esquema_venta  = multi_dropdown_tpl($esquema_venta_array);
		$forma_pago_array  = array(
						 'data'		=> $this->forma_pago->db_get_data($sqlData)
						,'value' 	=> 'id_forma_pago'
						,'text' 	=> array('clave_corta','forma_pago')
						,'name' 	=> "lts_forma_pago"
						,'class' 	=> "requerido"
					);
		$list_forma_pago  = multi_dropdown_tpl($forma_pago_array);

		$btn_save     = form_button(array('class'=>"btn btn-primary",'name' => 'save_almacen','onclick'=>'agregar()' , 'content' => $this->lang_item("btn_guardar") ));
		$btn_reset     = form_button(array('class'=>"btn btn-primary",'name' => 'reset','value' => 'reset','onclick'=>'clean_formulario()','content' => $this->lang_item("btn_limpiar")));

		$tab_1["nombre_sucursal"]   = $this->lang_item("nombre_sucursal");
		$tab_1["cvl_corta"]         = $this->lang_item("clave_corta");
		$tab_1["r_social"]          = $this->lang_item("rs");
		$tab_1["r_f_c"]             = $this->lang_item("rfc");
		$tab_1["lbl_email"]         = $this->lang_item("lbl_email");
		$tab_1["lbl_encargado"]     = $this->lang_item("lbl_encargado");
		$tab_1["lbl_telefono"]      = $this->lang_item("lbl_telefono");
		$tab_1["lbl_inicio"]        = $this->lang_item("lbl_inicio");
		$tab_1["lbl_final"]         = $this->lang_item("lbl_final");
		$tab_1["lbl_esquema_pago"]  = $this->lang_item("lbl_esquema_pago");
		$tab_1["lbl_esquema_venta"] = $this->lang_item("lbl_esquema_venta");
		$tab_1["lbl_comprobante_factura"] = $this->lang_item("lbl_comprobante_factura");
		$tab_1["lbl_forma_pago"]    = $this->lang_item("lbl_forma_pago");
		$tab_1["list_entidad"]      = $entidades;
		$tab_1["list_region"]       = $regiones;
		$tab_1["list_esquema_pago"] = $list_esquema_pago;
		$tab_1["list_esquema_venta"]= $list_esquema_venta;
		$tab_1["list_forma_pago"]   = $list_forma_pago;
		$tab_1["lbl_region"]       = $this->lang_item("lbl_region");
		$tab_1["lbl_entidad"]      = $this->lang_item("lbl_entidad");
		$tab_1["direccion"]        = $this->lang_item("direccion");

        $tab_1['button_save']      = $btn_save;
        $tab_1['button_reset']     = $btn_reset;


        if($this->ajax_post(false)) {
				echo json_encode($this->load_view_unique($seccion , $tab_1, true));
		}else{
			return $this->load_view_unique($seccion , $tab_1, true);
		}
	}

	public function insert_sucursal(){
		$objData  	= $this->ajax_post('objData');
		if($objData['incomplete']>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			echo json_encode(array(  'success'=>'false', 'mensaje' => alertas_tpl('error', $msg ,false)));
		}else{
			$ajax_inicio  =  $objData['timepicker1'];
			$ajax_termino =  $objData['timepicker2'];
			
			$check_times  =  $this->check_time_longer($ajax_inicio,$ajax_termino);
			if($check_times['response']){
				$data_insert     = array(
								  'sucursal'     => $objData['sucursal']
								 ,'clave_corta'  => $objData['clave_corta']
								 ,'direccion'    => $objData['direccion']
								 ,'id_region'    => $objData['lts_regiones']
								 ,'inicio'       => $objData['timepicker1']
								 ,'final'        => $objData['timepicker2']
								 ,'factura'	     => ($objData['factura']=="true")?1:0
								 ,'id_entidad'   => $objData['lts_entidades']
								 ,'razon_social' => $objData['razon_social']
								 ,'rfc'          => $objData['rfc']
								 ,'email'        => $objData['email']
								 ,'encargado'    => $objData['ecnargado']
								 ,'telefono'     => $objData['telefono']
								 ,'id_usuario'   => $this->session->userdata('id_usuario')
								 ,'timestamp'    => $this->timestamp());
				$insert = $this->db_model->db_insert_data($data_insert);

				$res = $this->db_model->get_id_sucursal();
				$arr_pago  = explode(',',$objData['lts_esquema_pago']);
				
				if(!empty($arr_pago)){
					$sqlData = array();
					foreach ($arr_pago as $key => $value){
						$sqlData = array(
							 'id_sucursal'       => ($res[0]['cuantos'])
							,'id_esquema_pago'   => $value
							,'id_usuario'   	 => $this->session->userdata('id_usuario')
							,'timestamp'    	 => $this->timestamp()
							);
						$insert_pago = $this->db_model->db_update_data_pago($sqlData);
					}
				}

				$arr_venta  = explode(',',$objData['lts_esquema_venta']);
				
				if(!empty($arr_venta)){
					$sqlData = array();
					foreach ($arr_venta as $key => $value){
						$sqlData = array(
							 'id_sucursal'       => ($res[0]['cuantos'])
							,'id_esquema_venta'  => $value
							,'id_usuario'   => $this->session->userdata('id_usuario')
							,'timestamp'    => $this->timestamp()
							);
						$insert_venta = $this->db_model->db_update_data_venta($sqlData);
					}
				}

				$arr_fpago  = explode(',',$objData['lts_forma_pago']);
				
				if(!empty($arr_fpago)){
					$sqlData = array();
					foreach ($arr_fpago as $key => $value){
						$sqlData = array(
							 'id_sucursal'       => ($res[0]['cuantos'])
							,'id_forma_pago'  => $value
							,'id_usuario'   => $this->session->userdata('id_usuario')
							,'timestamp'    => $this->timestamp()
							);
						$insert_venta = $this->db_model->db_update_data_fpago($sqlData);
					}
				}
				
				if($insert){
					$msg = $this->lang_item("msg_insert_success",false);
					echo json_encode(array(  'success'=>'true', 'mensaje' => $msg));
				}else{
					$msg = $this->lang_item("msg_err_clv",false);
					echo json_encode(array(  'success'=>'false', 'mensaje' => alertas_tpl('', $msg ,false)));
				}
			}else{
				$msg = $this->lang_item("msg_horainicio_mayor",false);
				echo json_encode( array( 'success'=>'false', 'mensaje' =>alertas_tpl('', $msg ,false)));
			}
		}
	}
	public function export_sucursal_xlsx($offset=0){
		$filtro      = ($this->ajax_get('filtro')) ?  base64_decode($this->ajax_get('filtro') ): "";
		$limit 		 = $this->limit_max;
		$forma 			= '';
		$pago  			= '';
		$venta 			= '';
		
		$fpago 			= $this->db_model->get_forma_pago($filtro);
		$epago 			= $this->db_model->get_esquemas_pago($filtro);
		$eventa 		= $this->db_model->get_esquemas_venta($filtro);
		$detalle  	    = $this->db_model->get_orden_unico_sucursal($filtro);
		
		if($fpago && $epago && $eventa){
			foreach ($fpago as $key => $value) {
				$forma_pago[]   = $value['forma_pago'];
				$forma .= $forma_pago[$key].', ';	
			}
			foreach ($epago as $key => $value) {
				$esquema_pago[]   = $value['esquema_pago'];
				$pago .= $esquema_pago[$key].', ';	
			}
			foreach ($eventa as $key => $value) {
				$esquema_venta[]   = $value['esquema_venta'];
				$venta .= $esquema_venta[$key].', ';	
			}
		}else{
			$fpago  = "";
			$epago  = "";
			$eventa = "";
		}
		
		$detalle  	    = $this->db_model->get_orden_unico_sucursal($filtro);
		if(count($detalle)>0){
			foreach ($detalle as $value) {
				$set_data[0] = array(
									 $value['sucursal'],
									 $value['clave_corta'],
									 $value['inicio'].' a '.$value['final'],
									 $forma,
									 $pago,
									 $venta,
									 $value['razon_social'],
									 $value['rfc'],
									 $value['email'],
									 $value['encargado'],
									 $value['telefono'],
									 $value['region'],
									 $value['entidad'],
									 $value['direccion']);
			}

			$set_heading = array(
									$this->lang_item("lbl_sucursal"),
									$this->lang_item("clave_corta"),
									$this->lang_item("lbl_horario_atencion"),
									$this->lang_item("lbl_forma_pago"),
									$this->lang_item("lbl_esquema_pago"),
									$this->lang_item("lbl_esquema_venta"),
									$this->lang_item("rs"),
									$this->lang_item("rfc"),
									$this->lang_item("lbl_email"),
									$this->lang_item("lbl_encargado"),
									$this->lang_item("lbl_telefono"),
									$this->lang_item("lbl_region"),
									$this->lang_item("lbl_entidad"),
									$this->lang_item("direccion"));
			
		}
		$params = array(	'title'   => $this->lang_item("lbl_excel"),
							'items'   => $set_data,
							'headers' => $set_heading
						);
		
		$this->excel->generate_xlsx($params);
	}
	public function export_xlsx($offset=0){
		$filtro      = ($this->ajax_get('filtro')) ?  base64_decode($this->ajax_get('filtro') ): "";
		$limit 		 = $this->limit_max;
		$sqlData     = array(
			 'buscar'      	=> $filtro
			,'offset' 		=> $offset
			,'limit'      	=> $limit
			);
		$lts_content = $this->db_model->db_get_data($sqlData);
		if(count($lts_content)>0){
			foreach ($lts_content as $value) {
				$set_data[] = array(
									 $value['sucursal'],
									 $value['cv_sucursal'],
									 $value['inicio'],
									 $value['inicio'],
									 $value['direccion']);
			}
			
			$set_heading = array(
									$this->lang_item("sucursal"),
									$this->lang_item("clave_corta"),
									$this->lang_item("lbl_inicio"),
									$this->lang_item("lbl_final"),
									$this->lang_item("direccion"));
	
		}

		$params = array(	'title'   => $this->lang_item("lbl_excel"),
							'items'   => $set_data,
							'headers' => $set_heading
						);
		
		$this->excel->generate_xlsx($params);
	}
}