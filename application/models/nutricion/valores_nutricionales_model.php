<?php
class valores_nutricionales_model extends Base_Model{

	public function get_valores_nutricionales_unico($id_articulo){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$query = "SELECT * FROM $tbl[nutricion_valores_nutricionales] vn WHERE vn.id_compras_articulos = $id_articulo";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
		else
		{
			return false;
		}
	}
}