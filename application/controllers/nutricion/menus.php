<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class menus extends Base_Controller{

	private $modulo;
	private $submodulo;
	private $view_content;
	private $path;
	private $icon;
	private $offset, $limit_max;
	private $tab, $tab1, $tab2, $tab3;

	public function __construct(){
		parent::__construct();
		$this->modulo 			= 'nutricion';
		$this->submodulo		= 'menus';
		$this->icon 			= 'fa fa-cutlery';
		$this->tab1 			= 'agregar';
		$this->tab2 			= 'listado';
		$this->tab3             = 'detalle';
		$this->view_content 	= 'content';
		$this->path 			= $this->modulo;
		$this->path_view        = $this->path.'/'.$this->submodulo;
		$this->view_agregar     = $this->path_view.'/'.$this->submodulo.'_'.$this->tab1;
		$this->view_detalle     = $this->path_view.'/'.$this->submodulo.'_'.$this->tab3;
		$this->view_listado     = $this->path.'/'.$this->tab2;
		$this->limit_max		= 10;
		$this->offset			= 0;
		// DB Model
		$this->load->model($this->modulo.'/'.$this->submodulo.'_model','db_model' );
		$this->load->model('administracion/sucursales_model','sucursales');
		// Diccionario
		$this->lang->load( $this->modulo.'/'.$this->submodulo,"es_ES" );
	}

	public function config_tabs(){
		$tab_1 	= $this->tab1;
		$path  	= $this->path;

		$config_tab['names']    = array( $this->lang_item($tab_1) ); 
		$config_tab['links']    = array( $path.$tab_1 ); 
		$config_tab['action']   = array('');
		$config_tab['attr']     = array('');
		$config_tab['style_content'] = array('','');
		
		return $config_tab;
	}
	
	private function uri_view_principal(){
		return $this->modulo.'/'.$this->view_content;
	}

	public function index(){
		$tabl_inicial 			  = 1;	
		$contenidos_tab           = $this->configuracion_menu();
		$data['titulo_submodulo'] = $this->lang_item($this->modulo);
		$data['titulo_seccion']   = $this->lang_item($this->submodulo);
		$data['icon']             = $this->icon;
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);	
		
		$js['js'][]  = array('name' => $this->modulo, 'dirname' => $this->modulo);
		$this->load_view($this->uri_view_principal(), $data, $js);
	}

	public function configuracion_menu(){
		$data['lbl_sucursal'] = $this->lang_item('lbl_sucursal');
		$sqlData = array(
							 'buscar' => 0
							,'offset' => 0
							,'limit'  => 0
						);
		$dropdown_sucursales = array(
						 'data'		=> $this->sucursales->db_get_data($sqlData)
						,'value' 	=> 'id_sucursal'
						,'text' 	=> array('clave_corta','sucursal')
						,'name' 	=> "lts_sucursales"
						,'leyenda' 	=> "-----"
						,'class' 	=> "requerido"
						,'event'    => array('event'      => 'onchange', 
											'function'    => 'load_ciclos', 
											'params'      => array('this.value'), 
											'params_type' => array(false))
					);
		$sucursales = dropdown_tpl($dropdown_sucursales);


		$data['dropdown_sucursales'] = $sucursales;

		if($this->ajax_post(false))
		{
			echo json_encode($this->load_view_unique($this->view_agregar,$data,true));
		}
		else
		{
			return $this->load_view_unique($this->view_agregar, $data, true);
		}
	}
}