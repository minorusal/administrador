<?php
class sync_model extends Base_Model{

	public function get_data_news($table, $columns , $last_id = array(), $limit = 100)
	{
		if(!empty($last_id)){
			$condicion = ' '.$last_id[0].' > '.$last_id[1];
		}else{
			$condicion = '';
		}

		$tbl     = $this->tbl;
		$columns = implode(',', $columns);
		$query   = "SELECT $columns FROM $tbl[$table] WHERE  $condicion LIMIT $limit";
		$query   = $this->db->query($query);

		if($query->num_rows >= 1){
			return $query->result_array();
		}else{
			return false;
		}
	}

	public function get_data_updates($table , $columns, $filter_id = array() ,$filter_timestamp =  array(),  $limit = 100)
	{
		if(!empty($filter_timestamp)){
			$filtro_1 = ' '.$filter_timestamp[0].' > "'.$filter_timestamp[1].'"';
		}else{
			$filtro_1 = '';
		}

		if($filter_id){
			$filtro_2 = 'AND '.$filter_id[0].' > "'.$filter_id[1].'"';
		}else{
			$filtro_2 = '';
		}

		$tbl     = $this->tbl;
		$columns = implode(',', $columns);
		$query   = "SELECT $columns FROM $tbl[$table] WHERE  $filtro_1 $filtro_2  LIMIT $limit";
		$query   = $this->db->query($query);

		if($query->num_rows >= 1){
			return $query->result_array();
		}else{
			return false;
		}
	}

	public function get_maxId($table, $primary_key, $condition = array() ){
		$tbl   = $this->tbl;
		$condition = ( !empty($condition) ) ? 'WHERE '.implode(' ', $condition) : '';
		$query = "SELECT IF(max($primary_key) is NULL , 0, max($primary_key) ) as last_id FROM $tbl[$table] $condition";
		$query = $this->db->query($query);
		$row   = $query->row();
		return $row->last_id;
	}

	public function insert_packages($table, $data){
		$tbl   = $this->tbl;
		$this->db->insert_batch($tbl[$table], $data);
	}

}