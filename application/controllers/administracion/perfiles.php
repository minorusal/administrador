<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class perfiles extends Base_Controller
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
		$this->submodulo		= 'catalogos';
		$this->seccion          = 'perfiles';
		$this->icon 			= 'iconfa-key'; 
		$this->path 			= $this->modulo.'/'.$this->seccion.'/'; 
		$this->view_content 	= 'content';
		$this->limit_max		= 5;
		$this->offset			= 0;
		// Tabs
		$this->tab1 			= 'agregar';
		$this->tab2 			= 'listado';
		$this->tab3 			= 'detalle';
		// DB Model
		$this->load->model($this->modulo.'/'.$this->seccion.'_model','db_model');
		// Diccionario
		$this->lang->load($this->modulo.'/'.$this->seccion,"es_ES");
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
	private function uri_view_principal()
	{
		return $this->modulo.'/'.$this->view_content;
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
	// Crea tabla con listado de elementos capturados 
		/*$seccion 		= '/listado';
		$tab_detalle	= $this->tab3;	
		$limit 			= $this->limit_max;
		$uri_view 		= $this->modulo.$seccion;
		$url_link 		= $this->path.'listado';
		$filtro      	= ($this->ajax_post('filtro')) ? $this->ajax_post('filtro') : "";
		$sqlData = array(
			 'buscar'      	=> $filtro
			,'offset' 		=> $offset
			,'limit'      	=> $limit
		);
		$uri_segment  = $this->uri_segment(); 
		$total_rows	  = count($this->db_model->db_get_data($sqlData));
		$sqlData['aplicar_limit'] = false;
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
							  	'onclick' => $tab_detalle.'('.$value['id_administracion_areas'].')'
						);
				// Datos para tabla
				$tbl_data[] = array('id'            => $value['id_administracion_areas'],
									'area'      => tool_tips_tpl($value['area'], $this->lang_item("tool_tip"), 'right' , $atrr),
									'clave_corta'   => $value['clave_corta'],
									'descripcion'   => $value['descripcion']
									);
			}
			// Plantilla
			$tbl_plantilla = array('table_open'  => '<table class="table table-bordered responsive ">');
			// Titulos de tabla
			$this->table->set_heading(	$this->lang_item("ID"),
										$this->lang_item("area"),
										$this->lang_item("clave_corta"),
										$this->lang_item("descripcion"));
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
		}*/
	}

	public function agregar()
	{
		$seccion   = $this->modulo.'/'.$this->seccion.'/'.$this->seccion.'_save';
		$btn_save  = form_button(array('class'=>'btn btn-primary', 'name'=>'save_perfil', 'onclick'=>'agregar()','content'=>$this->lang_item("btn_guardar")));
		$btn_reset = form_button(array('class'=>'btn btn_primary', 'name'=>'reset','onclick'=>'clean_formulario()','content'=>$this->lang_item('btn_limpiar')));

		$tab_1['lbl_perfil']       = $this->lang_item("nombre_perfil");
		$tab_1['lbl_descripcion']  = $this->lang_item('descripcion');
		$tab_1['tree_view']   = $this->treeview_perfiles(4);

		$tab_1['button_save']  = $btn_save;
		$tab_1['button_reset'] = $btn_reset;

		if($this->ajax_post(false))
		{
			echo json_encode($this->load_view_unique($seccion,$tab_1,true));
		}
		else
		{
			return $this->load_view_unique($seccion, $tab_1, true);
		}
	}

	public function treeview_perfiles($id_perfil = 2){
		$this->load_database('global_system');
		$this->load->model('users_model');
		$info_perfil  = $this->users_model->search_data_perfil($id_perfil);
		$id_menu_n1   = $info_perfil[0]['id_menu_n1'];
		$id_menu_n2   = $info_perfil[0]['id_menu_n2'];
		$id_menu_n3   = $info_perfil[0]['id_menu_n3'];

		$id_niveles   = array(	
						'id_menu_n1' => explode(',', $info_perfil[0]['id_menu_n1']),
						'id_menu_n2' => explode(',', $info_perfil[0]['id_menu_n2']),
						'id_menu_n3' => explode(',', $info_perfil[0]['id_menu_n3']),
						);
		$data_modulos = $this->users_model->search_modules_for_user('', '' , '' , true);
		$data_modulos = $this->build_array_treeview($data_modulos);
		$controls     = '<div id="sidetreecontrol"><a href="?#">'.$this->lang_item('collapse', false).'</a> | <a href="?#">'.$this->lang_item('expand', false).'</a></div>';
		return $controls.$this->list_tree_view($data_modulos, $id_niveles,false,false);
	}
}