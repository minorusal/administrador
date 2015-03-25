<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Catalogos extends Base_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->removeCache();
		if(!$this->session->userdata('is_logged')){
			redirect('login');
		}
	}
	public function articulos(){
		$this->load->model('inventario/Catalogos_model');
		$dominio = $this->session->userdata('dominio');
		$this->load_database($dominio);
		$cat_articulos = $this->Catalogos_model->articulos($dominio);
		$cat_articulos = $this->object_to_array($cat_articulos);

		//$this->print_format($cat_articulos);


		$data['titulo']      = 'Catalogo de Articulos';
		$data['widgettitle'] = 'Listado de Articulos';

		$data_tbl = $cat_articulos;


		$tbl_plantilla = array('table_open' =>  '<table class="table table-bordered table-hover responsive " ' );

		$this->table->set_template($tbl_plantilla);
		$content_tabla =  $this->table->generate($data_tbl);	
		$data['tabla'] = $content_tabla;
		$this->load_view('inventario/catalogos', $data);
	}

	
}