<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class pasillos extends Base_Controller 
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
		$this->modulo 			= 'almacen';
		$this->submodulo		= 'catalogos';
		$this->seccion          = 'pasillos';
		$this->icon 			= 'fa fa-road'; #Icono de modulo
		$this->path 			= $this->modulo.'/'.$this->seccion.'/'; #almacen/pasillos/
		$this->view_content 	= 'content';
		$this->limit_max		= 5;
		$this->offset			= 0;
		// Tabs
		$this->tab1 			= 'agregar_pasillos';
		$this->tab2 			= 'listado_pasillos';
		$this->tab3 			= 'detalle_pasillos';
		// DB Model
		$this->load->model($this->modulo.'/'.$this->submodulo.'_model','db_model');
			// $this->load->model($this->uri_modulo.'articulos_model');
			// $this->load->model($this->uri_modulo.'catalogos_model');
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
		return $this->modulo.'/'.$this->view_content; #almacen/content
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
		$seccion 		= '/listado';
		$tab_detalle	= $this->tab3;	
		$limit 			= $this->limit_max;
		$uri_view 		= $this->modulo.$seccion;
		$url_link 		= $this->path.'listado';	

		$filtro      = ($this->ajax_post('filtro')) ? $this->ajax_post('filtro') : "";

		$sqlData = array(
			 'buscar'      	=> $filtro
			,'offset' 		=> $offset
			,'limit'      	=> $limit
		);
		
		$uri_segment  = $this->uri_segment(); 
		$total_rows	  = count($this->db_model->db_get_data_pasillo($sqlData));
		$sqlData['aplicar_limit'] = false;	
		$list_content = $this->db_model->db_get_data_pasillo($sqlData);
		
		$url          = base_url($url_link);
		$paginador    = $this->pagination_bootstrap->paginator_generate($total_rows, $url, $limit, $uri_segment, array('evento_link' => 'onclick', 'function_js' => 'load_content', 'params_js'=>'1'));
		if($total_rows>0){
			foreach ($list_content as $value) {
				$atrr = array(
								'href' => '#',
							  	'onclick' => 'detalle('.$value['id_almacen_pasillos'].')'
						);
				
				$tbl_data[] = array('id'             => $value['clave_corta'],
									'clave_corta'    => tool_tips_tpl($value['clave_corta'], $this->lang_item("tool_tip"), 'right' , $atrr),
									'pasillos'       => $value['pasillos'],
									'almacen'        => $value['almacenes'],
									'gaveta'        => $value['gavetas'],
									'descripcion'    => $value['descripcion']);	
			}
			
			// Plantilla
			$tbl_plantilla = array ('table_open'  => '<table class="table table-bordered responsive ">');
			// Titulos de tabla
			$this->table->set_heading(	$this->lang_item("cvl_corta"),
										$this->lang_item("cvl_corta"),
										$this->lang_item("pasillo"),
										$this->lang_item("almacen"),
										$this->lang_item("gavetas"),
										$this->lang_item("descripcion"));
			// Generar tabla
			$this->table->set_template($tbl_plantilla);
			$tabla = $this->table->generate($tbl_data);
		}
		else
		{
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

	public function detalle()
	{
		$id_almacen_pasillos = $this->ajax_post('id_pasillo');
		$detalle  		      = $this->db_model->get_orden_unico_pasillo($id_almacen_pasillos);
		
		$seccion      = 'detalle';
		$tab_detalle  = $this->tab3;
		$almacenes    = dropdown_tpl($this->db_model->db_get_data_almacen('','','',false), $detalle[0]['id_almacen_almacenes'], 'id_almacen_almacenes', array('almacenes'),"lts_almacenes", "requerido");
		$gavetas      = dropdown_tpl($this->db_model->db_get_data_gaveta('','','',false), $detalle[0]['id_almacen_gavetas'], 'id_almacen_gavetas', array('gavetas'),"lts_gavetas", "");
		$btn_save     = form_button(array('class'=>"btn btn-primary",'name' => 'actualizar' , 'onclick'=>'actualizar()','content' => $this->lang_item("btn_guardar") ));
                
        $tabData['id_pasillo']            = $id_almacen_pasillos;
        $tabData["nombre_pasillos"]       = $this->lang_item("pasillo");
		$tabData["cvl_corta"]        	  = $this->lang_item("cvl_corta");
		$tabData["descrip"]         	  = $this->lang_item("descripcion");
		$tabData["registro_por"]    	  = $this->lang_item("registro_por");
		$tabData["fecha_registro"]        = $this->lang_item("fecha_registro");
		$tabData["list_almacen"]          = $almacenes;
		$tabData["almacen"]               = $this->lang_item("almacen");
		$tabData["list_gaveta"]           = $gavetas;
		$tabData["gaveta"]                = $this->lang_item("gavetas");
		$tabData["gaveta"]                = $this->lang_item("gaveta");
        $tabData['pasillo']               = $detalle[0]['pasillos'];
		$tabData['clave_corta']           = $detalle[0]['clave_corta'];
        $tabData['descripcion']           = $detalle[0]['descripcion'];
        $tabData['timestamp']             = $detalle[0]['timestamp'];
        $tabData['button_save']           = $btn_save;
        
        $this->load_database('global_system');
        $this->load->model('users_model');
        
        $usuario_registro               = $this->users_model->search_user_for_id($detalle[0]['id_usuario']);
        $tabData['registro_por']    	= $this->lang_item("registro_por",false);
        $tabData['usuario_registro']	= text_format_tpl($usuario_registro[0]['name'],"u");
		$uri_view   					= $this->modulo.'/'.$this->submodulo.'/'.$this->seccion.'/'.$this->seccion.'_'.$seccion;
		echo json_encode( $this->load_view_unique($uri_view ,$tabData, true));
	}

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
						 'id_almacen_pasillos'	    => $this->ajax_post('id_pasillo')
						,'pasillos' 		        => $this->ajax_post('pasillos')
						,'clave_corta' 				=> $this->ajax_post('clave_corta')
						,'descripcion'				=> $this->ajax_post('descripcion')
						,'id_almacen_almacenes'	    => $this->ajax_post('id_almacen')
						,'id_almacen_gavetas'	    => $this->ajax_post('id_gaveta')
						);
			$insert = $this->db_model->db_update_data_pasillo($sqlData);
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