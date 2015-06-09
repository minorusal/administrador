<?php
class valores_nutricionales_model extends Base_Model{

	public function get_valores_nutricionales_unico($id_articulo){
		// DB Info
		$tbl = $this->tbl;
		$condicion = array('id_compras_articulos =' => $id_articulo); 
		$existe    = $this->row_exist($tbl['nutricion_valores_nutricionales'], $condicion,true);
		if($existe){
			print_debug($existe);
		}
		else
		{
			return false;
		}
	}
}