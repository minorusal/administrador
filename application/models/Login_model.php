<?php


class Login_model extends CI_Model{
	/**
    * Validate the login's data with the database
    * @param string $user
    * @param string $pwd
    * @return void
    */
	function validate_user($user, $pwd){
		$query  = "	SELECT 
						U.id_usuario
						,CONCAT_WS(' ', P.nombre, P.paterno ,P.materno) as name
						,P.telefono
						,P.mail
						,P.avatar
						,E.id_empresa
						,E.empresa
						,S.id_sucursal
						,S.sucursal
						,N.id_nivel
						,N.nivel
						,U.registro
						,U.activo
						,C.user
					FROM 
						av_usuarios U
					left join av_personales P on U.id_personal = P.id_personal
					left join av_claves     C on U.id_clave    = C.id_clave
					left join av_niveles    N on U.id_nivel    = N.id_nivel
					left join av_empresas   E on U.id_empresa  = E.id_empresa
					left join av_sucursales S on U.id_sucursal = S.id_sucursal
					WHERE C.user = '$user' AND C.pwd = '$pwd' AND U.activo = 1;
				";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result();
		}		
	}

	function search_user_for_id($id_user){
		$query  = "	SELECT 
						U.id_usuario
						,CONCAT_WS(' ', P.nombre, P.paterno ,P.materno) as name
						,P.telefono
						,P.mail
						,P.avatar
						,E.id_empresa
						,E.empresa
						,S.id_sucursal
						,S.sucursal
						,N.id_nivel
						,N.nivel
						,U.registro
						,U.activo
						,C.user
					FROM 
						av_usuarios U
					left join av_personales P on U.id_personal = P.id_personal
					left join av_claves     C on U.id_clave    = C.id_clave
					left join av_niveles    N on U.id_nivel    = N.id_nivel
					left join av_empresas   E on U.id_empresa  = E.id_empresa
					left join av_sucursales S on U.id_sucursal = S.id_sucursal
					WHERE U.id_usuario = $id_user  AND U.activo = 1;
				";
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result();
		}		
	}


}

?>