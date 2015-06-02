<?php
class variables_model extends Base_Model
{
	private $db1;
	private $tbl1;
	
	public function __construct()
	{
		parent::__construct();
		$this->db1  = $this->dbinfo[1]['db'];
		$this->tbl1 = $this->dbinfo[1]['tbl_administracion_variables'];
	}

	//Función que obtiene toda la información de la tabla av_administracion_creditos
	public function db_get_data($data=array()){
		$buscar = (isset($data['buscar']))?$data['buscar']:false;
		$filtro = ($buscar) ? "and id_vars = $buscar " 
							: "";
		$tbl_variables = $this->db1.'.'.$this->tbl1;
		$query = "SELECT 
						va.nombre,
						va.valor,
						va.tabla,
						va.campo
					FROM $tbl_variables va
					WHERE va.activo = 1 $filtro;";
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	public function update($data=array()){	
		$resultado      = false;
		$tbl_variables  = $this->db1.'.'.$this->tbl1;
		$id_vars   		= (isset($data['id_vars']))?$data['id_vars']:false;
		//$datos['valor'] = $data['valor'];
		$filtro 		= ($id_vars)?"id_vars='$id_vars'":'';
		if($id_vars){
			$update    = $this->update_item($tbl_variables, $data, 'id_vars', $filtro);
			return $update;
		}
		return $resultado;
	}

}
?>