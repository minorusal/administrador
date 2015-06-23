<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class historial_ordenes extends Base_Controller {
		/**
	* Nombre:		Historial Ordenes
	* Ubicación:	Compras>Ordenes/historial ordenes
	* Descripción:	Funcionamiento para la sección de ordenes de compra
	* @author:		Alejandro Enciso
	* Creación: 	2015-05-19
	*/
	private $modulo;
	private $submodulo;
	private $seccion;
	private $view_content, $uri_view_principal;
	private $path;
	private $icon;
	private $offset, $limit_max;
	private $tab_inicial, $tab = array(), $tab_indice = array();
	
	public function __construct(){
		parent::__construct();
		$this->modulo 				= 'compras';
		$this->seccion 				= 'ordenes';
		$this->submodulo			= 'historial_ordenes';
		$this->icon 				= 'fa fa-file-text'; #Icono de modulo
		$this->path 				= $this->modulo.'/'.$this->seccion.'/'.$this->submodulo.'/';
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
		$this->load->model($this->modulo.'/ordenes_model','ordenes_model');
		// Diccionario
		$this->lang->load($this->modulo.'/'.$this->seccion,"es_ES");
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
										 $this->modulo.'/'.$this->submodulo.'/'.$tab_1
										,$this->modulo.'/'.$this->submodulo.'/'.$tab_2.$pagina
										,$tab_3
										,$tab_4
								); 
		// Accion de tabs
		$config_tab['action']   = array(
										 'load_content'
										,''
										,''
										,''
								);
		// Atributos 
		$config_tab['attr']     = array(array('style' => 'display:none'),array('style' => 'display:none'), array('style' => 'display:none'),array('style' => 'display:none'));
		return $config_tab;
	}
	public function index(){		
		// Carga de pagina inicial
		$tabl_inicial 			  = $this->tab_inicial;
		$view_listado    		  = $this->listado();		
		$contenidos_tab           = $view_listado;
		$data['titulo_seccion']   = $this->lang_item("historial_ordenes");
		$data['titulo_submodulo'] = $this->lang_item("titulo_submodulo");
		$data['icon']             = $this->icon;
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);	
		$js['js'][]  = array('name' => $this->submodulo, 'dirname' => $this->modulo);
		$this->load_view($this->uri_view_principal, $data, $js);
	}
	public function listado($offset=0){
		// Crea tabla con listado de ordenes aprobadas 
		$accion 		= $this->tab['listado'];
		$tab_detalle	= $this->tab['detalle'];
		$limit 			= $this->limit_max;
		$uri_view 		= $this->modulo.'/'.$accion;
		$url_link 		= $this->modulo.'/'.$this->submodulo.'/'.$accion;
		$buttonTPL 		= '';

		$filtro  = ($this->ajax_post('filtro')) ? $this->ajax_post('filtro') : "";
		$sqlData = array(
			 'buscar' => $filtro
			,'offset' => $offset
			,'limit'  => $limit
		);
		$uri_segment  			  = $this->uri_segment(); 
		$total_rows   			  = count($this->ordenes_model->db_get_data_historial($sqlData));
		$sqlData['aplicar_limit'] = false;
		$list_content 			  = $this->ordenes_model->db_get_data_historial($sqlData);
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
				$btn_acciones['agregar'] 		= '<span id="ico-articulos_'.$accion_id.'" class="ico_detalle fa fa-search-plus" onclick="articulos('.$accion_id.')" title="'.$this->lang_item("agregar_articulos").'"></span>';
				$acciones = implode('&nbsp;&nbsp;&nbsp;',$btn_acciones);
				// Datos para tabla
				$tbl_data[] = array('id'             => $value['id_compras_orden'],
									'orden_num'      => $value['orden_num'],
									'descripcion'    => $value['descripcion'],
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
							'href'       => base_url($this->modulo.'/'.$this->submodulo).'/export_xlsx?filtro='.base64_encode($filtro)
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
	public function export_xlsx(){
		$filtro      = ($this->ajax_get('filtro')) ?  base64_decode($this->ajax_get('filtro') ): "";
		$sqlData = array('buscar' => $filtro);
		$list_content = $this->ordenes_model->db_get_data_historial($sqlData);		

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

		$params = array(	'title'   => $this->lang_item("historial_ordenes"),
							'items'   => $set_data,
							'headers' => $set_heading
						);
		$this->excel->generate_xlsx($params);
	}
}