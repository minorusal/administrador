<?php
class users_model extends Base_Model{
	/**
    * Busca Usuario por usuario y password, funcion principla de login
    * @param string $user
    * @param string $pwd
    * @return array
    */
	function search_user_for_login($user, $pwd){
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
						,E.id_empresa
						,E.empresa
						,S.id_sucursal
						,S.sucursal
						,N.id_perfil
						,N.perfil
						,N.id_menu_n1
						,N.id_menu_n2
						,N.id_menu_n3
						,U.registro
						,U.activo
						,C.user
					FROM 
						sys_usuarios U
					left join sys_personales P on U.id_personal = P.id_personal
					left join sys_claves     C on U.id_clave    = C.id_clave
					left join sys_perfiles   N on U.id_perfil  = N.id_perfil
					left join sys_paises     Pa on U.id_pais    = Pa.id_pais
					left join sys_empresas   E on U.id_empresa  = E.id_empresa
					left join sys_sucursales S on U.id_sucursal = S.id_sucursal
					WHERE C.user = '$user' AND C.pwd = '$pwd' AND U.activo = 1
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
	function search_user_for_id($id_user){
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
						,E.id_empresa
						,E.empresa
						,S.id_sucursal
						,S.sucursal
						,N.id_perfil
						,N.perfil
						,N.id_menu_n1
						,N.id_menu_n2
						,N.id_menu_n3
						,U.registro
						,U.activo
						,C.user
					FROM 
						sys_usuarios U
					left join sys_personales P on U.id_personal = P.id_personal
					left join sys_claves     C on U.id_clave    = C.id_clave
					left join sys_perfiles   N on U.id_perfil   = N.id_perfil
					left join sys_paises     Pa on U.id_pais    = Pa.id_pais
					left join sys_empresas   E on U.id_empresa  = E.id_empresa
					left join sys_sucursales S on U.id_sucursal = S.id_sucursal
					WHERE U.id_usuario = $id_user  AND U.activo = 1;
				";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}		
	}
	/**
	* Consulta los modulos a los que tiene acceso el usuario
	* @param array $id_modulo
	* @return array
	*/
	function search_modules_for_user($id_menu_n1 , $id_menu_n2, $id_menu_n3, $root = false ){

		if($root){
			$sys_navigate_n1 = "n1.activo = 1";
			$sys_navigate_n2 = "SELECT * FROM sys_menu_n2 WHERE activo = 1";
			$sys_navigate_n3 = "SELECT * FROM sys_menu_n3 WHERE activo = 1";
		}else{
			$sys_navigate_n1 = "n1.id_menu_n1 IN ($id_menu_n1) AND n1.activo = 1";
			$sys_navigate_n2 = "SELECT * FROM sys_menu_n2 WHERE id_menu_n2 IN ($id_menu_n2) AND activo = 1";
			$sys_navigate_n3 = "SELECT * FROM sys_menu_n3 WHERE id_menu_n3 IN ($id_menu_n3) AND activo = 1";
		}
		$query = "	SELECT 
						 n1.menu_n1
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
					FROM
						sys_menu_n1 n1
					LEFT JOIN ($sys_navigate_n2)  n2 ON n1.id_menu_n1 = n2.id_menu_n1
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
	

}

?>