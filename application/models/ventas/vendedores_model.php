<?php
class vendedores_model extends Base_Model{

	private $vars;
	private $db1,$db2;
	private $tbl;

	public function __construct(){
		parent::__construct();		
		$this->vars		= new config_vars();
        $this->vars->load_vars('assets/cfg/dbmodel.cfg');
        $this->db2 = $this->vars->db['db2'];		
		$this->db1 = $this->vars->db['db1'];
		$this->tbl['sucursales'] = $this->db1.'.'.$this->vars->db['db1_tbl_sucursales'];
		$this->tbl['ventas_vendedores'] = $this->db2.'.'.$this->vars->db['db2_tbl_ventas_vendedores'];
		$this->tbl['administracion_entidades'] = $this->db2.'.'.$this->vars->db['db2_tbl_administracion_entidades'];
	}

	function get_vendedores($limit, $offset, $filtro="", $aplicar_limit = true){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$filtro = ($filtro=="") ? "" : "AND ( 	vv.nombre_vendedor  LIKE '%$filtro%' OR 
												vv.clave_corta  LIKE '%$filtro%' OR
												vv.rfc            LIKE '%$filtro%' OR 
												e.entidad         LIKE '%$filtro%' OR
												su.sucursal       LIKE '%$filtro%' 
											)";
		$limit = ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query = "SELECT 
						vv.id_ventas_vendedores,
						vv.nombre_vendedor,
						vv.clave_corta,
						vv.rfc,
						vv.telefonos,
						e.entidad,
						su.sucursal
					FROM $tbl[ventas_vendedores] vv
					LEFT JOIN $tbl[administracion_entidades]  e on vv.id_entidad = e.id_administracion_entidad
					LEFT JOIN $tbl[sucursales] su on vv.id_sucursal = su.id_sucursal
					WHERE vv.activo = 1 $filtro
					ORDER BY vv.id_ventas_vendedores
				$limit;";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			 return $query->result_array();
		}	
	}
	function get_existencia_vendedor($clave_corta){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$query = "SELECT * FROM $tbl[ventas_vendedores] vv WHERE vv.clave_corta = '$clave_corta'";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	function get_vendedor_unico($id_vendedor){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$query = "SELECT * FROM $tbl[ventas_vendedores] vv WHERE vv.id_ventas_vendedores = $id_vendedor";

		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	function insert_vendedor($data){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$insert = $this->insert_item($tbl['ventas_vendedores'], $data);
		return $insert;
	}
	function update_vendedor($data, $id_vendedor){
		$tbl1 	= $this->dbinfo[1]['tbl_ventas_vendedores'];
		
		$condicion = array('id_ventas_vendedores !=' => $id_vendedor, 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist($tbl1 , $condicion);
		if(!$existe){
			$condicion = "id_ventas_vendedores = $id_vendedor"; 
			$update    = $this->update_item($tbl1, $data, 'id_ventas_vendedores', $condicion);
			return $update;
		}else{
			return false;
		}
	}
}