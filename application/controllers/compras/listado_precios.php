<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class listado_precios extends Base_Controller { 
	private $modulo;
	private $submodulo;
	private $seccion;
	private $view_content;
	private $path;
	private $icon;


	private $offset, $limit_max;
	private $tab1, $tab2, $tab3;

	public function __construct(){
		parent::__construct();
		$this->modulo 			= 'compras';
		$this->seccion          = 'listado_precios';
		$this->icon 			= 'fa fa-list-alt'; #Icono de modulo
		$this->path 			= $this->modulo.'/'.$this->seccion.'/'; #compras/listado_precios/
		$this->view_content 	= 'content';
		$this->limit_max		= 10;
		$this->offset			= 0;
		// Tabs
		$this->tab1 			= 'agregar';
		$this->tab2 			= 'listado';
		$this->tab3 			= 'detalle';
		// DB Model
		$this->load->model($this->modulo.'/'.$this->seccion.'_model','db_model');
		$this->load->model($this->modulo.'/catalogos_model','catalogos_model');
		$this->load->model($this->modulo.'/proveedores_model','proveedores_model');
		$this->load->model('administracion/impuestos_model','impuestos_model');
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
										 $this->lang_item($tab_1) #agregar
										,$this->lang_item($tab_2) #listado
										,$this->lang_item($tab_3) #detalle
								); 
		// Href de tabs
		$config_tab['links']    = array(
										 $path.$tab_1             #compras/listado_precios/agregar
										,$path.$tab_2.'/'.$pagina #compras/listado_precios/listado/pagina
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
	private function uri_view_principal(){
		return $this->modulo.'/'.$this->view_content; #compras/content
	}
	public function index(){
		$tabl_inicial 			  = 2;
		$view_listado    		  = $this->listado();	
		$contenidos_tab           = $view_listado;
		$data['titulo_seccion']   = $this->lang_item($this->seccion);
		$data['titulo_submodulo'] = $this->lang_item($this->modulo);
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

		$filtro      = ($this->ajax_post('filtro')) ? $this->ajax_post('filtro') : "";
		$sqlData = array(
			 'buscar'      	=> $filtro
			,'offset' 		=> $offset
			,'limit'      	=> $limit
		);
		
		$uri_segment  = $this->uri_segment(); 
		$list_content = $this->db_model->db_get_data($sqlData);
		$sqlData['aplicar_limit'] = false;	
		$total_rows	  = count($this->db_model->db_get_data($sqlData));
		$url          = base_url($url_link);
		$paginador    = $this->pagination_bootstrap->paginator_generate($total_rows, $url, $limit, $uri_segment, array('evento_link' => 'onclick', 'function_js' => 'load_content', 'params_js'=>'1'));
		if($total_rows>0){
			foreach ($list_content as $value) {
				$atrr = array(
								'href' => '#',
							  	'onclick' => 'detalle('.$value['id_compras_articulo_precios'].')'
						);
				$tbl_data[] = array('id'             	 => $value['id_compras_articulo_precios'],
									'articulo'   		 => tool_tips_tpl($value['articulo'], $this->lang_item("tool_tip"), 'right' , $atrr),
									'nombre_comercial'   => $value['nombre_comercial'],	
									'marca'    			 => $value['marca'],	
									'presentacion'    	 => $value['presentacion'],
									'precio_proveedor'   => $value['precio_proveedor']);				
			}
			
			// Plantilla
			$tbl_plantilla = array ('table_open'  => '<table class="table table-bordered responsive ">');
			// Titulos de tabla
			$this->table->set_heading(	$this->lang_item("listado_precios"),
										$this->lang_item("articulo"),
										$this->lang_item("proveedor"),
										$this->lang_item("marca"),
										$this->lang_item("presentacion"),
										$this->lang_item("precio_proveedor"));
			// Generar tabla
			$this->table->set_template($tbl_plantilla);
			$tabla = $this->table->generate($tbl_data);

			$buttonTPL = array( 'text'   => $this->lang_item("btn_xlsx"), 
								'iconsweets' => 'iconsweets-excel',
								'href'       => base_url($this->path.'export_xlsx?filtro='.base64_encode($filtro))
								);
		}
		else{
			$buttonTPL = "";
			$msg   = $this->lang_item("msg_query_null");
			$tabla = alertas_tpl('', $msg ,false);
		}
			$tabData['filtro']    = (isset($filtro) && $filtro!="") ? sprintf($this->lang_item("msg_query_search"),$total_rows , $filtro) : "";
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
	public function agregar(){
		//LISTAS
		$dropArray = array(
					 'data'		=> $this->catalogos_model->get_articulos($limit="", $offset="",$filtro="", $aplicar_limit = false )
					,'value' 	=> 'id_compras_articulo'
					,'text' 	=> array('clave_corta','articulo')
					,'name' 	=> "lts_articulos"
					,'event'    => array('event'       => 'onchange',
	   						 'function'    => 'load_pre_um',
	   						 'params'      => array('this.value'),
	   						 'params_type' => array(0)
						)
					,'class' 	=> "requerido"
				);
		$lts_articulos  = dropdown_tpl($dropArray);

		$dropArray2 = array(
					 'data'		=> $this->proveedores_model->db_get_data()
					,'value' 	=> 'id_compras_proveedor'
					,'text' 	=> array('clave_corta','nombre_comercial')
					,'name' 	=> "lts_proveedores"
					,'class' 	=> "requerido"
				);
		$lts_proveedores  = dropdown_tpl($dropArray2);

		$dropArray3 = array(
					 'data'		=> $this->catalogos_model->get_marcas($limit="", $offset="", $filtro="", $aplicar_limit = false)
					,'value' 	=> 'id_compras_marca'
					,'text' 	=> array('clave_corta','marca')
					,'name' 	=> "lts_marcas"
					,'class' 	=> "requerido"
				);
		$lts_marcas  = dropdown_tpl($dropArray3);

		$dropArray4 = array(
					 'data'		=> $this->catalogos_model->get_presentaciones($limit="", $offset="", $filtro="", $aplicar_limit = false)
					,'value' 	=> 'id_compras_presentacion'
					,'text' 	=> array('clave_corta','presentacion')
					,'name' 	=> "lts_presentaciones"
					,'event'    => array('event'       => 'onchange',
	   						 'function'    => 'load_pre_emb',
	   						 'params'      => array('this.value'),
	   						 'params_type' => array(0)
						)
					,'class' 	=> "requerido"
				);
		$lts_presentaciones  = dropdown_tpl($dropArray4); 
		
		$dropArray5 = array(
					 'data'		=> $this->catalogos_model->get_embalaje()
					,'value' 	=> 'id_compras_embalaje'
					,'text' 	=> array('clave_corta','embalaje')
					,'name' 	=> "lts_embalaje"
					,'class' 	=> "requerido"
				);
		$lts_embalaje  = dropdown_tpl($dropArray5);

		$dropArray6 = array(
					 'data'		=> $this->impuestos_model->db_get_data()
					,'value' 	=> 'id_administracion_impuestos'
					,'text' 	=> array('clave_corta','valor')
					,'name' 	=> "lts_impuesto"
					,'class' 	=> ""
				);		

		$lts_impuesto  = dropdown_tpl($dropArray6);

		$seccion       = $this->modulo.'/'.$this->seccion.'/listado_precios_save';
		$btn_save      = form_button(array('class'=>"btn btn-primary",'name' => 'save_pasillo','onclick'=>'agregar()' , 'content' => $this->lang_item("btn_guardar") ));
		$btn_reset     = form_button(array('class'=>"btn btn-primary",'name' => 'reset','value' => 'reset','onclick'=>'clean_formulario()','content' => $this->lang_item("btn_limpiar")));

		$tab_1["cantidad_presentacion_embalaje"] = $this->lang_item("cantidad_presentacion_embalaje");
		$tab_1["cantidad_um_presentacion"]       = $this->lang_item("cantidad_um_presentacion");
		$tab_1["precio_proveedor"]               = $this->lang_item("precio_proveedor");
		$tab_1["impuesto_aplica"]              	 = $this->lang_item("impuesto_aplica");
		$tab_1["impuesto_porcentaje"]            = $this->lang_item("impuesto_porcentaje");
		$tab_1["articulo"]      				 = $this->lang_item("articulo");
		$tab_1["proveedores"]                    = $this->lang_item("proveedores");
		$tab_1["marcas"]                         = $this->lang_item("marcas");
		$tab_1["presentaciones"]                 = $this->lang_item("presentaciones");
		$tab_1["embajale"]                       = $this->lang_item("embajale");

		$tab_1['lts_articulos']      = $lts_articulos;
		$tab_1['lts_proveedores']    = $lts_proveedores;
		$tab_1['lts_marcas']         = $lts_marcas;
		$tab_1['lts_presentaciones'] = $lts_presentaciones;
		$tab_1['lts_embalaje'] 	  	 = $lts_embalaje;
		$tab_1['lts_impuesto'] 	  	 = $lts_impuesto;


        $tab_1['button_save']       = $btn_save;
        $tab_1['button_reset']      = $btn_reset;

        if($this->ajax_post(false)){
				echo json_encode($this->load_view_unique($seccion , $tab_1, true));
		}else{
			return $this->load_view_unique($seccion , $tab_1, true);
		}
	}
	public function insert(){
		$incomplete  = $this->ajax_post('incomplete');
		if($incomplete>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			echo json_encode('0|'.alertas_tpl('error', $msg ,false));
		}
		else{			
	        $cant_presentacion_embalaje = $this->ajax_post('cant_presentacion_embalaje');
	        $cant_um_presentacion 		= $this->ajax_post('cant_um_presentacion');
	        $precio_proveedor 			= $this->ajax_post('precio_proveedor');
	        $impuesto_aplica 			= $this->ajax_post('impuesto_aplica');
	        $impuesto_porcentaje 		= $this->ajax_post('impuesto_porcentaje');
	        $id_articulo 				= $this->ajax_post('id_articulo');
	        $id_proveedor 				= $this->ajax_post('id_proveedor');
	        $id_marca 					= $this->ajax_post('id_marca');
	        $id_presentacion 			= $this->ajax_post('id_presentacion');
	        $id_embalaje 				= $this->ajax_post('id_embalaje');

	        $data_insert  = array(
								'id_articulo'  					=> $id_articulo,
								'id_proveedor'  				=> $id_proveedor,
								'id_marca'  					=> $id_marca,
								'id_presentacion'  				=> $id_presentacion,
								'id_embalaje'  					=> $id_embalaje,
								'cantidad_presentacion_embalaje'=> $cant_presentacion_embalaje,
								'cantidad_um_presentacion'  	=> $cant_um_presentacion,
								'precio_proveedor'  			=> $precio_proveedor,
								'impuesto_aplica'  				=> $impuesto_aplica,
								'impuesto_porcentaje'  			=> $impuesto_porcentaje,
								'timestamp'            			=> $this->timestamp(),
								'id_usuario'           			=> $this->session->userdata('id_usuario')
							);
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
	public function detalle(){
		$seccion       = $this->modulo.'/'.$this->seccion.'/listado_precios_edit';
		$id_compras_articulo_precio    = $this->ajax_post('id_compras_articulo_precio');
		$detalle  		= $this->db_model->get_data_unico($id_compras_articulo_precio);
		$btn_save       = form_button(array('class'=>"btn btn-primary",'name' => 'update' , 'onclick'=>'update()','content' => $this->lang_item("btn_guardar") ));
		//se agrega para mostrar la opcion de proveedor y No. prefactura, solo si se selcciono proveedor en tipo de orden
		if($detalle[0]['impuesto_aplica']==1){
			$style='';
			$class ='requerido';
			$checked='checked';
		}else{
			$style='style="display:none"';
			$class ='';
			$checked='';
		}
		
       	$dropArray = array(
					 'data'		=> $this->catalogos_model->get_articulos($limit="", $offset="",$filtro="", $aplicar_limit = false )
					 ,'selected' => $detalle[0]['id_articulo']
					,'value' 	=> 'id_compras_articulo'
					,'text' 	=> array('clave_corta','articulo')
					,'name' 	=> "lts_articulos"
					,'class' 	=> "requerido"
				);
		$lts_articulos  = dropdown_tpl($dropArray);

		$dropArray2 = array(
					 'data'		=> $this->proveedores_model->db_get_data()
					 ,'selected' => $detalle[0]['id_proveedor']
					,'value' 	=> 'id_compras_proveedor'
					,'text' 	=> array('clave_corta','nombre_comercial')
					,'name' 	=> "lts_proveedores"
					,'class' 	=> "requerido"
				);
		$lts_proveedores  = dropdown_tpl($dropArray2);

		$dropArray3 = array(
					 'data'		=> $this->catalogos_model->get_marcas($limit="", $offset="", $filtro="", $aplicar_limit = false)
					 ,'selected' => $detalle[0]['id_marca']
					,'value' 	=> 'id_compras_marca'
					,'text' 	=> array('clave_corta','marca')
					,'name' 	=> "lts_marcas"
					,'class' 	=> "requerido"
				);
		$lts_marcas  = dropdown_tpl($dropArray3);

		$dropArray4 = array(
					 'data'		=> $this->catalogos_model->get_presentaciones($limit="", $offset="", $filtro="", $aplicar_limit = false)
					 ,'selected'=> $detalle[0]['id_presentacion']
					,'value' 	=> 'id_compras_presentacion'
					,'text' 	=> array('clave_corta','presentacion')
					,'name' 	=> "lts_presentaciones"
					,'class' 	=> "requerido"
				);
		$lts_presentaciones  = dropdown_tpl($dropArray4); 
		
		$dropArray5 = array(
					 'data'		=> $this->catalogos_model->get_embalaje()
					 ,'selected' => $detalle[0]['id_embalaje']
					,'value' 	=> 'id_compras_embalaje'
					,'text' 	=> array('clave_corta','embalaje')
					,'name' 	=> "lts_embalaje"
					,'class' 	=> "requerido"
				);
		$lts_embalaje  = dropdown_tpl($dropArray5);
		$dropArray6 = array(
					 'data'		=> $this->impuestos_model->db_get_data()
					 ,'selected' => $detalle[0]['impuesto_porcentaje']
					,'value' 	=> 'id_administracion_impuestos'
					,'text' 	=> array('clave_corta','valor')
					,'name' 	=> "lts_impuesto"
					,'class' 	=> $class
				);
		$lts_impuesto  = dropdown_tpl($dropArray6);

		$data_tab["cantidad_presentacion_embalaje"] 	= $this->lang_item("cantidad_presentacion_embalaje");
		$data_tab["cantidad_um_presentacion"]      	 	= $this->lang_item("cantidad_um_presentacion");
		$data_tab["precio_proveedor"]              	 	= $this->lang_item("precio_proveedor");
		$data_tab["impuesto_aplica"]              		= $this->lang_item("impuesto_aplica");
		$data_tab["impuesto_porcentaje"]           	 	= $this->lang_item("impuesto_porcentaje");
		$data_tab["articulo"]      				 		= $this->lang_item("articulo");
		$data_tab["proveedores"]                    	= $this->lang_item("proveedores");
		$data_tab["marcas"]                         	= $this->lang_item("marcas");
		$data_tab["presentaciones"]                 	= $this->lang_item("presentaciones");
		$data_tab["embajale"]                       	= $this->lang_item("embajale");
		$data_tab['lbl_fecha_registro']      			= $this->lang_item('lbl_fecha_registro');
		$data_tab['registro_por']    					= $this->lang_item('lbl_usuario_registro');
		$data_tab["lbl_ultima_modificacion"] 			= $this->lang_item('lbl_ultima_modificacion', false);

		$data_tab['id_compras_articulo_precios'] 		= $id_compras_articulo_precio;      
        $data_tab['lts_articulos']        		 		= $lts_articulos;
		$data_tab['lts_proveedores']           	 		= $lts_proveedores;
        $data_tab['lts_marcas']           	 			= $lts_marcas;
        $data_tab['lts_presentaciones']             	= $lts_presentaciones;
        $data_tab['lts_embalaje']             	 		= $lts_embalaje;
        $data_tab['lts_impuesto'] 	  				    = $lts_impuesto;
        $data_tab['val_impuesto_porcentaje']            = $lts_impuesto;
        $data_tab['val_cantidad_presentacion_embalaje']	= $detalle[0]['cantidad_presentacion_embalaje'];
        $data_tab['val_cantidad_um_presentacion']       = $detalle[0]['cantidad_um_presentacion'];
        $data_tab['val_precio_proveedor']             	= $detalle[0]['precio_proveedor'];
        $data_tab['val_impuesto_aplica']             	= $detalle[0]['impuesto_aplica'];
        $data_tab['timestamp']             	 			= $detalle[0]['timestamp'];
        $data_tab['style'] 								= $style;
        $data_tab['checked'] 							= $checked;
        $data_tab['button_save']           	 			= $btn_save;

       	$presentacion=$this->catalogos_model->get_presentacion_unico($detalle[0]['id_presentacion']);
       	$data_tab['pre_emb']						= $presentacion[0]['clave_corta'];

       	$presentacion_um=$this->db_model->get_articulos_um($detalle[0]['id_articulo']);
       	
       	$data_tab['pre_um']						= $presentacion_um[0]['cv_um'];

        $this->load_database('global_system');
        $this->load->model('users_model');
        	
        $usuario_registro               = $this->users_model->search_user_for_id($detalle[0]['id_usuario']);
        $data_tab['usuario_registro']   = text_format_tpl($usuario_registro[0]['name'],"u");

       if($detalle[0]['edit_id_usuario']){
        	$usuario_registro                   = $this->users_model->search_user_for_id($detalle[0]['edit_id_usuario']);
        	$usuario_name 				        = text_format_tpl($usuario_registro[0]['name'],"u");
        	$data_tab['val_ultima_modificacion'] = sprintf($this->lang_item('val_ultima_modificacion', false), $this->timestamp_complete($detalle[0]['edit_timestamp']), $usuario_name);
    	}else{
    		$usuario_name = '';
    		$data_tab['val_ultima_modificacion'] = $this->lang_item('lbl_sin_modificacion', false);
    	}
		echo json_encode( $this->load_view_unique($seccion ,$data_tab, true));
	}
	public function update(){
		$incomplete  = $this->ajax_post('incomplete');
		if($incomplete>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			echo json_encode('0|'.alertas_tpl('error', $msg ,false));
		}else{
			$id_compras_articulo_precios = $this->ajax_post('id_compras_articulo_precios');
			$cant_presentacion_embalaje = $this->ajax_post('cant_presentacion_embalaje');
	        $cant_um_presentacion 		= $this->ajax_post('cant_um_presentacion');
	        $precio_proveedor 			= $this->ajax_post('precio_proveedor');
	        $impuesto_aplica 			= $this->ajax_post('impuesto_aplica');
	        $impuesto_porcentaje 		= $this->ajax_post('impuesto_porcentaje');
	        $id_articulo 				= $this->ajax_post('id_articulo');
	        $id_proveedor 				= $this->ajax_post('id_proveedor');
	        $id_marca 					= $this->ajax_post('id_marca');
	        $id_presentacion 			= $this->ajax_post('id_presentacion');
	        $id_embalaje 				= $this->ajax_post('id_embalaje');
			
			$data_update  = array(
								'id_compras_articulo_precios'  => $id_compras_articulo_precios,
								'id_articulo'  					=> $id_articulo,
								'id_proveedor'  				=> $id_proveedor,
								'id_marca'  					=> $id_marca,
								'id_presentacion'  				=> $id_presentacion,
								'id_embalaje'  					=> $id_embalaje,
								'cantidad_presentacion_embalaje'=> $cant_presentacion_embalaje,
								'cantidad_um_presentacion'  	=> $cant_um_presentacion,
								'precio_proveedor'  			=> $precio_proveedor,
								'impuesto_aplica'  				=> $impuesto_aplica,
								'impuesto_porcentaje'  			=> $impuesto_porcentaje,
								'timestamp'            			=> $this->timestamp(),
								'id_usuario'           			=> $this->session->userdata('id_usuario')
								,'edit_timestamp'  	 			=> $this->timestamp()
								,'edit_id_usuario'   			=> $this->session->userdata('id_usuario')
							);
			$insert = $this->db_model->db_update_data($data_update);

			if($insert){
				$msg = $this->lang_item("msg_update_success",false);
				echo json_encode('1|'.alertas_tpl('success', $msg ,false));
			}else{
				$msg = $this->lang_item("msg_err_clv",false);
				echo json_encode('0|'.alertas_tpl('', $msg ,false));
			}
		}
	}
	public function export_xlsx(){
		$filtro      = ($this->ajax_get('filtro')) ?  base64_decode($this->ajax_get('filtro') ): "";
		$sqlData = array(
			 'buscar'      	=> $filtro
		);
		$list_content = $this->db_model->db_get_data($sqlData);

		if(count($list_content)>0){
			foreach ($list_content as $value) {
				$set_data[] = array(
									$value['articulo'],
									$value['nombre_comercial'],
									$value['marca'],
									$value['presentacion'],
									$value['precio_proveedor']);
			}
			$set_heading = array(
									$this->lang_item("articulo"),
									$this->lang_item("proveedor"),
									$this->lang_item("marca"),
									$this->lang_item("presentacion"),
									$this->lang_item("precio_proveedor"));
	
		}

		$params = array(	'title'   => $this->lang_item("listado"),
							'items'   => $set_data,
							'headers' => $set_heading
						);
		
		$this->excel->generate_xlsx($params);
	}
	public function load_presentacion_em(){
		$id_presentacion = $this->ajax_post('id_presentacion');
		$presentacion=$this->catalogos_model->get_presentacion_unico($id_presentacion);
       	echo json_encode($presentacion[0]['clave_corta']);
	}
	public function load_presentacion_um(){
		$id_articulo = $this->ajax_post('id_articulo');
		$presentacion_em=$this->db_model->get_articulos_um($id_articulo);
     	echo json_encode($presentacion_em[0]['cv_um']);
	}
}
?>