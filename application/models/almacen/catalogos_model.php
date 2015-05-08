<?php
class catalogos_model extends Base_Model
{
	/*ALMACENES*/
	public function get_almacenes_unico($id_almacen){
		$query = "SELECT * FROM av_almacen_almacenes cp WHERE cp.id_almacen_almacenes = $id_almacen";

		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}

	public function get_almacenes($limit, $offset, $filtro="", $aplicar_limit = true){
		$filtro = ($filtro=="") ? "" : "AND (
												cp.clave_corta like '%$filtro%'
											OR
												cp.descripcion like '%$filtro%'
										) ";
		$limit = ($aplicar_limit) ?  "LIMIT $offset ,$limit " : "";
		$query = "	SELECT 
						cp.id_almacen_almacenes
						,cp.clave_corta
						,cp.descripcion
					FROM
						av_almacen_almacenes cp
					ORDER BY cp.id_almacen_almacenes
					$limit";
      	
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}

	public function insert_almacen($data){
		$existe = $this->row_exist('av_almacen_almacenes', array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$query = $this->db->insert_string('av_almacen_almacenes', $data);
			$query = $this->db->query($query);

			return $query;
		}else{
			return false;
		}
	}
	public function update_almacenes($data, $id_almacen){
		$condicion = array('id_almacen_almacenes !=' => $id_almacen, 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist('av_almacen_almacenes', $condicion);
		if(!$existe){
			$condicion = "id_almacen_almacenes = $id_almacen"; 
			$query = $this->db->update_string('av_almacen_almacenes', $data, $condicion);
			$query = $this->db->query($query);
			return $query;
		}else{
			return false;
		}
	}
}

//WHERE cp.activo = 1 $filtro