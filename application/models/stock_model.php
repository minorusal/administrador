<?php
class stock_model extends Base_Model{
/**
* Descripción:	Manejo de stock
* @author 		Oscar Maldonado
* Creación:		2015-07-02
* Modificación:	
*/
	/*STOCK*/
	public function insert_data_stock($data=array()){
	/**
    * Descripción:	Inserta linea en tabla de stock
    * @author 		Oscar Maldonado
    * Creación:		2015-07-09
    * Modificación:	
    */
		// DB Info
		$tbl = $this->tbl;
		// Query
		if($insert = $this->insert_item($tbl['almacen_stock'], $data, true)){
			return $insert;
		}else{
			return false;
		}
	}

	public function update_data_stock($data=array()){
	/**
    * Descripción:	Actualiza linea en tabla de stock
    * @author 		Oscar Maldonado
    * Creación:		2015-07-09
    * Modificación:	
    */
		// DB Info
		$tbl = $this->tbl;
		// Query
		$id_stock = (isset($data['id_stock']))?$data['id_stock']:false;
		$condicion = ($id_stock)?"id_stock='$id_stock'":'';
		if($update    = $this->update_item($tbl['almacen_stock'], $data, 'id_stock', $condicion)){
			return $update;
		}else{
			return false;
		}
	}

	/*STOCK_LOGS*/
	public function insert_stock_log($data=array()){
	/**
    * Descripción:	Inserta linea en tabla de stock_logs
    * @author 		Oscar Maldonado
    * Creación:		2015-07-02
    * Modificación:	
    */
		// DB Info
		$tbl = $this->tbl;
		// Query
		if($insert = $this->insert_item($tbl['almacen_stock_logs'], $data,true)){
			return $insert;
		}else{
			return false;
		}
	}

	/*STOCK DELETED*/
	public function copy_stock_to_deleted($data=array()){
	/**
    * Descripción:	Copia los registos de la tabla de stock a la tabla de stock_deleted
    * @author 		Oscar Maldonado
    * Creación:		2015-07-15
    * Modificación:	
    */
		// DB Info
		$tbl = $this->tbl;
		// Filtro
		$id_stock 	= (isset($data['id_stock']))?$data['id_stock']:false;
		$filtro 	= ($id_stock)?"AND id_stock='$id_stock'":'';
		// Query
		$sql = "INSERT INTO $tbl[almacen_stock_deleted] (SELECT * FROM $tbl[almacen_stock] WHERE activo=0 AND stock=0 $filtro);";
		$resultado = ($this->db->query($sql))?true:false;
		return $resultado;
	}

	public function delete_stock_en_cero($data=array()){
	/**
    * Descripción:	Borrado físico de los registros con stock = 0
    * @author 		Oscar Maldonado
    * Creación:		2015-07-15
    * Modificación:	
    */
		// DB Info
		$tbl = $this->tbl;
		// Filtro
		$id_stock 	= (isset($data['id_stock']))?$data['id_stock']:false;
		$filtro 	= ($id_stock)?"AND id_stock='$id_stock'":'';
		// Query
		$sql = "DELETE FROM $tbl[almacen_stock] WHERE activo=0 AND stock=0 $filtro;";
		$query = ($this->db->query($sql))?true:false;
		return $query;
	}
}
?>