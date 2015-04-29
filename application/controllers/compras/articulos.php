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
		$config_tab['links']    = array('', 
										'compras/articulos/listado_articulos/'.$pagina, 
										'detalle_articulo'
										); 
		$config_tab['action']   = array('',
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
		
		$uri_view       = $this->uri_modulo.$this->uri_submodulo.'/agregar_articulo';
		$presentaciones = dropdown_tpl($this->catalogos_model->get_presentaciones('','',false),'id_cat_presentaciones', array('clave_corta', 'presentaciones'),"lts_presentaciones", "requerido");
		$lineas         = dropdown_tpl($this->catalogos_model->get_lineas('','',false),'id_cat_linea', array('clave_corta','linea'),"lts_lineas", "requerido");
		$um             = dropdown_tpl($this->catalogos_model->get_um('','',false),'id_cat_um', array('clave_corta','um'),"lts_um", "requerido");
		$marcas         = dropdown_tpl($this->catalogos_model->get_marcas('','',false),'id_cat_marcas', array('clave_corta','marcas'),"lts_marcas", "requerido");
		$btn_save       = form_button(array('class'=>"btn btn-primary",'name' => 'save_articulo' , 'content' => $this->lang_item("btn_guardar") ));
		$btn_reset      = form_button(array('class'=>"btn btn-primary",'name' => 'reset','value' => 'reset' ,'content' => $this->lang_item("btn_limpiar")));


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
		


		$view_agregar_articulo   =  $this->load_view_unique($uri_view , $data_tab_1, true);
		$view_listado_articulo   =  $this->listado_articulos($offset);
		
		$contenidos_tab           = array($view_agregar_articulo,$view_listado_articulo);

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

		$uri_view    = $this->uri_modulo.$this->uri_submodulo.'/listado_articulos';
		$limit       = 5;
		$uri_segment = $this->uri_segment(); 
		$lts_content = $this->articulos_model->get_articulos($limit, $offset, $filtro);

		$total_rows  = count($this->articulos_model->get_articulos($limit, $offset, $filtro, false));
		$url         = base_url($uri_view);
		$paginador   = $this->pagination_bootstrap->paginator_generate($total_rows, $url, $limit, $uri_segment, array('evento_link' => 'onclick', 'function_js' => 'load_content', 'params_js'=>'1'));

		if($total_rows>0){
			foreach ($lts_content as $value) {
				
				$atrr = array(
								'href' => '#',
							  	'onclick' => 'detalle_articulo('.$value['id_cat_articulos'].')'
						);
				
				$tbl_data[] = array('id'             => $value['id_cat_articulos'],
									'articulos'      => tool_tips_tpl($value['articulos'], $this->lang_item("tool_tip"), 'right' , $atrr),
									'clave_corta'    => $value['clave_corta'],
									'marca'          => $value['marcas'],
									'presentacione'  => $value['presentaciones'],
									'linea'          => $value['linea'],
									'um'             => $value['um'],
									'descripcion'    => $value['descripcion']);
			}

			$tbl_plantilla = array ('table_open'  => '<table class="table table-bordered responsive ">');
		
			$this->table->set_heading(	$this->lang_item("id"),
										$this->lang_item("articulos"),
										$this->lang_item("cvl_corta"),
										$this->lang_item("marcas"),
										$this->lang_item("presentaciones"),
										$this->lang_item("lineas"),
										$this->lang_item("u.m."),
										$this->lang_item("descripcion"));
			$this->table->set_template($tbl_plantilla);
			$tabla = $this->table->generate($tbl_data);

			
		}
			$data_tab_2['tabla']     = $tabla;
			$data_tab_2['paginador'] = $paginador;
			$data_tab_2['item_info'] = $this->pagination_bootstrap->showing_items($limit, $offset, $total_rows);

			if($this->ajax_post(false)){
				echo json_encode( $this->load_view_unique($uri_view , $data_tab_2, true));
			}else{
				return $this->load_view_unique($uri_view , $data_tab_2, true);
			}
			
	}
	public function agregar_articulo(){
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
			$data_insert = array('articulos' => $articulo,
								 'clave_corta'=> $clave_corta, 
								 'descripcion'=> $descripcion,
								 'id_cat_linea'=> $linea,
								 'id_cat_marcas'=> $marca,
								 'id_cat_presentaciones'=> $presentacion,
								 'id_cat_um'=> $um,
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

}