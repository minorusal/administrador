<?php
class juegos_model extends Base_Model{
	/**
	* DESCRIPCIÓN: Función que permite obtener la cantidad de regisrtros
	* de la tabla datalogos_conectores
	* @return array
	**/
	public function get_db_count_conectores(){
		$tbl = $this->tbl;
		$query = "SELECT COUNT(C.id_conector)
				   FROM $tbl[catalogos_conectores] C
				   WHERE C.activo = 1";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
}