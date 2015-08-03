<?php
class users_model extends Base_Model{

	/**
    * Busca Usuario por usuario y password, funcion principla de login
    * @param string $user
    * @param string $pwd
    * @return array
    */
	public function search_user_for_login($user, $pwd){
		// DB Info
		$tbl    = $this->tbl;
		$query  = "	SELECT 
						U.id_usuario
						,P.id_personal
						,CONCAT_WS(' ', P.nombre, P.paterno ,P.materno) as name
						,P.telefono
						,P.mail
						,P.avatar as avatar_user
						,Pa.id_pais
						,Pa.pais
						,Pa.dominio
						,Pa.avatar as avatar_pais
						,Pa.moneda
						,E.id_empresa
						,E.empresa
						,S.id_sucursal
						,S.sucursal
						,N.id_perfil
						,N.perfil
						,U.id_sucursales as user_id_sucursales
						,N.id_menu_n1
						,N.id_menu_n2
						,N.id_menu_n3
						,U.id_menu_n1 as user_id_menu_n1
						,U.id_menu_n2 as user_id_menu_n2
						,U.id_menu_n3 as user_id_menu_n3
						,U.timestamp
						,U.activo
						,C.user
					FROM $tbl[usuarios] U
					left join $tbl[personales] P on U.id_personal = P.id_personal
					left join $tbl[claves]     C on U.id_clave    = C.id_clave
					left join $tbl[perfiles]   N on U.id_perfil  = N.id_perfil
					left join $tbl[paises]     Pa on U.id_pais    = Pa.id_pais
					left join $tbl[empresas]   E on U.id_empresa  = E.id_empresa
					left join $tbl[sucursales] S on U.id_sucursal = S.id_sucursal
					WHERE md5(C.user) = md5('$user') AND C.pwd = '$pwd' AND U.activo = 1
					ORDER BY 
						N.id_perfil;
				";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}		
	}
	/**
    * Busca Usuario por su id unico de registro
    * @param integer $id_user
    * @return array
    */
	public function search_user_for_id($id_user){
		
		$tbl    = $this->tbl;
		$query  = "	SELECT 
						U.id_usuario
						,P.id_personal
						,CONCAT_WS(' ', P.nombre, P.paterno ,P.materno) as name
						,P.telefono
						,P.mail
						,P.avatar as avatar_user
						,Pa.id_pais
						,Pa.pais
						,Pa.dominio
						,Pa.avatar as avatar_pais
						,Pa.moneda
						,E.id_empresa
						,E.empresa
						,S.id_sucursal
						,S.sucursal
						,N.id_perfil
						,N.perfil
						,U.id_sucursales as user_id_sucursales
						,N.id_menu_n1
						,N.id_menu_n2
						,N.id_menu_n3
						,U.id_menu_n1 as user_id_menu_n1
						,U.id_menu_n2 as user_id_menu_n2
						,U.id_menu_n3 as user_id_menu_n3
						,U.timestamp
						,U.activo
						,C.user
					FROM $tbl[usuarios] U
					left join $tbl[personales] P on U.id_personal = P.id_personal
					left join $tbl[claves]     C on U.id_clave    = C.id_clave
					left join $tbl[perfiles]   N on U.id_perfil  = N.id_perfil
					left join $tbl[paises]     Pa on U.id_pais    = Pa.id_pais
					left join $tbl[empresas]   E on U.id_empresa  = E.id_empresa
					left join $tbl[sucursales] S on U.id_sucursal = S.id_sucursal
					WHERE U.id_usuario = $id_user  AND U.activo = 1;
				";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}		
	}
	/**
	* Consulta los modulos a los que tiene acceso el usuario de acuerdo a su perfil (tabla perfiles),
	* y de acuerdo a permisos especiales (tabla usuarios)
	* @param string $id_menu_n1
	* @param string $id_menu_n2
	* @param string $id_menu_n3
	* @param bool $root
	* @return array
	*/
	public function search_modules_for_user($id_menu_n1= '' , $id_menu_n2= '', $id_menu_n3= '', $root = false ){
		$id_menu_n1 = ($id_menu_n1 == '') ? '0' : $id_menu_n1;
		$id_menu_n2 = ($id_menu_n2 == '') ? '0' : $id_menu_n2;
		$id_menu_n3 = ($id_menu_n3 == '') ? '0' : $id_menu_n3;

		
		$tbl = $this->tbl;
		
		if($root){
			$sys_navigate_n1 = "n1.activo = 1";
			$sys_navigate_n2 = "SELECT * FROM $tbl[menu2] WHERE activo = 1";
			$sys_navigate_n3 = "SELECT * FROM $tbl[menu3] WHERE activo = 1";
		}else{
			$sys_navigate_n1 = "n1.id_menu_n1 IN ($id_menu_n1) AND n1.activo = 1";
			$sys_navigate_n2 = "SELECT * FROM $tbl[menu2] WHERE id_menu_n2 IN ($id_menu_n2) AND activo = 1";
			$sys_navigate_n3 = "SELECT * FROM $tbl[menu3] WHERE id_menu_n3 IN ($id_menu_n3) AND activo = 1";
		}
		$query = "	SELECT 
						n1.id_menu_n1
						,n1.menu_n1
						,n1.routes as menu_n1_routes
						,n1.icon as menu_n1_icon
						,n2.id_menu_n2
						,n2.menu_n2
						,n2.routes as menu_n2_routes
						,n2.icon as menu_n2_icon
						,n3.id_menu_n3
						,n3.menu_n3
						,n3.routes as menu_n3_routes
						,n3.icon as menu_n3_icon
					FROM $tbl[menu1] n1
					LEFT JOIN ($sys_navigate_n2 )  n2 ON n1.id_menu_n1 = n2.id_menu_n1
					LEFT JOIN ($sys_navigate_n3)  n3 ON n3.id_menu_n2 = n2.id_menu_n2
					WHERE
						$sys_navigate_n1
					ORDER BY 
						n1.order, n2.order,n3.order;
				";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}		
	}


	/**
	* Consulta los usuarios para mostrarlos en lista y hacer busquedas,
	* @param array $data
	* @return array
	*/
	public function get_users($data = array()){
		// DB Info		
		$tbl = $this->tbl;
		// Query
		$aplicar_user   = (isset($data['user']))?$data['user']:false;
		$filtro         = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$user           = ($aplicar_user)?"AND u.id_usuario <> $aplicar_user" : "";
		$filtro = ($filtro) ? "AND (u.id_usuario = $filtro OR
									p.nombre like '%$filtro%' OR
									p.paterno like '%$filtro%' OR
									p.materno like '%$filtro%' OR
									pe.perfil like '%$filtro%' OR
									a.area like '%$filtro%' OR
									pu.puesto like '%$filtro%' OR
									c.user like '%$filtro%')" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		//Query
		$query = "	SELECT
						u.id_usuario 
						,p.id_personal
						,CONCAT_WS(' ',p.nombre, p.paterno, p.materno) as nombre
						,p.nombre as nom
						,p.paterno
						,p.materno
						,p.telefono
						,p.mail
						,a.id_administracion_areas
						,pu.id_administracion_puestos
						,pe.id_perfil
						,c.user
						,pe.perfil
						,a.area
						,pu.puesto
						,p.edit_id_usuario
						,p.edit_timestamp
						,p.timestamp
					FROM $tbl[personales] p
					LEFT JOIN $tbl[usuarios] u on u.id_personal = p.id_personal 
					LEFT JOIN $tbl[claves] c on c.id_clave = u.id_clave
					LEFT JOIN $tbl[perfiles] pe on pe.id_perfil = u.id_perfil
					LEFT JOIN $tbl[administracion_areas] a on a.id_administracion_areas = u.id_area
					LEFT JOIN $tbl[administracion_puestos] pu on pu.id_administracion_puestos = u.id_puesto
					WHERE u.activo = 1 $user $filtro
					ORDER BY p.id_personal ASC
					$limit
					";
					//print_debug($query);
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	
}

?>