<?php
class ordenes_model extends Base_Model{

	public function insert_orden($data){
		$existe = $this->row_exist('av_compras_ordenes');
		if(!$existe){
			$query = $this->db->insert_string('av_compras_ordenes', $data);
			$query = $this->db->query($query);

			return $query;
		}else{
			return false;
		}
	}
	public function update_orden($data=array()){
		$resultado = false;
		$id_compras_orden   = (isset($data['id_compras_orden']))?$data['id_compras_orden']:false;
		$filtro 			= ($id_compras_orden)?"id_compras_orden='$id_compras_orden'":'';
		if($id_compras_orden){
			$query 		= $this->db->update_string('av_compras_ordenes', $data, $filtro);
			$resultado 	= $this->db->query($query);
		}
		return $resultado;
	}
	public function get_ordenes($data=array()){
		// Filtro
		$buscar = (isset($data['buscar']))?$data['buscar']:false;
		$filtro = ($buscar) ? "" : "";
		// Limit
		$limit 			= ($data['limit'])?$data['limit']:false;
		$offset 		= ($data['offset'])?$data['offset']:false;
		$aplicar_limit 	= ($data['aplicar_limit'])?true:false;
		$limit = ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		// Query
		$query = "	SELECT 
						 id_compras_orden
						,orden_num
						,razon_social
						,descripcion
					FROM vw_compras_orden_articulos
					WHERE 1 $filtro
					GROUP BY orden_num ASC
					$limit
					";
					// dump_var($query);
      	// Execute querie
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	public function get_orden_unico($id_compras_orden){
		$query = "SELECT * FROM vw_compras_orden_articulos WHERE id_compras_orden = $id_compras_orden";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	
}
?>