<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends Base_Controller {
	/**
    * Index del Controllador
    * @return void
    */
	public function index(){
		$this->removeCache();
		if($this->session->userdata('is_logged')){
			redirect('inicio');
        }else{
        	$this->load_view_login();	
        }
	}

	/**
    * Encripta el pass recibido
    * @param string $pwd
    * @return string
    */

	public function __encript_pwd($pwd){
		return md5($pwd);
	}

	/**
    * funcion principal de login
    * @return boolean
    */
	function authentication(){	
		$this->load->model('Users_model');
		$id_user    = $this->input->post('id_user');
		if($id_user==''){
			$user   = $this->input->post('user');
			$pwd    = $this->__encript_pwd($this->input->post('pwd'));
			$data   = $this->Users_model->search_user_for_login($user, $pwd);
		}else{
			$data   = $this->Users_model->search_user_for_id($id_user);
		}
		$data       = $this->query_result_to_array($this->object_to_array($data));
		$data_count = count($data);
		
		if((is_array($data))&&(!empty($data))){
			if($data_count>1){
				echo json_encode($this->tbl_multiples_perfiles($data));
			}else{
				$data_modulos       = $this->Users_model->search_modules_for_user($data[0]['id_modulo']);
				$data_modulos       = $this->object_to_array( $data_modulos );
				$data[0]['modulos'] = $this->buil_array_navigator($data_modulos);
				$this->session->set_userdata($data[0]);

				echo 1;
			}
		}else{
			echo 0;
		}
	}
	/**
	* Prepara un array para la construccion
	* del panel de navegacion 
	* @param array $array_navigator
	* @return array
	*/
	function buil_array_navigator($navigator){
		
		foreach ($navigator as $key => $value) {
			if(!is_null($value['submodulo'])){
				if(!is_null($value['seccion'])){
					$data_navigator[$value['modulo']]['content'][$value['submodulo']]['content'][$value['seccion']] = array( 'seccion'=> $value['seccion'] , 'icon' => $value['seccion_icon'],'routes'=> $value['modulo'].'/'.$value['submodulo'].'/'.$value['seccion_routes']);
					$data_navigator[$value['modulo']]['content'][$value['submodulo']]['icon'] = $value['submodulo_icon'];
					$data_navigator[$value['modulo']]['icon'] = $value['modulo_icon'];
				}else{
					$data_navigator[$value['modulo']]['content'][$value['submodulo']] = array('icon' => $value['submodulo_icon'] , 'routes' => $value['modulo'].'/'.$value['submodulo_routes']);
					$data_navigator[$value['modulo']]['icon'] = $value['modulo_icon'];
				}
			}else{
				$data_navigator[$value['modulo']] = array('icon'=>$value['modulo_icon'], 'routes' => $value['modulo_routes']);
			}
		}
		return $data_navigator;
	}

	/**
	* Recontruye el array devuelto por la consulta 
	* en caso de multiples perfiles para 
	* @param array $query_result
	* @return array
	*/
	function query_result_to_array($query_result){
		$data = array();
		if((is_array($query_result))&&(!empty($query_result))){
			foreach ($query_result as $key => $value) {
				# code...
				$data[] = array(
								'id_usuario'  => $value['id_usuario'],
								'name'        => strtoupper($value['name']),
								'telefono'    => $value['telefono'],
								'mail'        => $value['mail'],
								'avatar_user' => $value['avatar_user'],
								'id_pais'     => $value['id_pais'],
								'pais'        => $value['pais'],
								'dominio'     => $value['dominio'],
								'avatar_pais' => $value['avatar_pais'],
								'id_empresa'  => $value['id_empresa'],
								'empresa'     => $value['empresa'],
								'id_sucursal' => $value['id_sucursal'],
								'sucursal'    => $value['sucursal'],
								'id_perfil'   => $value['id_perfil'],
								'perfil'      => $value['perfil'],
								'id_modulo'   => $value['id_modulo'],
								'id_submodulo'=> $value['id_submodulo'],
								'id_seccion'  => $value['id_seccion'],
								'registro'    => $value['registro'],
								'activo'      => $value['activo'],
								'user'        => $value['user'],
								'is_logged'   => true
						);
			}
		}
		return $data;
	}

	/**
    * En caso de multiples perfiles genera
    * una tabla para selecionar perfil de ingreso
    * @param array $data
    * @return string
    */

	function tbl_multiples_perfiles($data){
		$bool = true;
		foreach ($data as $value) {
			$avatar    = array('data' => '<img src='.base_url().'assets/avatar/'.$value['avatar_user'].' />');
			$name_user = $value['name'];
			$attr      = array(
	                            'name'    => 'perfil_ingreso',
	                            'type'    => 'radio',
	                            'value'   => $value['id_usuario'],
	                            'checked' => ($bool) ? 'true' : 'false'
	                        );
			$tbl_data[] = array(
								
								'pais'   => '<img src='.base_url().'assets/avatar/'.$value['avatar_pais'].' />'.$value['pais'],
								'perfil'  => '<a href="#" onclick="authentication_perfil('.$value['id_usuario'].')"><span>'.$value['perfil'].'</span></a>'
						);
			$bool = false;
		}

		$tbl_plantilla = array ('table_open'  => '<table class="table table-bordered responsive ">');
		$this->table->set_heading('Pais', 'Perfil');
		$this->table->set_template($tbl_plantilla);

		$tbl_info[] = array(
								'image'  => $avatar,
								'msg'    => 'Se Identifico mas de un perfil asociado a su usuario, favor de seleccionar un perfil de ingreso, gracias'
						);
		$perfiles = $this->table->generate($tbl_data);
		
		$this->table->set_heading('Bienvenido:',$name_user);
		$this->table->set_template($tbl_plantilla);
		$info = $this->table->generate($tbl_info);

		return $info.$perfiles;

	}

	/**
	* Finalizar la sesion activa
	* @return void
	*/
	function logout(){
		$this->removeCache();
		$this->session->sess_destroy();
		redirect('login');
	}
}

/* End of file login.php */
/* Location: ./application/controllers/login.php */