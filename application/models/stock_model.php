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
}
?>