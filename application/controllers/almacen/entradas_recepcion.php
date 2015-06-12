<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class entradas_recepcion extends Base_Controller{
	private $modulo;
	private $seccion;
	private $view_content;
	private $path;
	private $icon;

	private $offset, $limit_max;
	private $tab1, $tab2, $tab3;

	public function __construct(){
		parent::__construct();
		$this->modulo 			= 'almacen';
		$this->seccion          = 'entradas_recepcion';
		$this->icon 			= 'fa fa-book'; //Icono de modulo
		$this->path 			= $this->modulo.'/'.$this->seccion.'/'; //almacen/entradas_recepcion/
		$this->view_content 	= 'content';
		$this->limit_max		= 10;
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
	public function config_tabs(){
		$tab_1 	= $this->tab1;
		$tab_2 	= $this->tab2;
		$tab_3 	= $this->tab3;
		$path  	= $this->path;
		$pagina =(is_numeric($this->uri_segment_end()) ? $this->uri_segment_end() : "");
		// Nombre de Tabs
		$config_tab['names']    = array(
										 $this->lang_item($tab_1) //agregar
										,$this->lang_item($tab_2) //listado
										,$this->lang_item($tab_3) //detalle
								); 
		// Href de tabs
		$config_tab['links']    = array(
										 $path.$tab_1            //almacen/entradas_recepcion/agregar
										,$path.$tab_2.'/'.$pagina //almacen/entradas_recepcion/listado/pagina
										,$tab_3                   //detalle
								); 
		// Accion de tabs
		$config_tab['action']   = array(
										 'load_content'
										,'load_content'
										,''
								);
		// Atributos 
		$config_tab['attr']     = array('',array('style' => 'display:none'), array('style' => 'display:none'));
		return $config_tab;
	}
	private function uri_view_principal(){
		return $this->modulo.'/'.$this->view_content; //compras/content
	}
	public function index(){
		$tabl_inicial 			  = 1;
		$view_listado    		  = $this->agregar();	
		//$view_listado    		  = 'tab';
		$contenidos_tab           = $view_listado;
		$data['titulo_submodulo'] = $this->lang_item($this->modulo);
		$data['titulo_seccion']   = $this->lang_item($this->seccion);
		$data['icon']             = $this->icon;
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);	
		
		$js['js'][]  = array('name' => $this->seccion, 'dirname' => $this->modulo);
		$this->load_view($this->uri_view_principal(), $data, $js);
	}
	public function agregar(){
		

		$seccion       = $this->modulo.'/'.$this->seccion.'/entradas_recepcion_save';
		$btn_save      = form_button(array('class'=>"btn btn-primary",'name' => 'save_entreda','onclick'=>'agregar()' , 'content' => $this->lang_item("btn_guardar") ));
		$btn_reset     = form_button(array('class'=>"btn btn-primary",'name' => 'reset','value' => 'reset','onclick'=>'clean_formulario()','content' => $this->lang_item("btn_limpiar")));

		$tab_1["lbl_no_orden_compra"]   = $this->lang_item("no_orden");
		$tab_1["lbl_no_factura"] 		= $this->lang_item("no_factura");
		$tab_1["lbl_fecha_factura"]     = $this->lang_item("fecha_factura");
		$tab_1["lbl_fecha_recepcion"]   = $this->lang_item("fecha_recepcion");

        $tab_1['button_save']             = $btn_save;
        $tab_1['button_reset']            = $btn_reset;

        if($this->ajax_post(false)){
				echo json_encode($this->load_view_unique($seccion , $tab_1, true));
		}else{
			return $this->load_view_unique($seccion , $tab_1, true);
		}
	}
	function get_data_orden(){
		$incomplete  = $this->ajax_post('incomplete');
		if($incomplete>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			echo json_encode('0|'.alertas_tpl('error', $msg ,false));
		}
		else{
			$no_orden		 = $this->ajax_post('no_orden');
	        $no_factura		 = $this->ajax_post('no_factura');
	        $fecha_factura 	 = $this->ajax_post('fecha_factura');
	        $fecha_recepcion = $this->ajax_post('fecha_recepcion');	        
		}
	}
}
?>