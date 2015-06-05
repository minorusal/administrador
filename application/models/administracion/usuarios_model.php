<?php
class usuarios_model extends Base_Model{
	/*Inserta registro de usuarios*/
	public function db_insert_data($data = array()){
		print_debug($data);
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$insert_personal = $this->insert_item($tbl['usuarios'], $data);
		$array_clave = array(
			 'user'     => ''
			,'pwd'      => ''
			,'registro' => ''
			);
		$insert_clave = $this->insert_item($tbl['claves'], $data);
		$array_usuarios = array(
			'id_personal' => $this->db->insert_id($insert)
			,'id_clave'   => $this->db->insert_id($insert_clave)
			,'id_perfil'  => $data['id_perfil']
			,'id_pais'    => $data
			);
		return $insert;
		if($insert){
			return $insert;
		}else{
			return false;
		}
	}
}