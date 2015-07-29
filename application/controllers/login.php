<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
ini_set('memory_limit', '1024M');
class login extends Base_Controller {
	public function __construct(){
		parent::__construct();
        $this->lang_load("login");
	}
	/**
    * Index del Controllador
    * @return void
    */
	public function index(){
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
		$this->load->model('users_model');
		$id_user    = $this->input->post('id_user');
		if($id_user==''){
			$user   = $this->input->post('user');
			$pwd    = $this->__encript_pwd($this->input->post('pwd'));
			$data   = $this->users_model->search_user_for_login($user, $pwd);			
		}else{
			$data   = $this->users_model->search_user_for_id($id_user);
		}
		$data       = $this->query_result_to_array($data);
		$data_count = count($data);
		
		if((is_array($data))&&(!empty($data))){
			if($data_count>1){
				echo json_encode($this->tbl_multiples_perfiles($data));
			}else{
				$this->session->set_userdata($data[0]);
				echo 1;
			}
		}else{
			echo 0;
		}
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
								'moneda' 	  => $value['moneda'],
								'id_empresa'  => $value['id_empresa'],
								'empresa'     => $value['empresa'],
								'id_sucursal' => $value['id_sucursal'],
								'sucursal'    => $value['sucursal'],
								'id_perfil'   => $value['id_perfil'],
								'perfil'      => $value['perfil'],
								'id_menu_n1'  => trim($value['id_menu_n1'],',').trim($value['user_id_menu_n1'],','),
								'id_menu_n2'  => trim($value['id_menu_n2'],',').trim($value['user_id_menu_n2'],','),
								'id_menu_n3'  => trim($value['id_menu_n3'],',').trim($value['user_id_menu_n3'],','),
								'timestamp'    => $value['timestamp'],
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
			$img_path ='./assets/avatar/users/';
			$avatar_image = $img_path.$value['avatar_user'];

			$avatar_foto = (file_exists($avatar_image)) ? $avatar_image : $img_path.'sin_foto.png';

						$avatar    = array('data' => '<img src='.$avatar_foto.' style="max-width:80px;max-height:90px;" />');
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

		$tbl_plantilla = set_table_tpl();
		$this->table->set_heading($this->lang_item('row_pais'), $this->lang_item('row_perfil'));
		$this->table->set_template($tbl_plantilla);

		$tbl_info[] = array(
								'image'  => $avatar,
								'msg'    => $this->lang_item('modal_msg',false)
						);
		$perfiles = $this->table->generate($tbl_data);
		
		$this->table->set_heading($this->lang_item('modal_saludo'),$name_user);
		$this->table->set_template($tbl_plantilla);
		$info = $this->table->generate($tbl_info);

		return $info.$perfiles;

	}

	/**
	* Finalizar la sesion activa
	* @return void
	*/
	function logout(){
		$this->load_database('global_system');
		$this->session->sess_destroy();
		redirect('login');
	}
}

/* End of file login.php */
/* Location: ./application/controllers/login.php */