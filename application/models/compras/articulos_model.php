<?php
class articulos_model extends Base_Model{

	private $vars;
	private $db1,$db2;
	private $tbl;

	public function __construct(){
		parent::__construct();		
		$this->vars		= new config_vars();
        $this->vars->load_vars('assets/cfg/dbmodel.cfg');
		$this->db1 = $this->vars->db['db1'];
		$this->tbl['sucursales'] = $this->db1.'.'.$this->vars->db['db1_tbl_sucursales'];
        $this->db2 = $this->vars->db['db2'];	
        $this->tbl['administracion_entidades'] = $this->db2.'.'.$this->vars->db['db2_tbl_administracion_entidades'];	
		$this->tbl['compras_articulos'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_articulos'];
		$this->tbl['compras_articulos_precios'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_articulos_precios'];
		$this->tbl['compras_lineas'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_lineas'];
		$this->tbl['compras_marcas'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_marcas'];
		$this->tbl['compras_ordenes_tipo'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_ordenes_tipo'];
		$this->tbl['compras_ordenes'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_ordenes'];
		$this->tbl['compras_ordenes_articulos'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_ordenes_articulos'];
		$this->tbl['compras_ordenes_estatus'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_ordenes_estatus'];
		$this->tbl['compras_presentaciones'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_presentaciones'];
		$this->tbl['compras_proveedores'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_proveedores'];
		$this->tbl['compras_proveedores_articulos'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_proveedores_articulos'];
		$this->tbl['compras_um'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_um'];
		$this->tbl['compras_embalaje'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_embalaje'];
		$this->tbl['vw_compras_orden_proveedores'] = $this->db2.'.'.$this->vars->db['db2_vw_compras_orden_proveedores'];
		$this->tbl['vw_articulos'] = $this->db2.'.'.$this->vars->db['db2_vw_articulos'];
		$this->tbl['vw_proveedores_articulos'] = $this->db2.'.'.$this->vars->db['db2_vw_proveedores_articulos'];
	}

	public function insert_articulo($data){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$existe = $this->row_exist($tbl['compras_articulos'], array('clave_corta'=> $data['clave_corta']));
		if(!$existe){
			$insert = $this->insert_item($tbl['compras_articulos'], $data);
			return $insert;
		}else{
			return false;
		}
	}
	public function update_articulo($data, $id_articulo){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$condicion = array('id_compras_articulo !=' => $id_articulo, 'clave_corta = '=> $data['clave_corta']); 
		$existe = $this->row_exist($tbl['compras_articulos'], $condicion);
		if(!$existe){
			$condicion = "id_compras_articulo = $id_articulo"; 
			$update    = $this->update_item($tbl['compras_articulos'], $data, 'id_compras_articulo', $condicion);
			return $update;
		}else{
			return false;
		}
	}
	public function get_articulos($limit, $offset, $filtro="", $aplicar_limit = true){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$filtro = ($filtro=="") ? "" : "AND ( 	ca.articulo  LIKE '%$filtro%' OR 
												cl.linea  LIKE '%$filtro%' OR 
												cm.marca  LIKE '%$filtro%' OR  
												cu.um  LIKE '%$filtro%' OR 
												ca.clave_corta  LIKE '%$filtro%' OR 
												ca.descripcion  LIKE '%$filtro%'
											)";
		$limit = ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query = "	SELECT 
						ca.id_compras_articulo
						,ca.articulo
						,cl.linea
						,cm.marca
						,cu.um
						,ca.clave_corta
						,ca.descripcion
					FROM $tbl[compras_articulos] ca
					LEFT JOIN $tbl[compras_lineas] cl on cl.id_compras_linea = ca.id_compras_linea 
					LEFT JOIN $tbl[compras_marcas] cm on cm.id_compras_marca = ca.id_compras_marca
					LEFT JOIN $tbl[compras_um] cu on cu.id_compras_um = ca.id_compras_um
					WHERE ca.activo = 1 $filtro
					ORDER BY ca.id_compras_articulo
				$limit
					";
      	//print_debug($query);
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	public function get_articulo_unico($id_articulo){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$query = "SELECT * FROM $tbl[compras_articulos] ca WHERE ca.id_compras_articulo = $id_articulo";

		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}

}
?>