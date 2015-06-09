<?php
class valores_nutricionales_model extends Base_Model{

	public function get_valores_nutricionales_unico($id_articulo){
		// DB Info
		$tbl = $this->tbl;
		//print_debug($tbl['nutricion_valores_nutricionales']);
		$condicion = array('id_compras_articulos =' => $id_articulo); 
		$existe    = $this->row_exist($tbl['nutricion_valores_nutricionales'], $condicion);
		if($existe){
			
		}
		else
		{
			return false;
		}
	}
}