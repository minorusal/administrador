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
		//$this->seccion          = 'almacenes';
		$this->icon 			= 'fa fa-share-alt-square'; #Icono de modulo
		$this->path 			= $this->modulo.'/'.$this->submodulo.'/'; #almacen/almacenes/
		$this->view_content 	= 'content';
		$this->limit_max		= 5;
		$this->offset			= 0;
		// Tabs
		$this->tab1 			= 'agregar';
		$this->tab2 			= 'listado';
		$this->tab3 			= 'detalle';
		// DB Model
		$this->load->model($this->modulo.'/'.$this->submodulo.'_model','db_model');
		$this->load->model('administracion/entidades_model','db_model2');
		// Diccionario
		$this->lang->load($this->modulo.'/'.$this->submodulo,"es_ES");
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
										 $path.$tab_1             #almacen/almacenes/agregar
										,$path.$tab_2.'/'.$pagina #almacen/almacenes/listado/pagina
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
		$data['titulo_seccion']     = $this->lang_item($this->submodulo);
		$data['titulo_submodulo'] = $this->lang_item("titulo_submodulo");
		$data['icon']             = $this->icon;
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);	
		
		$js['js'][]  = array('name' => $this->submodulo, 'dirname' => $this->modulo);
		//print_debug($js);
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
			,'aplicar_limit'=> true
		);
		
		$uri_segment  = $this->uri_segment(); 
		$total_rows	  = count($this->db_model->db_get_data($sqlData));
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
									'razon_social'  => $value['razon_social'],
									'direccion'     => tool_tips_tpl($value['direccion'], $this->lang_item("tool_tip"), 'right' , $atrr)
									);
			}
			// Plantilla
			$tbl_plantilla = array ('table_open'  => '<table class="table table-bordered responsive ">');
			// Titulos de tabla
			$this->table->set_heading(	$this->lang_item("id"),
										$this->lang_item("sucursal"),
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
		$id_sucursal  = $this->ajax_post('id_sucursal');
		$detalle  	  = $this->db_model->get_orden_unico_sucursal($id_sucursal);
		$seccion 	  = 'detalle';
		$tab_detalle  = $this->tab3;
		$entidades_array = array(
					 'data'		=> $this->db_model2->get_entidades('','','',false)
					,'value' 	=> 'id_administracion_entidad'
					,'text' 	=> array('entidad')
					,'name' 	=> "lts_entidades"
					,'class' 	=> "requerido"
					,'selected' => $detalle[0]['id_entidad']
					);
		$entidades           = dropdown_tpl($entidades_array);
		$btn_save            = form_button(array('class'=>"btn btn-primary",'name' => 'actualizar' , 'onclick'=>'actualizar()','content' => $this->lang_item("btn_guardar") ));
                
        $tabData['id_sucursal']     = $id_sucursal;
        $tabData["nombre_sucursal"] = $this->lang_item("nombre_sucursal");
		$tabData["cvl_corta"]       = $this->lang_item("clave_corta");
		$tabData["r_social"]        = $this->lang_item("rs");
		$tabData["r_f_c"]           = $this->lang_item("rfc");
		$tabData["dir"]             = $this->lang_item("direccion");
		$tabData["tel"]             = $this->lang_item("tel");
		$tabData["registro_por"]    = $this->lang_item("registro_por");
		$tabData["fecha_registro"]  = $this->lang_item("fecha_registro");
		$tabData["list_entidad"]    = $entidades;
		$tabData["entidad"]         = $this->lang_item("entidad");
        $tabData['sucursal']        = $detalle[0]['sucursal'];
		$tabData['clave_corta']     = $detalle[0]['clave_corta'];
        $tabData['razon_social']    = $detalle[0]['razon_social'];
        $tabData['rfc']             = $detalle[0]['rfc'];
        $tabData['direccion']       = $detalle[0]['direccion'];
        $tabData['telefono']        = $detalle[0]['telefono'];
        $tabData['timestamp']       = $detalle[0]['registro'];
        $tabData['button_save']     = $btn_save;
        
        $this->load_database('global_system');
        $this->load->model('users_model');
        
        $usuario_registro               = $this->users_model->search_user_for_id($detalle[0]['id_usuario']);
        $tabData['registro_por']    	= $this->lang_item("registro_por",false);
        $tabData['usuario_registro']	= text_format_tpl($usuario_registro[0]['name'],"u");
		$uri_view   					= $this->modulo.'/'.$this->submodulo.'/'.$this->submodulo.'_'.$seccion;
		echo json_encode( $this->load_view_unique($uri_view ,$tabData, true));
	}

	public function actualizar()
	{
		$incomplete  = $this->ajax_post('incomplete');
		if($incomplete>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			$json_respuesta = array(
						 'id' 		=> 0
						,'contenido'=> alertas_tpl('error', $msg ,false)
						,'success' 	=> false
				);
		}
		else
		{
			$sqlData = array(
						 'id_sucursal'	    => $this->ajax_post('id_sucursal')
						,'sucursal' 		=> $this->ajax_post('sucursal')
						,'clave_corta' 		=> $this->ajax_post('clave_corta')
						,'razon_social'		=> $this->ajax_post('razon_social')
						,'rfc'				=> $this->ajax_post('rfc')
						,'id_entidad'		=> $this->ajax_post('id_entidad')
						,'telefono'			=> $this->ajax_post('telefono')
						,'direccion'		=> $this->ajax_post('direccion')
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

	public function agregar()
	{
		$seccion       = $this->modulo.'/'.$this->submodulo.'/sucursales_save';
		$entidades_array = array(
					 'data'		=> $this->db_model2->get_entidades('','','',false)
					,'value' 	=> 'id_administracion_entidad'
					,'text' 	=> array('entidad')
					,'name' 	=> "lts_entidades"
					,'class' 	=> "requerido"
					);
		$entidades    = dropdown_tpl($entidades_array);
		$btn_save     = form_button(array('class'=>"btn btn-primary",'name' => 'save_almacen','onclick'=>'agregar()' , 'content' => $this->lang_item("btn_guardar") ));
		$btn_reset     = form_button(array('class'=>"btn btn-primary",'name' => 'reset','value' => 'reset','onclick'=>'clean_formulario()','content' => $this->lang_item("btn_limpiar")));

		$tab_1["nombre_sucursal"]  = $this->lang_item("nombre_sucursal");
		$tab_1["cvl_corta"]        = $this->lang_item("clave_corta");
		$tab_1["r_social"]         = $this->lang_item("rs");
		$tab_1["r_f_c"]            = $this->lang_item("rfc");
		$tab_1["tel"]              = $this->lang_item("telefono");
		$tab_1["list_entidad"]     = $entidades;
		$tab_1["entidad"]          = $this->lang_item("entidad");
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
			$sucursal = $this->ajax_post('sucursal');
			$clave_corta  = $this->ajax_post('clave_corta');
			$razon_social  = $this->ajax_post('razon_social');
			$rfc  = $this->ajax_post('rfc');
			$telefono = $this->ajax_post('tel');
			$entidad = $this->ajax_post('id_entidad');
			$direccion = $this->ajax_post('direccion');
			$data_insert = array('sucursal' => $sucursal,
								 'clave_corta'  => $clave_corta,
								 'direccion'    => $direccion,
								 'id_usuario'   => $this->session->userdata('id_usuario'),
								 'id_entidad'   => $entidad,
								 'razon_social' => $razon_social,
								 'rfc'          => $rfc,
								 'telefono'     => $telefono,  
								 'registro'    => $this->timestamp());
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
		$sqlData = array(
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

		$params = array(	'tittle'  => $this->lang_item("Sucursales"),
							'items'   => $set_data,
							'headers' => $set_heading
						);
		
		$this->excel->generate_xlsx($params);
	}
} 