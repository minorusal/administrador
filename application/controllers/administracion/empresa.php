<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class empresa extends Base_Controller {

	private $modulo;
	private $submodulo;
	private $seccion;
	//private $view_content;
	private $path;
	private $icon;

	private $offset, $limit_max;
	private $tab, $tab1;

	private $titulo, $mensaje, $template;
	
	public function __construct(){
		parent::__construct();
		$this->modulo 			= 'administracion';
		$this->submodulo 		= 'empresa';
		$this->icon 			= 'fa fa-building-o'; 
		$this->template 		= 'contentInfo';
		$this->path 			= $this->modulo.'/'.$this->submodulo.'/'; #administracion/entidades
		$this->view_content 	= 'contentInfo';
		$this->limit_max		= 5;
		$this->offset			= 0;
		$this->titulo 			= 'Empresa';
		$this->mensaje 			= '<h4>Bienvenido al sistema AdminVentas.</h4> <br/>Por favor seleccione una opción del menú lateral.';
		// Tabs
		$this->tab1 			= 'inicio';
		$this->load->model($this->modulo.'/'.$this->submodulo.'s_model','db_model');
		// Diccionario
		//$this->lang->load($this->modulo.'/'.$this->seccion,"es_ES");
	}

	public function config_tabs(){
		$tab_1 	= $this->tab1;
		$path  	= $this->path;
		// Nombre de Tabs
		$config_tab['names']    = array( $this->titulo ); 
		// Href de tabs
		$config_tab['links']    = array($path.$tab_1 ); 
		// Accion de tabs
		$config_tab['action']   = array('');
		// Atributos 
		$config_tab['attr']     = array('');
		return $config_tab;
	}

	public function index(){
		
		$sqlData = array(
			 'buscar'      	=> ''
			,'offset' 		=> 0
			,'limit'      	=> true
		);
		$datos = $this->db_model->db_get_data($sqlData);
		$button_save  = form_button(array('class'=>'btn btn-primary', 'name'=>'save_empresa', 'onclick'=>'agregar()','content'=>'Guardar'));
		$tabl_inicial 			  = 1;
		$contenidos_tab           = $this->mensaje;
		$data['titulo_seccion']   = $this->titulo;
		$data['titulo_submodulo'] = 'Bienvenido';
		$data['icon']             = $this->icon;
		$data['Titulo']           = $this->titulo;
		$data['id_empresa']         = $datos[0]['id_empresa'];
		$data['empresa']          = 'Empresa';
		$data['emp']         = $datos[0]['empresa'];
		$data['logotipo']         = 'Logotipo';
		$data['imagen']           = base_url().'assets/avatar/users/00001.jpg';
		$data['razon_social']     = 'Razón Social';
		$data['r_social']         = $datos[0]['razon_social'];
		$data['r_f_c']            = 'RFC';
		$data['rfc']              = $datos[0]['rfc'];
		$data['telefono']         = 'Teléfono';
		$data['tel']              = $datos[0]['telefono'];
		$data['direccion']        = 'Dirección';
		$data['dir']              = $datos[0]['direccion'];
		$data['button_save']      = $button_save;
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);	
		$js['js'][]  = array('name' => 'empresas', 'dirname' => 'administracion');
		$this->load_view($this->template, $data,$js);	
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
			$data_insert = array(
				  'id_empresa'   => $this->ajax_post('id_empresa')
				 ,'empresa'      => $this->ajax_post('empresa')
				 ,'razon_social' => $this->ajax_post('razon_social')
				 ,'rfc'          => $this->ajax_post('rfc')
				 ,'telefono'     => $this->ajax_post('telefono')
				 ,'direccion'    => $this->ajax_post('direccion')
				 ,'id_usuario'   => $this->session->userdata('id_usuario')
				 ,'timestamp'    => $this->timestamp()
				);

			$insert = $this->db_model->db_update_data($data_insert);

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