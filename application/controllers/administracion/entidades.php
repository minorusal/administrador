<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class entidades extends Base_Controller
{
	private $modulo;
	private $submodulo;
	private $seccion;
	private $view_content;
	private $path;
	private $icon;

	private $offset $limit_max;
	private $tab, $tab1, $tab2, $tab3;

	public function __construct()
	{
		parent::__construct();
		$this->modulo    = 'administracion';
		$this->submodulo = 'submodulo';
		$this->seccion   = 'entidades';
		$this->icon      = 'fa fa-map-marker';
		$this->path 	 =  $this->modulo.'/'.$this->seccion;
		$this->view_content = 'content';
		$this->limit_max = 5;
		$this->offset = 0;
		//Tabs
		$this->tab1 = 'agregar';
		$this->tab2 = 'listado';
		$this->tab3 = 'detalle';
		//DB Model
		$this->load->model($this->modulo.'/'.$this->seccion.'_model','db_model');
		//Diccionario
		$this->lang->load($this->modulo.'/'.$this->seccion,"es_Es");
	}

	public function config_tabs()
	{
		$tab_1  = $this->tab1;
		$tab_2  = $this->tab2;
		$tab_3  = $this->tab3;
		$path   = $this->path;
		$pagina = (is_numeric($this->uri_segment_end())?$this->uri_segment_end():"");
		//Nombre de Tabs
		$config_tab['names'] = array(
			 $this->lang_item($tab_1)
			,$this->lang_item($tab_2)
			,$this->lang_item($tab_3)
			);
		//HREF de Tabs
		$config_tab['links'] = array(
			 $path.$tab_1
			,$path.$tab_2.'/'.$pagina
			,$tab_3
			);
		//Acción de tabs
		$config_tab['attr'] = array(
			 'load_content'
			,'load_content'
			,''
			);
		//Atributos
		$config_tab['attr'] = array()'','',array('style' = 'display:none'));
		return $config_tab;
	}
	private function uri_view_principal()
	{
		return $this->modulo.'/'.$this->view_content;
	}

	public function index()
	{
		$tabl_inicial = 2;
		$view_listado = $this->listado();
		$contenidos_tab = $view_listado;
		$data['titulo_seccion'] = $this->lang_item($this->seccion);
		$data['titulo_submodulo'] = $this->lang_item('titulo_submodulo');
		$data['icon'] = $this->icon;
		$data['tabs'] =  tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);
		$js['js'][] = array('name' => $this->seccion, 'dirname' => $this->modulo);
		$rhis->load_view($this->uri_view_principal(), $data, $js)
	}

	public function listado($offset = 0)
	{
		//Crear tabla con listado de elementos capturados
		$seccion       = '/'.$this->$seccion;
		$tab_detalle   = $this->tab3;
		$limit         = $this->limit_max;
		$uri_view      = $this->modulo.$seccion;
		$yri_link      = $this->patch.$this->tab3;
		$sqlData       = array(
			 'buscar' => $filtro
			,'offset' => $offset
			,'limit'  => $limit
			);
		$uri_segment = $this->uri_segment();
		$total_rows = count($this->db_model->get_entidades_default($sqlData));
		$sqlData['aplicar_limit'] = false;
		$list_content = $this->db_model->get_entidades_default();

	}
}