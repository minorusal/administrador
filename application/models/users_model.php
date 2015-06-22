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
		$tbl = $this->tbl;
		// Query
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
		// DB Info
		$tbl = $this->tbl;
		// Query
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
		$id_menu_n1 = ($id_menu_n1=='') ? '0' : $id_menu_n1;
		$id_menu_n2 = ($id_menu_n2=='') ? '0' : $id_menu_n2;
		$id_menu_n3 = ($id_menu_n3=='') ? '0' : $id_menu_n3;

		// DB Info
		$tbl = $this->tbl;
		// Query
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
	* Consulta la info de un perfil en especifico
	* y de acuerdo a permisos especiales (tabla usuarios)
	* @param string $id_perfil
	* @return array
	*/

	
	public function search_data_perfil($id_perfil){
		// DB Info
		$tbl = $this->tbl;
		$query = "SELECT * FROM $tbl[perfiles] WHERE id_perfil = $id_perfil";
		$query = $this->db->query($query);
		return $query->result_array();
	}
	
	public function db_get_data($data = array()){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$filtro = ($filtro=='') ? "" : "AND ( 	name   LIKE '%$filtro%' 
											)";
		$limit = ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		$query = "	SELECT 
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
					WHERE U.id_usuario = $id_user  AND U.activo = 1 $filtro $limit";
		
		print_debug($query);
		$query = $this->db->query($query);

		if($query->num_rows >= 1){
			 return $query->result_array();
		}	
	}
	public function db_get_total_rows($data=array()){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$buscar = (array_key_exists('buscar',$data))?$data['buscar']:false;
		$filtro = ($buscar) ? "AND ( 	p.razon_social  LIKE '%$buscar%' OR 
										p.nombre_comercial  LIKE '%$buscar%' OR
										p.clave_corta  LIKE '%$buscar%' OR
										p.rfc  LIKE '%$buscar%' OR
										e.entidad LIKE '%$buscar%'
											)" : "";
		$query = "SELECT *
					FROM $tbl[usuarios] U
					left join $tbl[personales] P on U.id_personal = P.id_personal
					left join $tbl[claves]     C on U.id_clave    = C.id_clave
					left join $tbl[perfiles]   N on U.id_perfil  = N.id_perfil
					left join $tbl[paises]     Pa on U.id_pais    = Pa.id_pais
					left join $tbl[empresas]   E on U.id_empresa  = E.id_empresa
					left join $tbl[sucursales] S on U.id_sucursal = S.id_sucursal
					WHERE U.id_usuario = $id_user  AND U.activo = 1 $filtro";

      	$query = $this->db->query($query);
		return $query->num_rows;
	}
	/*Inserta registro de usuarios*/
		public function db_insert_data($data = array()){
			// DB Info		
			$tbl = $this->tbl;
			// Query
			$personal = array(
					 'nombre'     => $data['nombre']
					,'paterno'    => $data['paterno']
					,'materno'    => $data['paterno']
					,'telefono'   => $data['telefono']
					,'mail'       => $data['mail']
					,'id_usuario' => $data['id_usuario']
					,'timestamp'  => $data['timestamp']
							 );
			$insert_personal = $this->insert_item($tbl['personales'], $personal);
			$id_personal = $this->last_id();
			$array_clave = array(
				 'user'       => ''
				,'pwd'        => ''
				,'id_usuario' => $data['id_usuario']
				,'timestamp'  => $data['timestamp']
				);
			$insert_clave = $this->insert_item($tbl['claves'], $array_clave);
			$id_clave = $this->last_id();
			$array_usuarios = array(
				'id_personal'  	   => $id_personal
				,'id_clave'    	   => $id_clave
				,'id_perfil'   	   => $data['id_perfil']
				,'id_empresa'  	   => $data['id_empresa']
				,'id_pais'     	   => $data['id_pais']
				,'id_sucursal' 	   => $data['id_sucursal']
				,'id_puesto'   	   => $data['id_puesto']
				,'id_area'     	   => $data['id_area']
				,'id_menu_n1'  	   => $data['id_menu_n1']
				,'id_menu_n2'      => $data['id_menu_n2']
				,'id_menu_n3'      => $data['id_menu_n3']
				,'id_usuario_reg'  => $data['id_usuario']
				,'timestamp'       => $data['timestamp']
				);
			$insert_usuario = $this->insert_item($tbl['usuarios'], $array_usuarios);
			if($insert_usuario){
				return $insert_usuario;
			}else{
				return false;
			}
	}
	

}

?>