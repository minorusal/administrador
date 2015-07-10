<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class sucursales extends Base_Controller
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
		$this->submodulo		= 'sucursales';
		$this->seccion          = 'sucursales';
		$this->icon 			= 'fa fa-sitemap'; 
		$this->path 			= $this->modulo.'/'.$this->seccion.'/'; #administracion/sucursales
		$this->view_content 	= 'content';
		$this->limit_max		= 10;
		$this->offset			= 0;
		// Tabs
		$this->tab1 			= 'agregar';
		$this->tab2 			= 'listado';
		$this->tab3 			= 'detalle';
		// DB Model
		$this->load->model($this->modulo.'/'.$this->seccion.'_model','db_model');
		$this->load->model('administracion/entidades_model','db_model2');
		$this->load->model('administracion/regiones_model','regiones');
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
										 $this->lang_item($tab_1) #agregar
										,$this->lang_item($tab_2) #listado
										,$this->lang_item($tab_3) #detalle
								); 
		// Href de tabs
		$config_tab['links']    = array(
										 $path.$tab_1             #administracion/sucursales/agregar
										,$path.$tab_2.'/'.$pagina #administracion/sucursales/listado
										,$tab_3                   #detalle
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
		if($total_rows)
		{
			foreach ($list_content as $value)
			{
				// Evento de enlace
				$atrr = array(
								'href' => '#',
							  	'onclick' => $tab_detalle.'('.$value['id_sucursal'].')'
						);
				// Datos para tabla
				$tbl_data[] = array('id'            => $value['id_sucursal'],
									'sucursal'      => tool_tips_tpl($value['sucursal'], $this->lang_item("tool_tip"), 'right' , $atrr),
									'clave_corta'   => $value['clave_corta'],
									'razon_social'  => $value['rfc'],
									'regiones'      => $value['region'],
									'rfc'  => $value['razon_social'],
									'direccion'     => tool_tips_tpl($value['direccion'], $this->lang_item("tool_tip"), 'right' , $atrr)
									);
			}
			// Plantilla
			$tbl_plantilla = set_table_tpl();
			// Titulos de tabla
			$this->table->set_heading(	$this->lang_item("id"),
										$this->lang_item("sucursal"),
										$this->lang_item("clave_corta"),
										$this->lang_item("rfc"),
										$this->lang_item("región"),
										$this->lang_item("rs"),
										$this->lang_item("direccion"));
			// Generar tabla
			$this->table->set_template($tbl_plantilla);
			$tabla = $this->table->generate($tbl_data);
			$buttonTPL = array( 'text'   => $this->lang_item("btn_xlsx"), 
								'iconsweets' => 'iconsweets-excel',
								'href'       => base_url($this->path.'export_xlsx?filtro='.base64_encode($filtro))
								);
		}
		else
		{
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
		$id_sucursal                 = $this->ajax_post('id_sucursal');
		$detalle  	                 = $this->db_model->get_orden_unico_sucursal($id_sucursal);
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
		$btn_save                          = form_button(array('class'=>"btn btn-primary",'name' => 'actualizar' , 'onclick'=>'actualizar()','content' => $this->lang_item("btn_guardar") ));   
        $tabData['id_sucursal']            = $id_sucursal;
        $tabData["nombre_sucursal"]        = $this->lang_item("nombre_sucursal");
		$tabData["cvl_corta"]              = $this->lang_item("clave_corta");
		$tabData["r_social"]               = $this->lang_item("rs");
		$tabData["r_f_c"]                  = $this->lang_item("rfc");
		$tabData["lbl_email"]              = $this->lang_item("lbl_email");
		$tabData["lbl_encargado"]          = $this->lang_item("lbl_encargado");
		$tabData["dir"]                    = $this->lang_item("direccion");
		$tabData["tel"]                    = $this->lang_item("tel");
		$tabData["list_entidad"]           = $entidades;
		$tabData["list_region"]            = $regiones;
		$tabData["lbl_entidad"]            = $this->lang_item("lbl_entidad");
		$tabData["lbl_region"]             = $this->lang_item("lbl_region");
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
	{
		$incomplete  = $this->ajax_post('incomplete');
		if($incomplete>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			$json_respuesta = array(
						 'id' 		     => 0
						,'contenido'     => alertas_tpl('error', $msg ,false)
						,'success' 	     => false
				);
		}
		else
		{
			$sqlData = array(
						 'id_sucursal'	 => $this->ajax_post('id_sucursal')
						,'sucursal'      => $this->ajax_post('sucursal')
						,'clave_corta' 	 => $this->ajax_post('clave_corta')
						,'razon_social'	 => $this->ajax_post('razon_social')
						,'rfc'			 => $this->ajax_post('rfc')
						,'email'	     => $this->ajax_post('email')
						,'encargado'	 => $this->ajax_post('encargado')
						,'id_entidad'	 => $this->ajax_post('id_entidad')
						,'telefono'		 => $this->ajax_post('telefono')
						,'direccion'	 => $this->ajax_post('direccion')
						,'edit_timestamp'	=> $this->timestamp()
						,'edit_id_usuario'	=> $this->session->userdata('id_usuario')
						);
			$insert = $this->db_model->db_update_data($sqlData);
			if($insert)
			{
				$msg = $this->lang_item("msg_insert_success",false);
				$json_respuesta = array(
						 'id' 		     => 1
						,'contenido'     => alertas_tpl('success', $msg ,false)
						,'success' 	     => true
				);
			}
			else
			{
				$msg = $this->lang_item("msg_err_clv",false);
				$json_respuesta = array(
						 'id' 		    => 0
						,'contenido'    => alertas_tpl('', $msg ,false)
						,'success'    	=> false
				);
			}
		}
		echo json_encode($json_respuesta);
	}

	public function agregar()
	{							
		$seccion       = $this->modulo.'/'.$this->seccion.'/sucursales_save';
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
		$btn_save     = form_button(array('class'=>"btn btn-primary",'name' => 'save_almacen','onclick'=>'agregar()' , 'content' => $this->lang_item("btn_guardar") ));
		$btn_reset     = form_button(array('class'=>"btn btn-primary",'name' => 'reset','value' => 'reset','onclick'=>'clean_formulario()','content' => $this->lang_item("btn_limpiar")));

		$tab_1["nombre_sucursal"]  = $this->lang_item("nombre_sucursal");
		$tab_1["cvl_corta"]        = $this->lang_item("clave_corta");
		$tab_1["r_social"]         = $this->lang_item("rs");
		$tab_1["r_f_c"]            = $this->lang_item("rfc");
		$tab_1["lbl_email"]        = $this->lang_item("lbl_email");
		$tab_1["lbl_encargado"]    = $this->lang_item("lbl_encargado");
		$tab_1["tel"]              = $this->lang_item("telefono");
		$tab_1["list_entidad"]     = $entidades;
		$tab_1["list_region"]      = $regiones;
		$tab_1["lbl_region"]       = $this->lang_item("lbl_region");
		$tab_1["lbl_entidad"]      = $this->lang_item("lbl_entidad");
		$tab_1["direccion"]        = $this->lang_item("direccion");

        $tab_1['button_save']      = $btn_save;
        $tab_1['button_reset']     = $btn_reset;


        if($this->ajax_post(false))
        {
				echo json_encode($this->load_view_unique($seccion , $tab_1, true));
		}
		else
		{
			return $this->load_view_unique($seccion , $tab_1, true);
		}
	}

	public function insert_sucursal(){
		$incomplete  = $this->ajax_post('incomplete');

		if($incomplete>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			echo json_encode('0|'.alertas_tpl('error', $msg ,false));
		}else{
			$sucursal        = $this->ajax_post('sucursal');
			$clave_corta     = $this->ajax_post('clave_corta');
			$razon_social    = $this->ajax_post('razon_social');
			$rfc             = $this->ajax_post('rfc');
			$email           = $this->ajax_post('email');
			$encargado       = $this->ajax_post('encargado');
			$telefono        = $this->ajax_post('tel');
			$region          = $this->ajax_post('id_region');
			$entidad         = $this->ajax_post('id_entidad');
			$direccion       = $this->ajax_post('direccion');
			$data_insert     = array('sucursal' => $sucursal,
								 'clave_corta'  => $clave_corta,
								 'direccion'    => $direccion,
								 'id_usuario'   => $this->session->userdata('id_usuario'),
								 'id_region'    => $region,
								 'id_entidad'   => $entidad,
								 'razon_social' => $razon_social,
								 'rfc'          => $rfc,
								 'email'        => $email,
								 'encargado'    => $encargado,
								 'telefono'     => $telefono,  
								 'timestamp'    => $this->timestamp());
			$insert = $this->db_model->db_insert_data($data_insert);
			
			if($insert){
				$msg = $this->lang_item("msg_insert_success",false);
				echo json_encode('1|'.alertas_tpl('success', $msg ,false));
			}else{
				$msg = $this->lang_item("msg_err_clv",false);
				echo json_encode('0|'.alertas_tpl('', $msg ,false));
			}
		}
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
									 $value['clave_corta'],
									 $value['razon_social'],
									 $value['rfc'],
									 $value['direccion']);
			}
			
			$set_heading = array(
									$this->lang_item("sucursal"),
									$this->lang_item("clave_corta"),
									$this->lang_item("rs"),
									$this->lang_item("rfc"),
									$this->lang_item("direccion"));
	
		}

		$params = array(	'title'   => $this->lang_item("Catálogo Sucursales"),
							'items'   => $set_data,
							'headers' => $set_heading
						);
		
		$this->excel->generate_xlsx($params);
	}
} 