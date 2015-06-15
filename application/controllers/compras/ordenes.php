<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ordenes extends Base_Controller { 
/**
* Nombre:		Ordenes de compra
* Ubicación:	Compras>Ordenes
* Descripción:	Funcionamiento para la sección de ordenes de compra
* @author:		Oscar Maldonado - OM
* Creación: 	2015-05-11
* Modificación:	OM-2015-05-14
*/
	private $modulo;
	private $submodulo;
	private $view_content, $uri_view_principal;
	private $path;
	private $icon;
	private $offset, $limit_max;
	private $tab_inicial, $tab = array(), $tab_indice = array();
	
	public function __construct(){
		parent::__construct();
		$this->modulo 				= 'compras';
		$this->submodulo			= 'ordenes';
		$this->icon 				= 'fa fa-file-text'; #Icono de modulo
		$this->path 				= $this->modulo.'/'.$this->submodulo.'/';
		$this->view_content 		= 'content';
		$this->uri_view_principal 	= $this->modulo.'/'.$this->view_content;
		$this->limit_max			= 10;
		$this->offset				= 0;
		// Tabs
		$this->tab_inicial 			= 2;
		$this->tab_indice 		= array(
									 'agregar'
									,'listado'
									,'detalle'
									,'articulos'
								);
		for($i=0; $i<=count($this->tab_indice)-1; $i++){
			$this->tab[$this->tab_indice[$i]] = $this->tab_indice[$i];
		}
		// DB Model
		$this->load->model($this->modulo.'/'.$this->submodulo.'_model','db_model');
		$this->load->model('users_model','users_model');
		$this->load->model('administracion/sucursales_model','sucursales_model');
		$this->load->model('administracion/formas_de_pago_model','formas_de_pago_model');
		$this->load->model('administracion/creditos_model','creditos_model');
		$this->load->model('administracion/variables_model','variables_model');
		$this->load->model('compras/listado_precios_model','listado_precios_model');
		// Diccionario
		$this->lang->load($this->modulo.'/'.$this->submodulo,"es_ES");
	}

	public function config_tabs(){
		// Creación de tabs en el contenedor principal
		for($i=1; $i<=count($this->tab); $i++){
			${'tab_'.$i} = $this->tab [$this->tab_indice[$i-1]];
		}
		$path  	= $this->path;
		$pagina =(is_numeric($this->uri_segment_end()) ? $this->uri_segment_end() : "");
		// Nombre de Tabs
		$config_tab['names']    = array(
										 $this->lang_item($tab_1)
										,$this->lang_item($tab_2)
										,$this->lang_item($tab_3)
										,$this->lang_item($tab_4)
								); 
		// Href de tabs
		$config_tab['links']    = array(
										 $path.$tab_1
										,$path.$tab_2.'/'.$pagina
										,$tab_3
										,$tab_4
								); 
		// Accion de tabs
		$config_tab['action']   = array(
										 'load_content'
										,'load_content'
										,''
										,''
								);
		// Atributos 
		$config_tab['attr']     = array('','', array('style' => 'display:none'),array('style' => 'display:none'));
		return $config_tab;
	}
	public function index(){		
		// Carga de pagina inicial
		$tabl_inicial 			  = $this->tab_inicial;
		$view_listado    		  = $this->listado();		
		$contenidos_tab           = $view_listado;
		$data['titulo_seccion']   = $this->lang_item("titulo_seccion");
		$data['titulo_submodulo'] = $this->lang_item("titulo_submodulo");
		$data['icon']             = $this->icon;
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);	
		$js['js'][]  = array('name' => $this->submodulo, 'dirname' => $this->modulo);
		$this->load_view($this->uri_view_principal, $data, $js);
	}
	public function listado($offset=0){
		// Crea tabla con listado de elementos capturados 
		$seccion 		= '';
		$accion 		= $this->tab['listado'];
		$tab_detalle	= $this->tab['detalle'];
		$limit 			= $this->limit_max;
		$uri_view 		= $this->modulo.'/'.$accion;
		$url_link 		= $this->path.$seccion.$accion;		
		$buttonTPL 		= '';

		$filtro  = ($this->ajax_post('filtro')) ? $this->ajax_post('filtro') : "";
		$sqlData = array(
			 'buscar' => $filtro
			,'offset' => $offset
			,'limit'  => $limit
		);
		$uri_segment  			  = $this->uri_segment(); 
		$total_rows   			  = count($this->db_model->db_get_data($sqlData));
		$sqlData['aplicar_limit'] = false;
		$list_content 			  = $this->db_model->db_get_data($sqlData);
		$url          			  = base_url($url_link);
		$paginador    			  = $this->pagination_bootstrap->paginator_generate($total_rows, $url, $limit, $uri_segment, array('evento_link' => 'onclick', 'function_js' => 'load_content', 'params_js'=>'1'));

		if($total_rows){
			foreach ($list_content as $value) {
				// Evento de enlace
				$atrr = array(
								'href' => '#',
							  	'onclick' => $tab_detalle.'('.$value['id_compras_orden'].')'
						);
				// Acciones
				$accion_id 						= $value['id_compras_orden'];
				$btn_acciones['detalle'] 		= '<span id="ico-detalle_'.$accion_id.'" class="ico_detalle fa fa-search-plus" onclick="detalle('.$accion_id.')" title="'.$this->lang_item("detalle").'"></span>';
				$btn_acciones['agregar'] 		= '<span id="ico-articulos_'.$accion_id.'" class="ico_articulos fa fa-cart-plus" onclick="articulos('.$accion_id.')" title="'.$this->lang_item("agregar_articulos").'"></span>';
				$btn_acciones['eliminar']       = '<span id="ico-eliminar_'.$accion_id.'" class="ico_eliminar fa fa-times" onclick="eliminar('.$accion_id.')" title="'.$this->lang_item("eliminar").'"></span>';
				$acciones = implode('&nbsp;&nbsp;&nbsp;',$btn_acciones);
				// Datos para tabla
				$tbl_data[] = array('id'             => $value['id_compras_orden'],
									'orden_num'      => tool_tips_tpl($value['orden_num'], $this->lang_item("tool_tip"), 'right' , $atrr),
									'descripcion'    => tool_tips_tpl($value['descripcion'], $this->lang_item("tool_tip"), 'right' , $atrr),
									'timestamp'      => $value['timestamp'],
									'entrega_fecha'  => $value['entrega_fecha'],
									'estatus'   	 => $value['estatus'],
									'acciones' 		 => $acciones
									);
			}
			// Plantilla
			$tbl_plantilla = array ('table_open'  => '<table id="tbl_grid" class="table table-bordered responsive ">');
			// Titulos de tabla
			$this->table->set_heading(	$this->lang_item("id"),
										$this->lang_item("orden_num"),										
										$this->lang_item("descripcion"),
										$this->lang_item("fecha_registro"),
										$this->lang_item("entrega_fecha"),
										$this->lang_item("estatus"),
										$this->lang_item("acciones")
									);
			// Generar tabla
			$this->table->set_template($tbl_plantilla);
			$tabla = $this->table->generate($tbl_data);
			// XLS
			$buttonTPL = array( 'text'   => $this->lang_item("btn_xlsx"), 
							'iconsweets' => 'iconsweets-excel',
							'href'       => base_url($this->path.$seccion).'/export_xlsx?filtro='.base64_encode($filtro)
							);
		}else{
			$msg   = $this->lang_item("msg_query_null");
			$tabla = alertas_tpl('', $msg ,false);
		}
		$tabData['filtro']    = (isset($filtro) && $filtro!="") ? sprintf($this->lang_item("msg_query_search",false),$total_rows , $filtro) : "";
		$tabData['tabla']     = $tabla;
		$tabData['paginador'] = $paginador;
		$tabData['item_info'] = $this->pagination_bootstrap->showing_items($limit, $offset, $total_rows);
		$tabData['export']    = button_tpl($buttonTPL);

		if($this->ajax_post(false)){
			echo json_encode( $this->load_view_unique($uri_view , $tabData, true));
		}else{
			return $this->load_view_unique($uri_view , $tabData, true);
		}
	}
	public function detalle(){
		// Crea formulario de detalle y edición
		$seccion 			= '';
		$accion 			= $this->tab['detalle'];
		$id_compras_orden 	= $this->ajax_post('id_compras_orden');
		$detalle  			= $this->db_model->get_orden_unico($id_compras_orden);
		$btn_save       	= form_button(array('class'=>"btn btn-primary",'name' => 'actualizar' , 'onclick'=>'actualizar()','content' => $this->lang_item("btn_guardar") ));
		$btn_eliminar       = form_button(array('class'=>"btn btn-primary",'name' => 'eliminar' , 'onclick'=>'eliminar()','content' => $this->lang_item("btn_eliminar") ));
		//se agrega para mostrar la opcion de proveedor y No. prefactura, solo si se selcciono proveedor en tipo de orden
		if($detalle[0]['id_orden_tipo']==2){
			$style='style="display:none"';
			$class ='';
		}else{
			$style='';
			$class ='requerido';
		}	
		$dropArray = array(
					'data'		=> $this->db_model->db_get_proveedores()
					,'selected' => $detalle[0]['id_proveedor'] 
					,'value' 	=> 'id_compras_proveedor'
					,'text' 	=> array('clave_corta','razon_social')
					,'name' 	=> "id_proveedor"
					,'class' 	=> $class
				);
		$proveedores    = dropdown_tpl($dropArray);

		$dropArray2 = array(
					 'data'		=> $this->sucursales_model->db_get_data()
					 ,'selected'=> $detalle[0]['id_sucursal']
					,'value' 	=> 'id_sucursal'
					,'text' 	=> array('clave_corta','sucursal')
					,'name' 	=> "id_sucursal"
					,'class' 	=> "requerido"
					,'event'    => array('event'       => 'onchange',
				   						 'function'    => 'show_direccion',
				   						 'params'      => array('this.value'),
				   						 'params_type' => array(0)
									)
				);
		$sucursales	    = dropdown_tpl($dropArray2);

		$dropArray3 = array(
					 'data'		=> $this->formas_de_pago_model->db_get_data()
					 ,'selected'=> $detalle[0]['id_forma_pago']
					,'value' 	=> 'id_forma_pago'
					,'text' 	=> array('clave_corta','descripcion')
					,'name' 	=> "id_forma_pago"
					,'class' 	=> "requerido"
				);
		$forma_pago	    = dropdown_tpl($dropArray3);

		$dropArray4 = array(
					 'data'		=> $this->creditos_model->db_get_data()
					 ,'selected'=> $detalle[0]['id_credito']
					,'value' 	=> 'id_administracion_creditos'
					,'text' 	=> array('clave_corta','credito')
					,'name' 	=> "id_administracion_creditos"
					,'class' 	=> "requerido"
				);
		$creditos	    = dropdown_tpl($dropArray4);

		$dropArray5 = array(
					 'data'		=> $this->db_model->db_get_tipo_orden()
					 ,'selected'=> $detalle[0]['id_orden_tipo']
					,'value' 	=> 'id_orden_tipo'
					,'text' 	=> array('orden_tipo')
					,'name' 	=> "id_orden_tipo"
					,'class' 	=> "requerido"
					,'disabled' => 'disabled="disabled"'
					,'event'    => array('event'       => 'onchange',
				   						 'function'    => 'show_proveedor',
				   						 'params'      => array('this.value'),
				   						 'params_type' => array(0)
									)
				);
		$orden_tipo	    = dropdown_tpl($dropArray5);
		// 

		$fec=explode('-',$detalle[0]['entrega_fecha']);
		$entrega_fecha=$fec[2].'/'.$fec[1].'/'.$fec[0];
		$fec2=explode('-',$detalle[0]['orden_fecha']);
		$orden_fecha=$fec2[2].'/'.$fec2[1].'/'.$fec2[0];
		$tabData['id_compras_orden']		 = $id_compras_orden;
		$tabData['orden_num']   			 = $this->lang_item("orden_num",false);
		$tabData['orden_num_value']	 		 = $detalle[0]['orden_num'];
        $tabData['proveedor'] 	 			 = $this->lang_item("proveedor",false);
		$tabData['list_proveedores']		 = $proveedores;
		$tabData['sucursal']     			 = $this->lang_item("sucursal",false);
        $tabData['list_sucursales']			 = $sucursales;
        $tabData['descripcion']       		 = $this->lang_item("descripcion",false);
        $tabData['descripcion_value'] 		 = $detalle[0]['descripcion'];
        $tabData['lbl_fecha_registro']    	 = $this->lang_item("lbl_fecha_registro",false);
        $tabData['timestamp']         		 = $detalle[0]['timestamp'];
        $tabData['button_save']       		 = $btn_save;
        $tabData['button_delete']       	 = $btn_eliminar;
        $tabData['orden_fecha']   		     = $this->lang_item("orden_fecha",false);
		$tabData['orden_fecha_value']	 	 = $orden_fecha;
        $tabData['entrega_direccion']        = $this->lang_item("entrega_direccion",false);
        $tabData['entrega_direccion_value']	 = $detalle[0]['entrega_direccion'];
		$tabData['entrega_fecha']            = $this->lang_item("entrega_fecha",false);
        $tabData['entrega_fecha_value']	     = $entrega_fecha;
        $tabData['prefactura_num']       	 = $this->lang_item("prefactura_num",false);
        $tabData['prefactura_num_value'] 	 = $detalle[0]['prefactura_num'];
        $tabData['observaciones']    	     = $this->lang_item("observaciones",false);
        $tabData['observaciones_value']      = $detalle[0]['observaciones'];
        $tabData['forma_pago']     			 = $this->lang_item("forma_pago",false);
        $tabData['creditos']     			 = $this->lang_item("creditos",false);
        $tabData['list_forma_pago']			 = $forma_pago;
		$tabData['list_creditos']			 = $creditos;
		$tabData['orden_tipo']  			 = $this->lang_item("orden_tipo",false);
		$tabData['list_orden_tipo']			 = $orden_tipo;
		$tabData['style']					 = $style;
		$tabData['class']					 = $class;
		$tabData['lbl_ultima_modificacion']  = $this->lang_item('lbl_ultima_modificacion', false);

		$this->load->model('users_model');
    	$usuario_registro              = $this->users_model->search_user_for_id($detalle[0]['id_usuario']);
        $tabData['usuario_registro']   = text_format_tpl($usuario_registro[0]['name'],"u");

       if($detalle[0]['edit_id_usuario']){
        	$usuario_registro                   = $this->users_model->search_user_for_id($detalle[0]['edit_id_usuario']);
        	$usuario_name 				        = text_format_tpl($usuario_registro[0]['name'],"u");
        	$tabData['val_ultima_modificacion'] = sprintf($this->lang_item('val_ultima_modificacion', false), $this->timestamp_complete($detalle[0]['edit_timestamp']), $usuario_name);
    	}else{
    		$usuario_name = '';
    		$tabData['val_ultima_modificacion'] = $this->lang_item('lbl_sin_modificacion', false);
    	}
		$uri_view  = $this->path.$this->submodulo.'_'.$accion;
		echo json_encode( $this->load_view_unique($uri_view ,$tabData, true));
	}
	public function agregar(){
		// Crea formulario para agregar nuevo elemento
		$seccion 		= '';
		$accion 		= $this->tab['agregar'];
		$uri_view   	= $this->path.$this->submodulo.'_'.$accion;
		// Listas
		$dropArray = array(
					 'data'		=> $this->db_model->db_get_proveedores()
					,'value' 	=> 'id_compras_proveedor'
					,'text' 	=> array('clave_corta','razon_social')
					,'name' 	=> "id_proveedor"
				);
		$proveedores    = dropdown_tpl($dropArray);

		$dropArray2 = array(
					 'data'		=> $this->sucursales_model->db_get_data()
					,'value' 	=> 'id_sucursal'
					,'text' 	=> array('clave_corta','sucursal')
					,'name' 	=> "id_sucursal"
					,'class' 	=> "requerido"
					,'event'    => array('event'       => 'onchange',
				   						 'function'    => 'show_direccion',
				   						 'params'      => array('this.value'),
				   						 'params_type' => array(0)
									)
				);
		$sucursales	    = dropdown_tpl($dropArray2);

		$dropArray3 = array(
					 'data'		=> $this->formas_de_pago_model->db_get_data()
					,'value' 	=> 'id_forma_pago'
					,'text' 	=> array('clave_corta','descripcion')
					,'name' 	=> "id_forma_pago"
					,'class' 	=> "requerido"
				);
		$forma_pago	    = dropdown_tpl($dropArray3);

		$dropArray4 = array(
					 'data'		=> $this->creditos_model->db_get_data()
					,'value' 	=> 'id_administracion_creditos'
					,'text' 	=> array('clave_corta','credito')
					,'name' 	=> "id_administracion_creditos"
					,'class' 	=> "requerido"
				);
		$creditos	    = dropdown_tpl($dropArray4);

		$dropArray5 = array(
					 'data'		=> $this->db_model->db_get_tipo_orden()
					,'value' 	=> 'id_orden_tipo'
					,'text' 	=> array('orden_tipo')
					,'name' 	=> "id_orden_tipo"
					,'class' 	=> "requerido"
					,'event'    => array('event'       => 'onchange',
				   						 'function'    => 'show_proveedor',
				   						 'params'      => array('this.value'),
				   						 'params_type' => array(0)
									)
				);
		$orden_tipo	    = dropdown_tpl($dropArray5);
		// Botones
		$btn_save       = form_button(array('class'=>"btn btn-primary",'name' => 'save','onclick'=>'insert()' , 'content' => $this->lang_item("btn_guardar") ));
		$btn_reset      = form_button(array('class'=>"btn btn-primary",'name' => 'reset','value' => 'reset','onclick'=>'clean_formulario()','content' => $this->lang_item("btn_limpiar")));
		// Etiquetas
		$tabData['proveedor'] 			= $this->lang_item("proveedor",false);
        $tabData['list_proveedores']	= $proveedores;
        $tabData['sucursal']     		= $this->lang_item("sucursal",false);
        $tabData['list_sucursales']		= $sucursales;
        $tabData['forma_pago']     		= $this->lang_item("forma_pago",false);
        $tabData['list_forma_pago']		= $forma_pago;
        $tabData['creditos']     		= $this->lang_item("creditos",false);
        $tabData['list_creditos']		= $creditos;
        $tabData['descripcion']     	= $this->lang_item("descripcion",false);
        $tabData['fecha_registro']  	= $this->lang_item("fecha_registro",false);
        $tabData['timestamp']       	= date('Y-m-d H:i');
        $tabData['orden_fecha']     	= $this->lang_item("orden_fecha",false);
        $tabData['entrega_direccion']   = $this->lang_item("entrega_direccion",false);
        $tabData['entrega_fecha']     	= $this->lang_item("entrega_fecha",false);
        $tabData['prefactura_num']     	= $this->lang_item("prefactura_num",false);
        $tabData['observaciones']     	= $this->lang_item("observaciones",false);        
        $tabData['orden_tipo']  		= $this->lang_item("orden_tipo",false);
        $tabData['list_orden_tipo']		= $orden_tipo;    
        $tabData['button_save']     	= $btn_save;
        $tabData['button_reset']    	= $btn_reset;
        // Respuesta
        if($this->ajax_post(false)){
				echo json_encode($this->load_view_unique($uri_view , $tabData, true));
		}else{
			return $this->load_view_unique($uri_view , $tabData, true);
		}
	}
	public function insert(){
		// Recibe datos de formulario e inserta un nuevo registro en la BD
		$incomplete  = $this->ajax_post('incomplete');
		if($incomplete>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			$json_respuesta = array(
						 'id' 		=> 0
						,'contenido'=> alertas_tpl('error', $msg ,false)
						,'success' 	=> false
				);
		}else{
			$fec=explode('/',$this->ajax_post('entrega_fecha'));
			$entrega_fecha=$fec[2].'-'.$fec[1].'-'.$fec[0];
			$fec2=explode('/',$this->ajax_post('orden_fecha'));
			$orden_fecha=$fec2[2].'-'.$fec2[1].'-'.$fec2[0];
			$filtro=1;
				$sqlData = array('buscar'=> $filtro);
				$no_orden=$this->variables_model->db_get_data($sqlData);
			$sqlData = array(
						'orden_num' 		 => $no_orden[0]['valor']+1
						,'id_orden_tipo' 	 => $this->ajax_post('id_orden_tipo')
						,'orden_fecha' 		 => $orden_fecha
						,'id_proveedor' 	 => $this->ajax_post('id_proveedor')
						,'descripcion'		 => $this->ajax_post('descripcion')
						,'id_sucursal'  	 => $this->ajax_post('id_sucursal')
						,'entrega_direccion' => $this->ajax_post('entrega_direccion')
						,'entrega_fecha'     => $entrega_fecha
						,'id_forma_pago'     => $this->ajax_post('id_forma_pago')
						,'id_credito' 		 => $this->ajax_post('id_administracion_creditos')
						,'prefactura_num' 	 => $this->ajax_post('prefactura_num')
						,'observaciones' 	 => $this->ajax_post('observaciones')
						,'id_usuario' 		 => $this->session->userdata('id_usuario')
						,'timestamp'  		 => $this->timestamp()
						);
			$insert = $this->db_model->insert($sqlData);
			if($insert){
				$sqlData2 = array(
							'valor' 		  => $no_orden[0]['valor']+1,
							'id_vars'		  => 1,
							'edit_id_usuario'           	=> $this->session->userdata('id_usuario'),
							'edit_timestamp' 				=>$this->timestamp()
									);	
				$insert2 = $this->variables_model->update($sqlData2);	
				if($insert2){
					//$msg = $this->lang_item("msg_insert_success",false);
					$msg = sprintf($this->lang_item('msg_insert_orden_success', false), $no_orden[0]['valor']+1);
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
	public function actualizar(){
		// Recibe datos de formulario y actualiza un registro existente en la BD
		$incomplete  = $this->ajax_post('incomplete');
		if($incomplete>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			$json_respuesta = array(
						 'id' 		=> 0
						,'contenido'=> alertas_tpl('error', $msg ,false)
						,'success' 	=> false
				);
		}else{
			$fec=explode('/',$this->ajax_post('entrega_fecha'));
			$entrega_fecha=$fec[2].'-'.$fec[1].'-'.$fec[0];
			$fec2=explode('/',$this->ajax_post('orden_fecha'));
			$orden_fecha=$fec2[2].'-'.$fec2[1].'-'.$fec2[0];
			$sqlData = array(
						 'id_compras_orden'	 => $this->ajax_post('id_compras_orden')
						,'orden_fecha' 		 => $orden_fecha
						,'id_proveedor' 	 => $this->ajax_post('id_proveedor')
						,'descripcion'		 => $this->ajax_post('descripcion')
						,'id_sucursal'  	 => $this->ajax_post('id_sucursal')
						,'entrega_direccion' => $this->ajax_post('entrega_direccion')
						,'entrega_fecha'     => $entrega_fecha
						,'id_forma_pago'     => $this->ajax_post('id_forma_pago')
						,'id_credito' 		 => $this->ajax_post('id_administracion_creditos')
						,'prefactura_num' 	 => $this->ajax_post('prefactura_num')
						,'observaciones' 	 => $this->ajax_post('observaciones')
						,'edit_timestamp'  	 => $this->timestamp()
						,'edit_id_usuario'   => $this->session->userdata('id_usuario')
						);
			$update = $this->db_model->db_update_data($sqlData);
			if($update){
				$msg = $this->lang_item("msg_update_success",false);
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
	public function eliminar(){
		$msj_grid = $this->ajax_post('msj_grid');
		$sqlData = array(
						 'id_compras_orden'	=> $this->ajax_post('id_compras_orden')
						,'estatus' 		 =>6
						,'activo' 		 =>0
						,'edit_timestamp'  	 => $this->timestamp()
						,'edit_id_usuario'   => $this->session->userdata('id_usuario')
						);
			 $insert = $this->db_model->db_update_data($sqlData);
			if($insert){
				$msg = $this->lang_item("msg_delete_success",false);
				$json_respuesta = array(
						 'id' 		=> 1
						,'contenido'=> alertas_tpl('success', $msg ,false)
						,'success' 	=> true
						,'msj_grid'	=> $msj_grid
				);
			}else{
				$msg = $this->lang_item("msg_err_clv",false);
				$json_respuesta = array(
						 'id' 		=> 0
						,'contenido'=> alertas_tpl('', $msg ,false)
						,'success' 	=> false
						,'msj_grid'	=> $msj_grid
				);
			}
		echo json_encode($json_respuesta);
	}
	public function articulos(){
		// Agregar articulos a una orden de compra
		$seccion 			= '';
		$accion 			= $this->tab['articulos'];
		$id_compras_orden 	= $this->ajax_post('id_compras_orden');
		$detalle  			= $this->db_model->get_orden_unico($id_compras_orden);
		$btn_save       	= form_button(array('class'=>"btn btn-primary",'name' => 'actualizar' , 'onclick'=>'agregar_articulos()','content' => $this->lang_item("btn_guardar") ));
		//se agrega para mostrar la opcion de proveedor y No. prefactura, solo si se selcciono proveedor en tipo de orden
		if($detalle[0]['id_orden_tipo']==2){
			$style='style="display:none"';
			$class ='';
		}else{
			$style='';
			$class ='requerido';
		}	
		if($detalle[0]['id_proveedor']>0){
			//dump_var($detalle[0]['id_proveedor']);
			$get_data=$this->listado_precios_model->db_get_data_x_proveedor($detalle[0]['id_proveedor']);
		}else{
			$get_data=$this->listado_precios_model->db_get_data_x_proveedor();
		}
		//dump_var($get_data);
		$dropArray4 = array(
					 'data'		=> $get_data
					,'value' 	=> 'id_compras_articulo_precios'
					,'text' 	=> array('articulo','presentacion','embalaje','peso_unitario','cl_um')
					,'name' 	=> "lts_articulos"
					,'event'    => array('event'       => 'onchange',
				   						 'function'    => 'test',
				   						 'params'      => array('this.value'),
				   						 'params_type' => array(0)
									)
					,'class' 	=> "articulos_lista"
				);
		$list_articulos  = dropdown_tpl($dropArray4);

		$data='';
		$proveedores    = $this->db_model->db_get_proveedores($data,$detalle[0]['id_proveedor']);
		$sucursales	    = $this->sucursales_model->get_orden_unico_sucursal($detalle[0]['id_sucursal']);
		$forma_pago	    = $this->formas_de_pago_model->get_orden_unico_formapago($detalle[0]['id_forma_pago']);
		$creditos	    = $this->creditos_model->get_orden_unico_credito($detalle[0]['id_credito']);
		$orden_tipo	    = $this->db_model->db_get_tipo_orden($detalle[0]['id_orden_tipo']);
		
		/*$articulos = $get_data;
		$table = "";
		foreach ($articulos as $key => $value) {
			$table .='<tbody style="display:none" id="'.$value['id_compras_articulo_precios'].'">
						<tr>
							<td>
								<span name="proveedor">'.$value['nombre_comercial'].'</span>
								<input type="hidden" value="'.$value['id_compras_articulo_precios'].'" data-campo="id_compras_articulo_precios['.$key.']" id="id_compras_articulo_precios[]">
							</td>
							<td>
								'.$value['articulo'].'
							</td>
							<td>
								'.$value['cl_presentacion'].'
							</td>
							<td class="right">
								<input type="hidden" id="costo_sin_impuesto" value="'.$value['costo_sin_impuesto'].'">
								'.$value['costo_sin_impuesto'].'
							</td>
							<td>
								<input type="text" id="cantidad" data-campo="cantidad['.$value['id_compras_articulo_precios'].']" class="input-small" onkeyup="calcula_costo2('.$value['id_compras_articulo_precios'].')">
							</td>
							<td>
								<!--<input type="text" id="costo2" data-campo="costo2['.$value['id_compras_articulo_precios'].']" class="input-small">-->
								<span id="costo_2_'.$value['id_compras_articulo_precios'].'"></span>
							</td>
							<td>
								<input type="text" id="iva" data-campo="descuento['.$value['id_compras_articulo_precios'].']" class="input-small">
							</td>
							<td>
								<input type="hidden" value="'.$value['id_impuesto'].'" name="impuesto['.$value['id_compras_articulo_precios'].']">
								'.$value['impuesto'].'
							</td>
							<td>
								<input type="text" id="iva" data-campo="valor_imp['.$value['id_compras_articulo_precios'].']" class="input-small">
							</td>
							<td>
								<input type="text" id="iva" data-campo="total['.$value['id_compras_articulo_precios'].']" class="input-small">
							</td>
						</tr>
					</tbody>';
		}*/
		$fec=explode('-',$detalle[0]['entrega_fecha']);
		$entrega_fecha=$fec[2].'/'.$fec[1].'/'.$fec[0];
		$fec2=explode('-',$detalle[0]['orden_fecha']);
		$orden_fecha=$fec2[2].'/'.$fec2[1].'/'.$fec2[0];
		$tabData['id_compras_orden']		 = $id_compras_orden;
		$tabData['orden_num']   			 = $this->lang_item("orden_num",false);
        $tabData['proveedor'] 	 			 = $this->lang_item("proveedor",false);
		$tabData['sucursal']     			 = $this->lang_item("sucursal",false);
        $tabData['descripcion']       		 = $this->lang_item("descripcion",false);
        $tabData['lbl_fecha_registro']    	 = $this->lang_item("lbl_fecha_registro",false);
        $tabData['orden_fecha']   		     = $this->lang_item("orden_fecha",false);
        $tabData['entrega_direccion']        = $this->lang_item("entrega_direccion",false);
		$tabData['entrega_fecha']            = $this->lang_item("entrega_fecha",false);
        $tabData['prefactura_num']       	 = $this->lang_item("prefactura_num",false);
        $tabData['observaciones']    	     = $this->lang_item("observaciones",false);
        $tabData['forma_pago']     			 = $this->lang_item("forma_pago",false);
        $tabData['creditos']     			 = $this->lang_item("creditos",false);
		$tabData['orden_tipo']  			 = $this->lang_item("orden_tipo",false);
		$tabData['lst_articulos_label'] 	 = $this->lang_item("lst_articulos_label",false);
		//DATA
		$tabData['orden_num_value']	 		 = $detalle[0]['orden_num'];
		$tabData['list_proveedores']		 = $proveedores[0]['razon_social'];
		$tabData['list_sucursales']			 = $sucursales[0]['sucursal'];
		$tabData['descripcion_value'] 		 = $detalle[0]['descripcion'];
		$tabData['timestamp']         		 = $detalle[0]['timestamp'];
		$tabData['button_save']       		 = $btn_save;
		$tabData['orden_fecha_value']	 	 = $orden_fecha;
		$tabData['entrega_direccion_value']	 = $detalle[0]['entrega_direccion'];
		$tabData['entrega_fecha_value']	     = $entrega_fecha;
		$tabData['prefactura_num_value'] 	 = $detalle[0]['prefactura_num'];
		$tabData['observaciones_value']      = $detalle[0]['observaciones'];
		$tabData['list_forma_pago']			 = $forma_pago[0]['forma_pago'];
		$tabData['list_creditos']			 = $creditos[0]['credito'];
		$tabData['list_orden_tipo']			 = $orden_tipo[0]['descripcion'];
		$tabData['list_arti']			     = $list_articulos ;
		$tabData['style']					 = $style;
		$tabData['class']					 = $class;
		$tabData['lbl_ultima_modificacion']  = $this->lang_item('lbl_ultima_modificacion', false);

		$this->load->model('users_model');
    	$usuario_registro              = $this->users_model->search_user_for_id($detalle[0]['id_usuario']);
        $tabData['usuario_registro']   = text_format_tpl($usuario_registro[0]['name'],"u");

       if($detalle[0]['edit_id_usuario']){
        	$usuario_registro                   = $this->users_model->search_user_for_id($detalle[0]['edit_id_usuario']);
        	$usuario_name 				        = text_format_tpl($usuario_registro[0]['name'],"u");
        	$tabData['val_ultima_modificacion'] = sprintf($this->lang_item('val_ultima_modificacion', false), $this->timestamp_complete($detalle[0]['edit_timestamp']), $usuario_name);
    	}else{
    		$usuario_name = '';
    		$tabData['val_ultima_modificacion'] = $this->lang_item('lbl_sin_modificacion', false);
    	}
		$uri_view  = $this->path.$this->submodulo.'_'.$accion;
		echo json_encode( $this->load_view_unique($uri_view ,$tabData, true));
	}
	public function get_data_articulo(){
		$id_compras_articulo_precios 	= $this->ajax_post('id_compras_articulo_precios');
		/////¨PASAR LA CONSULTA A RREGLO PARA SOLO TENER UNA *******************************************************************************************************
		$get_data=$this->listado_precios_model->db_get_data_x_articulos($id_compras_articulo_precios);
		$table='<tr id="'.$get_data[0]['id_compras_articulo_precios'].'">
				<td>
					<span name="proveedor">'.$get_data[0]['nombre_comercial'].'</span>
					<input type="hidden" value="'.$get_data[0]['id_compras_articulo_precios'].'" data-campo="id_compras_articulo_precios['.$get_data[0]['id_compras_articulo_precios'].']" id="idarticuloprecios_'.$get_data[0]['id_compras_articulo_precios'].'">
				</td>
				<td>
					'.$get_data[0]['articulo'].'
				</td>
				<td>
					'.$get_data[0]['cl_presentacion'].'
				</td>
				<td class="right">
					<input type="hidden" id="costo_sin_impuesto_'.$get_data[0]['id_compras_articulo_precios'].'" value="'.$get_data[0]['costo_sin_impuesto'].'">
					'.$get_data[0]['costo_sin_impuesto'].'
				</td>
				<td>
					<input type="text" id="cantidad_'.$get_data[0]['id_compras_articulo_precios'].'" data-campo="cantidad['.$get_data[0]['id_compras_articulo_precios'].']" class="input-small" onkeyup="calcula_costo2('.$get_data[0]['id_compras_articulo_precios'].')">
				</td>
				<td>
					<input type="hidden" id="costo_2'.$get_data[0]['id_compras_articulo_precios'].'" value="">
					<span id="costo_2_'.$get_data[0]['id_compras_articulo_precios'].'"></span>
				</td>
				<td>
					<input type="text" id="descuento_'.$get_data[0]['id_compras_articulo_precios'].'" data-campo="descuento['.$get_data[0]['id_compras_articulo_precios'].']" class="input-small" onkeyup="calcula_subtotal('.$get_data[0]['id_compras_articulo_precios'].')">
				</td>
				<td>
					<!--<input type="text" id="costo2" data-campo="costo2['.$get_data[0]['id_compras_articulo_precios'].']" class="input-small">-->
					<span id="subtotal_'.$get_data[0]['id_compras_articulo_precios'].'"></span>
				</td>
				<td>
					<input type="hidden" value="'.$get_data[0]['impuesto'].'" id="impuesto_'.$get_data[0]['id_compras_articulo_precios'].'"name="impuesto['.$get_data[0]['id_compras_articulo_precios'].']">
					'.$get_data[0]['impuesto'].'
				</td>
				<td>
					<input type="hidden" value="" id="valor_hidden_impuesto_'.$get_data[0]['id_compras_articulo_precios'].'">
					<span id="valor_impuesto_'.$get_data[0]['id_compras_articulo_precios'].'"></span>
				</td>
				<td>
					<input type="hidden" value="" id="total_hidden_'.$get_data[0]['id_compras_articulo_precios'].'">
					<span id="total_'.$get_data[0]['id_compras_articulo_precios'].'"></span>
				</td>
				</tr>';
		 echo json_encode($table);
	}
	public function registrar_articulos(){
		$id_compras_articulo_precios 	= $this->ajax_post('id_compras_articulo_precios');
		$cantidad 	= $this->ajax_post('cantidad');
		$costo2 	= $this->ajax_post('costo2');
		$descuento 	= $this->ajax_post('descuento');
		$imp 	= $this->ajax_post('imp');
		$valor_imp 	= $this->ajax_post('valor_imp');
		$total 	= $this->ajax_post('total');
		$array=array(0 => $cantidad, 1 => $descuento,2 => $imp);
		dump_var($array);
	}
	public function export_xlsx(){
		$filtro      = ($this->ajax_get('filtro')) ?  base64_decode($this->ajax_get('filtro') ): "";
		$sqlData = array('buscar' => $filtro);
		$list_content = $this->db_model->db_get_data($sqlData);		

		if($list_content){
			foreach ($list_content as $value) {
				$set_data[] = array(
									$value['id_compras_orden'],
									$value['orden_num'],
									$value['orden_tipo'],
									$value['orden_fecha'],									 
									$value['razon_social'],
									$value['descripcion'],
									$value['sucursal'],
									$value['entrega_direccion'],
									$value['entrega_fecha'],
									$value['forma_pago'],
									$value['credito'],
									$value['prefactura_num'],
									$value['observaciones'],
									$value['timestamp'],
									$value['estatus']
								);
			}
			$set_heading = array(
								$this->lang_item("ID"),
								$this->lang_item("orden_num"),
								$this->lang_item("orden_tipo"),
								$this->lang_item("orden_fecha"),
								$this->lang_item("proveedor"),
								$this->lang_item("descripcion"),
								$this->lang_item("sucursal"),
								$this->lang_item("entrega_direccion"),
								$this->lang_item("entrega_fecha"),
								$this->lang_item("forma_pago"),
								$this->lang_item("credito"),
								$this->lang_item("prefactura_num"),
								$this->lang_item("observaciones"),
								$this->lang_item("fecha_registro"),
								$this->lang_item("estatus")
							);
		}

		$params = array(	'title'   => $this->lang_item("ordenes"),
							'items'   => $set_data,
							'headers' => $set_heading
						);
		$this->excel->generate_xlsx($params);
	}
	public function show_direccion(){
		$id_sucursal = $this->ajax_post('id_sucursal');
		$sucursal= $this->sucursales_model->get_orden_unico_sucursal($id_sucursal);
		echo json_encode($sucursal[0]['direccion']);

	}
}