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
								);
		for($i=0; $i<=count($this->tab_indice)-1; $i++){
			$this->tab[$this->tab_indice[$i]] = $this->tab_indice[$i];
		}
		// DB Model
		$this->load->model($this->modulo.'/'.$this->submodulo.'_model','db_model');
		$this->load->model('users_model','users_model');
		// $this->load->model($this->uri_modulo.'articulos_model','articulos_model');
		// $this->load->model($this->uri_modulo.'catalogos_model','catalogos_model');
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
		$filtro      	= ($this->ajax_post('filtro')) ? $this->ajax_post('filtro') : "";
		$sqlData = array(
			 'buscar'      	=> $filtro
			,'offset' 		=> $offset
			,'limit'      	=> $limit
			,'aplicar_limit'=> true
		);
		$uri_segment  = $this->uri_segment(); 
		$total_rows	  = count($this->db_model->db_get_total_rows($sqlData));
		$list_content = $this->db_model->db_get_data($sqlData);
		$url          = base_url($url_link);
		$paginador    = $this->pagination_bootstrap->paginator_generate($total_rows, $url, $limit, $uri_segment, array('evento_link' => 'onclick', 'function_js' => 'load_content', 'params_js'=>'1'));

		if($total_rows){
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
		$tabData['filtro']    = (isset($filtro) && $filtro!="") ? sprintf($this->lang_item("msg_query_search",false),$total_rows , $filtro) : "";
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
		// Crea formulario de detalle y edición
		$seccion 			= '';
		$accion 			= $this->tab['detalle'];
		$id_compras_orden 	= $this->ajax_post('id_compras_orden');
		$detalle  			= $this->db_model->get_orden_unico($id_compras_orden);
		$btn_save       	= form_button(array('class'=>"btn btn-primary",'name' => 'actualizar' , 'onclick'=>'actualizar()','content' => $this->lang_item("btn_guardar") ));

		$dropArray = array(
					'data'		=> $this->db_model->db_get_proveedores()
					,'selected' => $detalle[0]['id_proveedor'] 
					,'value' 	=> 'id_compras_proveedor'
					,'text' 	=> array('clave_corta','razon_social')
					,'name' 	=> "id_proveedor"
					,'class' 	=> "requerido"
					// ,'leyenda' 	=> ''
				);
		$proveedores    = dropdown_tpl($dropArray);
		$tabData['id_compras_orden']	= $id_compras_orden;
		$tabData['orden_num']   		= $this->lang_item("orden_num",false);
		$tabData['orden_num_value']	 	= $detalle[0]['orden_num'];
        $tabData['razon_social'] 	 	= $this->lang_item("razon_social",false);
		$tabData['list_proveedores']	= $proveedores;
        $tabData['descripcion']       	= $this->lang_item("descripcion",false);
        $tabData['descripcion_value'] 	= $detalle[0]['descripcion'];
        $tabData['fecha_registro']    	= $this->lang_item("fecha_registro",false);
        $tabData['timestamp']         	= $detalle[0]['timestamp'];
        $tabData['button_save']       	= $btn_save;
               
        if($detalle[0]['id_usuario']){
        	$usuario_registro           = $this->users_model->search_user_for_id($detalle[0]['id_usuario']);
        	$usuario_name 				= text_format_tpl($usuario_registro[0]['name'],"u");
    	}else{
    		$usuario_name = '';
    	}
        $tabData['registro_por']    	= $this->lang_item("registro_por",false);
        $tabData['usuario_registro']	= $usuario_name;
		$uri_view   					= $this->path.$this->submodulo.'_'.$accion;
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
					// ,'selected' => '' 
					,'value' 	=> 'id_compras_proveedor'
					,'text' 	=> array('clave_corta','razon_social')
					,'name' 	=> "id_proveedor"
					,'class' 	=> "requerido"
					// ,'leyenda' 	=> ''
				);
		$proveedores    = dropdown_tpl($dropArray);
		// Botones
		$btn_save       = form_button(array('class'=>"btn btn-primary",'name' => 'save','onclick'=>'insert()' , 'content' => $this->lang_item("btn_guardar") ));
		$btn_reset      = form_button(array('class'=>"btn btn-primary",'name' => 'reset','value' => 'reset','onclick'=>'clean_formulario()','content' => $this->lang_item("btn_limpiar")));
		// Etiquetas
		$tabData['orden_num']   	= $this->lang_item("orden_num",false);
		$tabData['razon_social'] 	= $this->lang_item("razon_social",false);
        $tabData['list_proveedores']= $proveedores;
        $tabData['descripcion']     = $this->lang_item("descripcion",false);
        $tabData['fecha_registro']  = $this->lang_item("fecha_registro",false);
        $tabData['timestamp']       = date('Y-m-d H:i');
        $tabData['registro_por']   	= $this->lang_item("registro_por",false);
        $tabData['usuario_registro']= $this->session->userdata('name');
        $tabData['button_save']     = $btn_save;
        $tabData['button_reset']    = $btn_reset;
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
			$sqlData = array(
						 'id_compras_orden'	=> $this->ajax_post('id_compras_orden')
						,'orden_num' 		=> $this->ajax_post('orden_num')
						,'id_proveedor' 	=> $this->ajax_post('id_proveedor')
						,'descripcion'		=> $this->ajax_post('descripcion')
						,'id_usuario' 		=> $this->session->userdata('id_usuario')
						,'timestamp'  		=> $this->timestamp()
						);

			$insert = $this->db_model->insert($sqlData);
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
			$sqlData = array(
						 'id_compras_orden'	=> $this->ajax_post('id_compras_orden')
						,'orden_num' 		=> $this->ajax_post('orden_num')
						,'id_proveedor' 	=> $this->ajax_post('id_proveedor')
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