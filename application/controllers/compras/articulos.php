<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class articulos extends Base_Controller { 
	
	var $uri_modulo     = 'compras/';
	var $uri_submodulo  = 'articulos';
	var $view_content   = 'content';
	
	public function __construct(){
		parent::__construct();
		$this->load->model($this->uri_modulo.'articulos_model');
		$this->load->model($this->uri_modulo.'catalogos_model');
		$this->lang->load("compras/articulos","es_ES");
	}
	public function config_tabs(){
		$pagina =(is_numeric($this->uri_segment_end()) ? $this->uri_segment_end() : "");
		$config_tab['names']    = array($this->lang_item("agregar_articulo"), 
										$this->lang_item("listado_articulos"), 
										$this->lang_item("detalle_articulo")
								); 
		$config_tab['links']    = array('compras/articulos/agregar_articulo', 
										'compras/articulos/listado_articulos/'.$pagina, 
										'detalle_articulo'
										); 
		$config_tab['action']   = array('load_content',
										'load_content', 
										''
										);
		$config_tab['attr']     = array('','', array('style' => 'display:none'));

		return $config_tab;
	}

	private function uri_view_principal(){
		return $this->uri_modulo.$this->view_content;
	}
	public function index($offset = 0){
		
		
		$view_listado_articulo    = $this->listado_articulos($offset);
		
		$contenidos_tab           = $view_listado_articulo;

		$data['titulo_seccion']   = $this->lang_item("articulos");
		$data['titulo_submodulo'] = $this->lang_item("titulo_submodulo");
		$data['icon']             = 'fa fa-cubes';
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),2,$contenidos_tab);
		
		$js['js'][]     = array('name' => 'articulos', 'dirname' => 'compras');
		$this->load_view($this->uri_view_principal(), $data, $js);
	}
	public function listado_articulos($offset = 0){

		$data_tab_2  = "";
		$filtro      = ($this->ajax_post('filtro')) ? $this->ajax_post('filtro') : "";
		$uri_view    = $this->uri_modulo.'/listado';
		$limit       = 5;
		$uri_segment = $this->uri_segment(); 
		$lts_content = $this->articulos_model->get_articulos($limit, $offset, $filtro);
		$total_rows  = count($this->articulos_model->get_articulos($limit, $offset, $filtro, false));
		$url         = base_url($this->uri_modulo.$this->uri_submodulo.'/listado_articulos');
		$paginador   = $this->pagination_bootstrap->paginator_generate($total_rows, $url, $limit, $uri_segment, array('evento_link' => 'onclick', 'function_js' => 'load_content', 'params_js'=>'1'));

		if($total_rows>0){
			foreach ($lts_content as $value) {
				$atrr = array(
								'href' => '#',
							  	'onclick' => 'detalle_articulo('.$value['id_compras_articulo'].')'
						);
				
				$tbl_data[] = array('id'             => $value['articulo'],
									'articulos'      => tool_tips_tpl($value['articulo'], $this->lang_item("tool_tip"), 'right' , $atrr),
									'clave_corta'    => $value['clave_corta'],
									'marca'          => $value['marca'],
									'presentacione'  => $value['presentacion'],
									'linea'          => $value['linea'],
									'um'             => $value['um'],
									'descripcion'    => $value['descripcion']);
			}

			$tbl_plantilla = array ('table_open'  => '<table class="table table-bordered responsive ">');
		
			$this->table->set_heading(	$this->lang_item("articulos"),
										$this->lang_item("articulos"),
										$this->lang_item("cvl_corta"),
										$this->lang_item("marca"),
										$this->lang_item("presentacion"),
										$this->lang_item("lineas"),
										$this->lang_item("u.m."),
										$this->lang_item("descripcion"));
			
			$this->table->set_template($tbl_plantilla);
			$tabla = $this->table->generate($tbl_data);
		}else{
			$msg   = $this->lang_item("msg_query_null");
			$tabla = alertas_tpl('', $msg ,false);
		}

		$data_tab_2['filtro']    = ($filtro!="") ? sprintf($this->lang_item("msg_query_search", false),$total_rows , $filtro) : "";
		$data_tab_2['tabla']     = $tabla;
		$data_tab_2['export']    = form_button(array('class'=>"btn btn-primary",'content' => $this->lang_item("btn_guardar") ));;
		$data_tab_2['paginador'] = $paginador;
		$data_tab_2['item_info'] = $this->pagination_bootstrap->showing_items($limit, $offset, $total_rows);


		if($this->ajax_post(false)){
			echo json_encode( $this->load_view_unique($uri_view , $data_tab_2, true));
		}else{
			return $this->load_view_unique($uri_view , $data_tab_2, true);
		}
	}

	public function agregar_articulo(){
		
		$uri_view       = $this->uri_modulo.$this->uri_submodulo.'/articulo_save';
		// listas
		$dropArray = array(
					 'data'		=> $this->catalogos_model->get_presentaciones('','','',false)
					,'value' 	=> 'id_compras_presentacion'
					,'text' 	=> array('clave_corta', 'presentacion')
					,'name' 	=> "lts_presentaciones"
					,'class' 	=>  "requerido"
				);
		$presentaciones = dropdown_tpl($dropArray);
		$dropArray2 = array(
					 'data'		=> $this->catalogos_model->get_lineas('','','',false)
					,'value' 	=> 'id_compras_linea'
					,'text' 	=> array('clave_corta','linea')
					,'name' 	=> "lts_lineas"
					,'class' 	=> "requerido"
				);
		$lineas         = dropdown_tpl($dropArray2);
		$dropArray3 = array(
					 'data'		=> $this->catalogos_model->get_um('','','',false)
					,'value' 	=> 'id_compras_um'
					,'text' 	=> array('clave_corta','um')
					,'name' 	=> "lts_um"
					,'class' 	=> "requerido"
					);
		$um             = dropdown_tpl($dropArray3);
		$dropArray4 = array(
					 'data'		=> $this->catalogos_model->get_marcas('','','',false)
					,'value' 	=> 'id_compras_marca'
					,'text' 	=> array('clave_corta','marca')
					,'name' 	=> "lts_marcas"
					,'class' 	=> "requerido"
					);
		$marcas         = dropdown_tpl($dropArray4);
		// 
		$btn_save       = form_button(array('class'=>"btn btn-primary",'name' => 'save_articulo','onclick'=>'insert_articulo()' , 'content' => $this->lang_item("btn_guardar") ));
		$btn_reset      = form_button(array('class'=>"btn btn-primary",'name' => 'reset','value' => 'reset','onclick'=>'clean_formulario()','content' => $this->lang_item("btn_limpiar")));

        $data_tab_1['nombre_articulo']   = $this->lang_item("nombre_articulo",false);
        $data_tab_1['cvl_corta']         = $this->lang_item("cvl_corta",false);
        $data_tab_1['marca']             = $this->lang_item("marca",false);
        $data_tab_1['presentacion']      = $this->lang_item("presentacion",false);
        $data_tab_1['linea']             = $this->lang_item("linea",false);
        $data_tab_1['um']                = $this->lang_item("um",false);
        $data_tab_1['descripcion']       = $this->lang_item("descripcion",false);
        $data_tab_1['list_marca']        = $marcas;
        $data_tab_1['list_presentacion'] = $presentaciones;
        $data_tab_1['list_linea']        = $lineas;
        $data_tab_1['list_um']           = $um;
        $data_tab_1['button_save']       = $btn_save;
        $data_tab_1['button_reset']      = $btn_reset;

        if($this->ajax_post(false)){
				echo json_encode($this->load_view_unique($uri_view , $data_tab_1, true));
		}else{
			return $this->load_view_unique($uri_view , $data_tab_1, true);
		}
	}
	public function detalle_articulo(){
		$id_articulo       = $this->ajax_post('id_articulo');
		$detalle_articulo  = $this->articulos_model->get_articulo_unico($id_articulo);
		// listas
		$dropArray = array(
					 'data'		=> $this->catalogos_model->get_presentaciones('','','',false)
					,'value' 	=> 'id_compras_presentacion'
					,'text' 	=> array('clave_corta', 'presentacion')
					,'name' 	=> "lts_presentaciones"
					,'class' 	=>  "requerido"
					,'selected' => $detalle_articulo[0]['id_compras_presentacion']
				);
		$presentaciones = dropdown_tpl($dropArray);
		$dropArray2 = array(
					 'data'		=> $this->catalogos_model->get_lineas('','','',false)
					,'value' 	=> 'id_compras_linea'
					,'text' 	=> array('clave_corta','linea')
					,'name' 	=> "lts_lineas"
					,'class' 	=> "requerido"
					,'selected' => $detalle_articulo[0]['id_compras_linea']
				);
		$lineas         = dropdown_tpl($dropArray2);
		$dropArray3 = array(
					 'data'		=> $this->catalogos_model->get_um('','','',false)
					,'value' 	=> 'id_compras_um'
					,'text' 	=> array('clave_corta','um')
					,'name' 	=> "lts_um"
					,'class' 	=> "requerido"
					,'selected' => $detalle_articulo[0]['id_compras_um']
					);
		$um             = dropdown_tpl($dropArray3);
		$dropArray4 = array(
					 'data'		=> $this->catalogos_model->get_marcas('','','',false)
					,'value' 	=> 'id_compras_marca'
					,'text' 	=> array('clave_corta','marca')
					,'name' 	=> "lts_marcas"
					,'class' 	=> "requerido"
					,'selected' => $detalle_articulo[0]['id_compras_marca']
					);
		$marcas         = dropdown_tpl($dropArray4);
		// 
		$btn_save          = form_button(array('class'=>"btn btn-primary",'name' => 'update_articulo' , 'onclick'=>'update_articulo()','content' => $this->lang_item("btn_guardar") ));
		
		$data_tab_3['id_articulo']       = $id_articulo;
		$data_tab_3['nombre_articulo']   = $this->lang_item("nombre_articulo",false);
		$data_tab_3['articulo_value']    = $detalle_articulo[0]['articulo'];
        $data_tab_3['cvl_corta']         = $this->lang_item("cvl_corta",false);
        $data_tab_3['cvl_value']         = $detalle_articulo[0]['clave_corta'];
        $data_tab_3['marca']             = $this->lang_item("marca",false);
        $data_tab_3['presentacion']      = $this->lang_item("presentacion",false);
        $data_tab_3['linea']             = $this->lang_item("linea",false);
        $data_tab_3['um']                = $this->lang_item("um",false);
        $data_tab_3['descripcion']       = $this->lang_item("descripcion",false);
        $data_tab_3['descripcion_value'] = $detalle_articulo[0]['descripcion'];
        $data_tab_3['timestamp']         = $detalle_articulo[0]['timestamp'];
        $data_tab_3['list_marca']        = $marcas;
        $data_tab_3['list_presentacion'] = $presentaciones;
        $data_tab_3['list_linea']        = $lineas;
        $data_tab_3['list_um']           = $um;
        $data_tab_3['button_save']       = $btn_save;
        
        $this->load_database('global_system');
        $this->load->model('users_model');
        
        $usuario_registro               = $this->users_model->search_user_for_id($detalle_articulo[0]['id_usuario']);
        $data_tab_3['usuario_registro'] = text_format_tpl($usuario_registro[0]['name'],"u");
		$uri_view    = $this->uri_modulo.$this->uri_submodulo.'/articulo_edit';

		
		echo json_encode( $this->load_view_unique($uri_view ,$data_tab_3, true));
	}
	public function insert_articulo(){
		$incomplete  = $this->ajax_post('incomplete');
		if($incomplete>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			echo json_encode('0|'.alertas_tpl('error', $msg ,false));
		}else{
			$articulo     = $this->ajax_post('articulo');
			$clave_corta  = $this->ajax_post('clave_corta');
			$presentacion = $this->ajax_post('presentacion');
			$linea        = $this->ajax_post('linea');
			$um           = $this->ajax_post('um');
			$marca        = $this->ajax_post('marca');
			$descripcion  = ($this->ajax_post('descripcion')=='')? $this->lang_item("sin_descripcion") : $this->ajax_post('descripcion');
			$data_insert = array('articulo' => $articulo,
								 'clave_corta'=> $clave_corta, 
								 'descripcion'=> $descripcion,
								 'id_compras_linea'=> $linea,
								 'id_compras_marca'=> $marca,
								 'id_compras_presentacion'=> $presentacion,
								 'id_compras_um'=> $um,
								 'id_usuario' => $this->session->userdata('id_usuario'),
								 'timestamp'  => $this->timestamp());
			$insert = $this->articulos_model->insert_articulo($data_insert);

			if($insert){
				$msg = $this->lang_item("msg_insert_success",false);
				echo json_encode('1|'.alertas_tpl('success', $msg ,false));
			}else{
				$msg = $this->lang_item("msg_err_clv",false);
				echo json_encode('0|'.alertas_tpl('', $msg ,false));
			}
		}
	}
	public function update_articulo(){
		$incomplete  = $this->ajax_post('incomplete');
		if($incomplete>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			echo json_encode('0|'.alertas_tpl('error', $msg ,false));
		}else{
			$id_articulo  = $this->ajax_post('id_articulo');
			$articulo     = $this->ajax_post('articulo');
			$clave_corta  = $this->ajax_post('clave_corta');
			$presentacion = $this->ajax_post('presentacion');
			$linea        = $this->ajax_post('linea');
			$um           = $this->ajax_post('um');
			$marca        = $this->ajax_post('marca');
			$descripcion  = ($this->ajax_post('descripcion')=='')? $this->lang_item("sin_descripcion") : $this->ajax_post('descripcion');
			$data_update  = array('articulo' => $articulo,
								 'clave_corta'=> $clave_corta, 
								 'descripcion'=> $descripcion,
								 'id_compras_linea'=> $linea,
								 'id_compras_marca'=> $marca,
								 'id_compras_presentacion'=> $presentacion,
								 'id_compras_um'=> $um);
			$insert = $this->articulos_model->update_articulo($data_update,$id_articulo);

			if($insert){
				$msg = $this->lang_item("msg_insert_success",false);
				echo json_encode('1|'.alertas_tpl('success', $msg ,false));
			}else{
				$msg = $this->lang_item("msg_err_clv",false);
				echo json_encode('0|'.alertas_tpl('', $msg ,false));
			}
		}
	}
}