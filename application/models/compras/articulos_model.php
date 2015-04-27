<?php
class articulos_model extends Base_Model{

	public function insert_articulo($data){
		$existe = $this->row_exist('av_cat_presentaciones', array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$query = $this->db->insert_string('av_cat_presentaciones', $data);
			$query = $this->db->query($query);

			return $query;
		}else{
			return false;
		}
	}
}
?>