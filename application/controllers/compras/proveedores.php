<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class proveedores extends Base_Controller { 

	private $modulo;
	private $submodulo;
	private $view_content;
	private $path;
	private $icon;
	private $offset, $limit_max;
	private $tab = array(), $tab_indice = array();


	public function __construct(){
		parent::__construct();
		$this->modulo 			= 'compras';
		$this->submodulo		= 'proveedores';
		$this->icon 			= 'fa fa-users'; #Icono de modulo
		$this->path 			= $this->modulo.'/'.$this->submodulo.'/';
		$this->view_content 	= 'content';
		$this->limit_max		= 5;
		$this->offset			= 0;

		$this->tab_indice 		= array(
									 'agregar'
									,'listado'
									,'detalle'
								);
		for($i=0; $i<=count($this->tab_indice)-1; $i++){
			$this->tab[$this->tab_indice[$i]] = $this->tab_indice [$i];
		}

		$this->load->model($this->modulo.'/'.$this->submodulo.'_model','db_model');
		$this->lang->load($this->modulo.'/'.$this->submodulo,"es_ES");
	}

	public function config_tabs(){
		for($i=1; $i<=count($this->tab); $i++){
			${'tab_'.$i} = $this->tab [$this->tab_indice[$i-1]];
		}
		$path  	= $this->path;
		$pagina =(is_numeric($this->uri_segment_end()) ? $this->uri_segment_end() : "");
		
		$config_tab['names']    = array(
										 $this->lang_item($tab_1)
										,$this->lang_item($tab_2)
										,$this->lang_item($tab_3)
								); 
		$config_tab['links']    = array(
										 $path.$tab_1
										,$path.$tab_2.'/'.$pagina
										,$tab_3
								); 
		$config_tab['action']   = array(
										 array('function' => '23'  )
										,'load_content'
										,''
								);
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
		$data['titulo_seccion']   = $this->lang_item("titulo_seccion");
		$data['titulo_submodulo'] = $this->lang_item("titulo_submodulo");
		$data['icon']             = $this->icon;
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);	
		$js['js'][]               = array('name' => $this->submodulo, 'dirname' => $this->modulo);
		$this->load_view($this->uri_view_principal(), $data, $js);
	}

	public function listado($offset=0){
		$seccion 		= '';
		$accion 		= $this->tab['listado'];
		$tab_detalle	= $this->tab['detalle'];
		$limit 			= $this->limit_max;
		$uri_view 		= $this->modulo.'/'.$accion;
		$url_link 		= $this->path.$seccion.$accion;		
		$sqlData = array(
			 'buscar'      	=> ($this->ajax_post('filtro')) ? $this->ajax_post('filtro') : ""
			,'offset' 		=> $offset
			,'limit'      	=> $limit
			,'aplicar_limit'=> true
		);
		$uri_segment  = $this->uri_segment(); 
		$total_rows	  = $this->db_model->db_get_total_rows();
		$list_content = $this->db_model->db_get_data($sqlData);
		$url          = base_url($url_link);
		$paginador    = $this->pagination_bootstrap->paginator_generate($total_rows, $url, $limit, $uri_segment, array('evento_link' => 'onclick', 'function_js' => 'load_content', 'params_js'=>'1'));

		if($total_rows>0){
			foreach ($list_content as $value) {
				// Evento de enlace
				$atrr = array(
								'href' => '#',
							  	'onclick' => $tab_detalle.'('.$value['id_compras_proveedor'].')'
						);
				// Datos para tabla
				$tbl_data[] = array('id'                => $value['razon_social'],
									'razon_social'      => tool_tips_tpl($value['razon_social'], $this->lang_item("tool_tip"), 'right' , $atrr),
									'nombre_comercial'  => $value['nombre_comercial'],
									'clave_corta'       => $value['clave_corta']
									);
			}

			$tbl_plantilla = array ('table_open'  => '<table class="table table-bordered responsive ">');
			
			$this->table->set_heading(	$this->lang_item("id"),
										$this->lang_item("lbl_rsocial"),
										$this->lang_item("lbl_nombre"),
										$this->lang_item("lbl_clv"));
			
			$this->table->set_template($tbl_plantilla);
			$tabla = $this->table->generate($tbl_data);
		}else{
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


	/*

	public function agregar_proveedor(){
		$uri_view       = $this->uri_modulo.$this->uri_submodulo.'/proveedores_save';
		$lts_entidades  = dropdown_tpl(		$this->proveedores_model->get_entidades('','','',false), 
										'' ,
										'id_administracion_entidad', 
										array('clave_corta','entidad'),
										'lts_entidades', 
										'requerido'
									);
		$btn_save   = form_button(array('class'   => 'btn btn-primary',
										'name'    => 'save_proveedor',
										'onclick' => "send_form_ajax('".$this->uri_cl_principal('insert_proveedor')."','mensajes');", 
										'content' => $this->lang_item("btn_guardar")));

		$btn_reset  = form_button(array('class'   => 'btn btn-primary',
										'name'    => 'reset',
										'value'   => 'reset',
										'onclick' => 'clean_formulario()',
										'content' => $this->lang_item("btn_limpiar")));

		$lbl['lbl_rsocial']    = $this->lang_item('lbl_rsocial');
		$lbl['lbl_nombre']     = $this->lang_item('lbl_nombre');
		$lbl['lbl_clv']        = $this->lang_item('lbl_clv');
		$lbl['lbl_rfc']        = $this->lang_item('lbl_rfc');
		$lbl['lbl_calle']      = $this->lang_item('lbl_calle');
		$lbl['lbl_num_int']    = $this->lang_item('lbl_num_int');
		$lbl['lbl_num_ext']    = $this->lang_item('lbl_num_ext');
		$lbl['lbl_colonia']    = $this->lang_item('lbl_colonia');
		$lbl['lbl_municipio']  = $this->lang_item('lbl_municipio');
		$lbl['lbl_entidad']    = $this->lang_item('lbl_entidad');
		$lbl['lbl_cp']         = $this->lang_item('lbl_cp');
		$lbl['lbl_telefono']   = $this->lang_item('lbl_telefono');
		$lbl['lbl_email']      = $this->lang_item('lbl_email');
		$lbl['lbl_contacto']   = $this->lang_item('lbl_contacto');
		$lbl['lbl_comentario'] = $this->lang_item('lbl_comentario');
		$lbl['dropdown_entidad'] = $lts_entidades;
		$lbl['button_save']    = $btn_save;
		$lbl['button_reset']   = $btn_reset;

		if($this->ajax_post(false)){
				echo json_encode($this->load_view_unique($uri_view , $lbl, true));
		}else{
			return $this->load_view_unique($uri_view , $lbl, true);
		}
	}

	public function insert_proveedor(){
		$jsonData = array(	'result'  => $this->ajax_post('comentario'),
							'functions' => array( 'clean_formulario' => '' ));
		echo json_encode($jsonData);
	}*/
}