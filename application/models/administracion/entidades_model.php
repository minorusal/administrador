<?php
class entidades_model extends Base_Model
{
	public function get_entidades_default($data = array()){
		
		$tbl1 = $this->dbinfo[1]['tbl_administracion_entidades'];

		$buscar = (array_key_exists('buscar',$data))?$data['buscar']:false;
		$filtro = ($buscar) ? "AND ( 	e.ent_abrev  LIKE '%$buscar%' OR 
										e.entidad  LIKE '%$buscar%' OR
										e.clave_corta  LIKE '%$buscar%'
									)" : "";
		
		$limit 			= (array_key_exists('limit',$data)) ?$data['limit']:0;
		$offset 		= (array_key_exists('offset',$data))?$data['offset']:0;
		$aplicar_limit 	= (array_key_exists('aplicar_limit',$data)) ? $data['aplicar_limit'] : false;
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";


		$query = "	SELECT 
						*
					FROM
						$tbl1 e
					WHERE e.activo = 1 $filtro
					$limit
					";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	/*public function get_entidades($limit, $offset, $filtro="", $aplicar_limit = true){
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
	}*/
}