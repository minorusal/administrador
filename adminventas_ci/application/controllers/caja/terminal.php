<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class terminal extends Base_controller
{
	private $modulo;
	private $view_content;
	private $path;
	private $icon;


	function __construct()
	{
		parent::__construct();
		$this->icon         = 'fa fa-credit-card';
		$this->modulo       = 'caja';
		$this->view_content = 'terminal';
		$this->path         = $this->modulo;

	}

	private function uri_view_principal(){
		return $this->modulo.'/'.$this->view_content;
	}
	public function  index(){
		
		$data['titulo_seccion']   = $this->lang_item($this->modulo);
		$data['titulo_submodulo'] = $this->lang_item("titulo_submodulo");
		$data['icon']             = $this->icon;
		
		$js['js'][]  = array('name' => $this->modulo, 'dirname' => $this->modulo);
		
		$this->load_view($this->uri_view_principal(), $data, '');
	}

	
}