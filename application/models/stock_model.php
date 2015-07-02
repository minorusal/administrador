<?php
class stock_model extends Base_Model{
/**
* Descripción:	Manejo de stock
* @author 		Oscar Maldonado
* Creación:		2015-07-02
* Modificación:	
*/

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