<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Catalogos extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->removeCache();
		if(!$this->session->userdata('is_logged')){
			redirect('login');
		}
	}
	public function articulos(){
		$data['titulo'] = 'Catalogo de Articulos';
		$data['widgettitle'] = 'Listado de Articulos';

		$data_tbl = array(
             array('id', 'articulo', 'clave corta'),
             array('1', 'Pan', 'pan'),
             array('2', 'Jitomate', 'jito'),
             array('3', 'papas', 'potatoes')	
             );


		$tbl_plantilla = array('table_open' =>  '<table id="dyntable" class="table table-bordered responsive dataTable" aria-describedby="dyntable_info">' );

		$this->table->set_template($tbl_plantilla);
		$content_tabla =  $this->table->generate($data_tbl);	
		$data['tabla'] = $content_tabla;
		$this->load_view('inventario/catalogos', $data);
	}

	
}