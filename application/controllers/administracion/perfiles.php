<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class perfiles extends Base_Controller { 
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
		$this->seccion          = 'perfiles';
		$this->icon 			= 'iconfa-key'; #Icono de modulo
		$this->path 			= $this->modulo.'/'.$this->seccion.'/';
		$this->view_agregar     = $this->modulo.'/'.$this->seccion.'/'.$this->seccion.'_agregar';
		$this->view_detalle     = $this->modulo.'/'.$this->seccion.'_detalle';
		$this->view_content 	= 'content';
		$this->limit_max		= 5;
		$this->offset			= 0;

		$this->tab_indice 		= array(
									 'agregar'
									,'listado'
									,'detalle'
								);
		for($i=0; $i<=count($this->tab_indice)-1; $i++){
			$this->tab[$this->tab_indice[$i]] = $this->tab_indice[$i];
		}
		$this->load->model($this->seccion.'_model','db_model');
		$this->lang->load($this->modulo.'/'.$this->seccion,"es_ES");
	}
}