<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class usuarios extends Base_Controller { 

	private $modulo;
	private $submodulo;
	private $view_content;
	private $path;
	private $icon;
	private $offset, $limit_max;
	private $tab = array(), $tab_indice = array();

	public function __construct(){
		parent::__construct();
		$this->modulo 			= 'administracion';
		$this->submodulo		= 'control_de_usuarios';
		$this->seccion          = 'usuarios';
		$this->icon 			= 'fa fa-user'; #Icono de modulo
		$this->path 			= $this->modulo.'/'.$this->seccion.'/';
		$this->view_agregar     = $this->modulo.'/'.$this->seccion.'/'.$this->seccion.'_agregar';
		$this->view_detalle     = $this->modulo.'/'.$this->seccion.'_detalle';
		$this->view_content 	= 'content';
		$this->limit_max		= 10;
		$this->offset			= 0;

		$this->tab_indice 		= array(
									 'agregar'
									,'listado'
									,'detalle'
								);
		for($i=0; $i<=count($this->tab_indice)-1; $i++){
			$this->tab[$this->tab_indice[$i]] = $this->tab_indice[$i];
		}
		$this->load->model('users_model','db_model');
		$this->load->model($this->modulo.'/perfiles_model','perfiles');
		$this->lang->load($this->modulo.'/'.$this->seccion,"es_ES");
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
										 'load_content'
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
		$data['titulo_seccion']   = $this->lang_item("seccion");
		$data['titulo_submodulo'] = $this->lang_item("submodulo");
		$data['icon']             = $this->icon;
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);	
		$js['js'][]               = array('name' => $this->seccion, 'dirname' => $this->modulo);
		$this->load_view($this->uri_view_principal(), $data, $js);
	}
	public function listado($offset = 0){
		/*$seccion 		= '';
		$filtro         = ($this->ajax_post('filtro')) ? $this->ajax_post('filtro') : "";
		$accion 		= $this->tab['listado'];
		$tab_detalle	= $this->tab['detalle'];
		$limit 			= $this->limit_max;
		$uri_view 		= $this->modulo.'/'.$accion;
		$url_link 		= $this->path.$seccion.$accion;		
		$sqlData = array(
						 'buscar'      	=> $filtro
						,'offset' 		=> $offset
						,'limit'      	=>  $limit
						,'aplicar_limit'=> true
					);
		$uri_segment  = $this->uri_segment(); 
		//$total_rows	  = $this->db_model->db_get_total_rows($sqlData);
		$list_content = $this->db_model->db_get_data($sqlData);
		$url          = base_url($url_link);
		$paginador    = $this->pagination_bootstrap->paginator_generate($total_rows, $url, $limit, $uri_segment, array('evento_link' => 'onclick', 'function_js' => 'load_content', 'params_js'=>'1'));
		/*
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
									'rfc'               => $value['rfc'],
									'clave_corta'       => $value['clave_corta'],
									'entidad'           => $value['entidad']
									);
			}

			$tbl_plantilla = array ('table_open'  => '<table class="table table-bordered responsive ">');
			
			$this->table->set_heading(	$this->lang_item("id"),
										$this->lang_item("lbl_rsocial"),
										$this->lang_item("lbl_nombre"),
										$this->lang_item("lbl_rfc"),
										$this->lang_item("lbl_clv"),
										$this->lang_item("lbl_entidad")

									);
			$buttonTPL = array( 'text'       => $this->lang_item("btn_xlsx"), 
								'iconsweets' => 'iconsweets-excel',
								'href'       => base_url($this->path.'export_xlsx?filtro='.base64_encode($filtro))
								);

			$this->table->set_template($tbl_plantilla);
			$tabla = $this->table->generate($tbl_data);
		}else{
			$buttonTPL = "";
			$msg       = $this->lang_item("msg_query_null");
			$tabla     = alertas_tpl('', $msg ,false);
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
		}*/
	}
	public function agregar(){
		$seccion 		= '';
		$uri_view   	= $this->view_agregar;
		$btn_save       = form_button(array('class'=>"btn btn-primary",'name' => 'save','onclick'=>'insert()' , 'content' => $this->lang_item("btn_guardar") ));
		$btn_reset      = form_button(array('class'=>"btn btn-primary",'name' => 'reset','value' => 'reset','onclick'=>'clean_formulario()','content' => $this->lang_item("btn_limpiar")));
		
		$dropdown_array      = array(
								 'data'		=> $this->perfiles->db_get_data()
								,'value' 	=> 'id_perfil'
								,'text' 	=> array('perfil')
								,'name' 	=> "lts_perfiles"
								,'class' 	=> "requerido"
								,'event'    => array('event'       => 'onchange',
							   						 'function'    => 'load_tree_view',
							   						 'params'      => array('this.value'),
							   						 'params_type' => array(0)
			   										)
								);
		$perfiles                    =  dropdown_tpl($dropdown_array);
		$tabData['base_url']         =  base_url();
		$tabData['lbl_nombre']       =  $this->lang_item('lbl_nombre', false);
		$tabData['lbl_paterno']      =  $this->lang_item('lbl_paterno', false);
		$tabData['lbl_materno']      =  $this->lang_item('lbl_materno', false);
		$tabData['lbl_telefono']     =  $this->lang_item('lbl_telefono', false);
		$tabData['lbl_email']        =  $this->lang_item('lbl_email', false);
		$tabData['lbl_perfil']       =  $this->lang_item('lbl_perfil', false);
		$tabData['dropdown_perfil']  =  $perfiles;
		$tabData['button_save']      =  $btn_save;
		$tabData['button_reset']     =  $btn_reset;
		$tabData['tree_view']        =  '';
		
		

        if($this->ajax_post(false)){
				echo json_encode($this->load_view_unique($uri_view , $tabData, true));
		}else{
			return $this->load_view_unique($uri_view , $tabData, true);
		}
	}
	public function load_tree_view_perfil(){
		$id_perfil         = $this->ajax_post('id_perfil');
		$treeview_perfiles = $this->treeview_perfiles($id_perfil, true);
		echo json_encode($treeview_perfiles);
	}

	public function insert()
	{
		$incomplete = $this->ajax_post('incomplete');
		if($incomplete > 0)
		{
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			echo json_encode('0|'.alertas_tpl('error', $msg ,false));
		}
		else
		{
			/*print_debug($this->session->userdata('id_sucursal'));*/
			$sqlData = array(
				 'nombre'      => $this->ajax_post('nombre')
				,'paterno'     => $this->ajax_post('paterno')
				,'materno'     => $this->ajax_post('materno')
				,'id_menu_n1'  => $this->ajax_post('nivel_1')
				,'id_menu_n2'  => $this->ajax_post('nivel_2')
				,'id_menu_n3'  => $this->ajax_post('nivel_3')
				,'id_perfil'   => $this->ajax_post('id_perfil')
				,'id_usuario'  => $this->session->userdata('id_usuario')
				,'id_pais'     => $this->session->userdata('id_pais')
				,'id_sucursal' => $this->session->userdata('id_sucursal')
				,'registro'    => $this->timestamp());
			$insert = $this->db_model->db_insert_data($sqlData);
			
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