<?php
class entidades_model extends Base_Model
{
	public function get_entidades($limit, $offset, $filtro="", $aplicar_limit = true){
		$query = "	SELECT 
						en.id_administracion_entidad
						,en.entidad
					FROM
						00_av_mx.av_administracion_entidades en
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
}