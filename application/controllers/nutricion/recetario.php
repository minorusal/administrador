<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class recetario extends Base_Controller{
	private $modulo;
	private $submodulo;
	private $seccion;
	private $view_content;
	private $path;
	private $icon;
	private $view_modal;
	private $offset, $limit_max;
	private $tab, $tab1, $tab2, $tab3;

	public function __construct(){
		parent::__construct();
		$this->modulo 			= 'nutricion';
		$this->seccion		    = 'recetario';
		$this->icon 			= 'fa fa-pencil-square'; 
		$this->path 			= $this->modulo.'/'.$this->seccion.'/'; 
		$this->view_content 	= 'content';
		$this->view_modal       = 'modal_cropper';
		$this->limit_max		= 10;
		$this->offset			= 0;
		// Tabs
		$this->tab1 			= 'agregar';
		$this->tab2 			= 'listado';
		$this->tab3 			= 'detalle';
		// DB Model
		$this->load->model($this->modulo.'/'.$this->seccion.'_model','db_model');
		$this->load->model($this->modulo.'/familias_model','familias');
		$this->load->model('compras/catalogos_model','compras');
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
										 $path.$tab_1             #administracion/impuestos/agregar
										,$path.$tab_2.'/'.$pagina #administracion/impuestos/listado
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
		return $this->modulo.'/'.$this->view_content;
	}
	public function index(){
		$tabl_inicial 			  = 2;
		$view_listado    		  = $this->listado();
		$contenidos_tab           = $view_listado;
		$data['titulo_seccion']   = $this->lang_item($this->seccion);
		$data['titulo_submodulo'] = $this->lang_item("titulo_submodulo");
		$data['icon']             = $this->icon;
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);	
		$js['js'][]               = array('name' => $this->seccion, 'dirname' => $this->modulo);
		$this->load_view($this->uri_view_principal(), $data, $js);
	}
	
	public function listado($offset=0){
		// Crea tabla con listado de elementos capturados 
		$seccion 		= '/listado';
		$tab_detalle	= $this->tab2;	
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
		$total_rows	  = count($this->db_model->get_data($sqlData));
		
		$sqlData['aplicar_limit'] = true;
		
		$list_content = $this->db_model->get_data($sqlData);
		$url          = base_url($url_link);
		$arreglo      = array($total_rows, $url, $limit, $uri_segment);
		$paginador    = $this->pagination_bootstrap->paginator_generate($total_rows, $url, $limit, $uri_segment, array('evento_link' => 'onclick', 'function_js' => 'load_content', 'params_js'=>'1'));
		
		if($total_rows){
			foreach ($list_content as $value){
				// Evento de enlace
				$atrr = array(
								'href' => '#',
							  	'onclick' => $tab_detalle.'('.$value['id_nutricion_receta'].')'
						);
				// Datos para tabla
				$tbl_data[] = array('id'           => $value['id_nutricion_receta'],
									'receta'       => tool_tips_tpl($value['receta'], $this->lang_item("tool_tip"), 'right' , $atrr),
									'clave_corta'  => $value['clave_corta'],
									'porciones'    => $value['porciones'],
									'familia'      => $value['familia'],
									'preparacion'  => $value['preparacion']
									
									);
			}
			// Plantilla
			$tbl_plantilla = set_table_tpl();
			// Titulos de tabla
			$this->table->set_heading(	$this->lang_item("ID"),
										$this->lang_item("lbl_receta"),
										$this->lang_item("lbl_clave_corta"),
										$this->lang_item("lbl_porciones"),
										$this->lang_item("lbl_familia"),
										$this->lang_item("lbl_preparacion")
										);
			// Generar tabla
			$this->table->set_template($tbl_plantilla);
			$tabla = $this->table->generate($tbl_data);
			$buttonTPL = array( 'text'   => $this->lang_item("btn_xlsx"), 
								'iconsweets' => 'iconsweets-excel',
								'href'       => base_url($this->path.'export_xlsx?filtro='.base64_encode($filtro))
								);
		}else{
			$buttonTPL = "";
			$msg   = $this->lang_item("msg_query_null");
			$tabla = alertas_tpl('', $msg ,false);
		}
		$tabData['filtro']    = (isset($filtro) && $filtro!="") ? sprintf($this->lang_item("msg_query_search",false),$total_rows , $filtro) : "";
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
		$seccion = $this->modulo.'/'.$this->seccion.'/'.$this->seccion.'_agregar';

		$familias = array(
						 'data'		=> $this->familias->db_get_data(array())
						,'value' 	=> 'id_nutricion_familia'
						,'text' 	=> array('clave_corta','familia')
						,'name' 	=> "lts_familias_insert"
						,'class' 	=> "requerido"
					);

		$list_familias  = dropdown_tpl($familias);

		

		$insumos  = array(
						 'data'		=> $insumos  = $this->db_model->get_insumos()
						,'value' 	=> 'id_compras_articulo'
						,'text' 	=> array('clave_corta','articulo')
						,'name' 	=> "lts_insumos_insert"
						,'class' 	=> "requerido chosen-rtl"
					);

		$list_insumos  = multi_dropdown_tpl($insumos);

		$btn_save = form_button(array('class'=>'btn btn-primary', 'name'=>'save_receta', 'onclick'=>'agregar()','content'=>$this->lang_item("btn_guardar")));
		$btn_reset = form_button(array('class'=>'btn btn_primary', 'name'=>'reset','onclick'=>'clean_formulario()','content'=>$this->lang_item('btn_limpiar')));

		
		$tab_1['lbl_receta']               = $this->lang_item('lbl_receta');
		$tab_1['lbl_clave_corta']          = $this->lang_item('lbl_clave_corta');
		$tab_1['lbl_porciones']            = $this->lang_item('lbl_porciones');
		$tab_1['lbl_preparacion']          = $this->lang_item('lbl_preparacion');
		$tab_1['lbl_familia']              = $this->lang_item('lbl_familia');
		$tab_1['lbl_asignar_insumos']      = $this->lang_item('lbl_asignar_insumos');
		$tab_1['select_insumos']            = $this->lang_item('select_insumos');
		
		$tab_1['multiselect_insumos']      = $list_insumos;
		$tab_1['select_familias']          = $list_familias;
		$tab_1['button_save']              = $btn_save;
		$tab_1['button_reset']             = $btn_reset;
		if($this->ajax_post(false)){
			echo json_encode($this->load_view_unique($seccion,$tab_1 ,true));
		}
		else{
			return $this->load_view_unique($seccion, $tab_1, true);
		}
	}

	

	public function upload_photo(){
      	$src =  $this->ajax_post('avatar_src');
      	$data = $this->ajax_post('avatar_data');
     

      	$response = $this->jcrop->initialize_crop($src,$tab_1,$file);

       echo json_encode($response);
    }

}

