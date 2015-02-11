<?php
class Users_model extends CI_Model{
	/**
    * Busca Usuario por usuario y password, funcion principla de login
    * @param string $user
    * @param string $pwd
    * @return array
    */
	function search_user_for_login($user, $pwd){
		$query  = "	SELECT 
						U.id_usuario
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
						,N.id_nivel
						,N.nivel
						,N.id_modulo
						,U.registro
						,U.activo
						,C.user
					FROM 
						sys_usuarios U
					left join sys_personales P on U.id_personal = P.id_personal
					left join sys_claves     C on U.id_clave    = C.id_clave
					left join sys_niveles    N on U.id_nivel    = N.id_nivel
					left join sys_paises     Pa on U.id_pais    = Pa.id_pais
					left join sys_empresas   E on U.id_empresa  = E.id_empresa
					left join sys_sucursales S on U.id_sucursal = S.id_sucursal
					WHERE C.user = '$user' AND C.pwd = '$pwd' AND U.activo = 1;
				";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result();
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
						,N.id_nivel
						,N.nivel
						,N.id_modulo
						,U.registro
						,U.activo
						,C.user
					FROM 
						sys_usuarios U
					left join sys_personales P on U.id_personal = P.id_personal
					left join sys_claves     C on U.id_clave    = C.id_clave
					left join sys_niveles    N on U.id_nivel    = N.id_nivel
					left join sys_paises     Pa on U.id_pais    = Pa.id_pais
					left join sys_empresas   E on U.id_empresa  = E.id_empresa
					left join sys_sucursales S on U.id_sucursal = S.id_sucursal
					WHERE U.id_usuario = $id_user  AND U.activo = 1;
				";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result();
		}		
	}
	/**
	* Consulta los modulos a los que tiene acceso el usuario
	* @param array $id_modulo
	* @return array
	*/
	function search_modules_for_user($id_modulo){
		$id_modulo = implode(',', $id_modulo);
		$query  = "	SELECT 
						 M.id_modulo
						,M.modulo
						,M.registro
						,M.activo
					FROM
						sys_modulos M
					WHERE
						M.id_modulo IN ( $id_modulo ) AND M.activo = 1;
				";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result();
		}		
	}


}

?>