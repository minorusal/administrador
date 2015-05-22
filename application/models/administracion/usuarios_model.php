<?php
class usuarios_model extends Base_Model{
	function get_usuarios($limit, $offset, $filtro="", $aplicar_limit = true){
		$tbl1 	= $this->dbinfo[0]['tbl_usuarios'];
		$tbl2 	= $this->dbinfo[0]['tbl_personales'];
		$tbl3 	= $this->dbinfo[0]['tbl_perfiles'];
		$tbl4 	= $this->dbinfo[0]['tbl_paises'];
		$tbl5 	= $this->dbinfo[0]['tbl_empresas'];
		$tbl6 	= $this->dbinfo[0]['tbl_sucursales'];
		$bd 	= $this->dbinfo[0]['db'];

		$filtro = ($filtro=='') ? "" : "AND ( 	sp.nombre   LIKE '%$filtro%' OR 
												sp.paterno  LIKE '%$filtro%' OR 
												sp.materno  LIKE '%$filtro%' OR  
												spl.perfil  LIKE '%$filtro%' OR 
												spa.pais    LIKE '%$filtro%' OR
												se.empresas LIKE '%$filtro%' OR
												ss.sucursal LIKE '%$filtro%' 
											)";
		$limit = ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query = "SELECT 
						su.id_usuario,
						sp.nombre,
						sp.paterno,
						sp.materno,
						spl.perfil,
						spa.pais,
						ss.sucursal,
						se.empresa
					FROM 
						$bd.$tbl1 su
					LEFT JOIN $bd.$tbl2 sp on su.id_personal = sp.id_personal
					LEFT JOIN $bd.$tbl3 spl on su.id_perfil  = spl.id_perfil
					LEFT JOIN $bd.$tbl4 spa on su.id_pais    = spa.id_pais
					LEFT JOIN $bd.$tbl5 se on su.id_empresa  = se.id_empresa
					LEFT JOIN $bd.$tbl6 ss on su.id_sucursal = ss.id_sucursal
					WHERE su.activo = 1 $filtro
					ORDER BY su.id_usuario
				$limit;";
		$query = $this->db->query($query);

		if($query->num_rows >= 1){
			 return $query->result_array();
		}	
	}
	function get_usuario_unico($id_usuario){
		$tbl1 	= $this->dbinfo[0]['tbl_usuarios'];
		$tbl2 	= $this->dbinfo[0]['tbl_personales'];
		$tbl3 	= $this->dbinfo[0]['tbl_perfiles'];
		$tbl4 	= $this->dbinfo[0]['tbl_paises'];
		$tbl5 	= $this->dbinfo[0]['tbl_empresas'];
		$tbl6 	= $this->dbinfo[0]['tbl_sucursales'];
		$bd 	= $this->dbinfo[0]['db'];

		$query = "SELECT 
						su.id_usuario,
						su.id_empresa,
						su.id_sucursal,
						su.registro,
						sp.nombre,
						sp.paterno,
						sp.materno,
						sp.telefono,
						sp.mail,
						spl.perfil,
						spa.pais,
						ss.sucursal,
						se.empresa
					FROM 
						$bd.$tbl1 su
					LEFT JOIN $bd.$tbl2 sp on su.id_personal = sp.id_personal
					LEFT JOIN $bd.$tbl3 spl on su.id_perfil  = spl.id_perfil
					LEFT JOIN $bd.$tbl4 spa on su.id_pais    = spa.id_pais
					LEFT JOIN $bd.$tbl5 se on su.id_empresa  = se.id_empresa
					LEFT JOIN $bd.$tbl6 ss on su.id_sucursal = ss.id_sucursal
					WHERE su.id_usuario = $id_usuario;";

		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
}
?>