<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	public function index(){
		$this->removeCache();
		if($this->session->userdata('is_logged')){
			redirect('inicio');
        }else{
        	$this->load->view('login');	
        }
	}

	public function __encript_pwd($pwd){
		return md5($pwd);
	}

	function authentication(){	
		$this->load->model('Login_model');
		$id_user    = $this->input->post('id_user');
		if($id_user==''){
			$user   = $this->input->post('user');
			$pwd    = $this->__encript_pwd($this->input->post('pwd'));
			$data   = $this->Login_model->validate_user($user, $pwd);
		}else{
			$data   = $this->Login_model->search_user_for_id($id_user);
		}
		$data       = $this->query_result_to_array($this->object_to_array($data));
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
								'avatar'      => $value['avatar'],
								'id_empresa'  => $value['id_empresa'],
								'empresa'     => $value['empresa'],
								'id_sucursal' => $value['id_sucursal'],
								'sucursal'    => $value['sucursal'],
								'id_nivel'    => $value['id_nivel'],
								'nivel'       => $value['nivel'],
								'registro'    => $value['registro'],
								'activo'      => $value['activo'],
								'user'        => $value['user'],
								'is_logged'   => true
						);
			}
		}
		return $data;
	}

	function tbl_multiples_perfiles($data){
		$count = 0;
		foreach ($data as $value) {
			# code...
			$count++;
			$avatar    = array('data' => '<img src='.base_url().'assets/'.$value['avatar'].' />');
			$name_user = $value['name'];
			$attr      = array(
								'id'      => $count,
	                            'name'    => 'perfil_ingreso',
	                            'type'    => 'radio',
	                            'value'   => $value['id_usuario'],
	                            'checked' => ($count==1) ? 'true' : 'false'
	                        );
			$tbl_data[] = array(
								'id'     => form_input($attr),
								'name'   => strtoupper($value['name']),
								'nivel'  => $value['nivel']
						);
		}

		$tbl_plantilla = array ('table_open'  => '<table class="table table-bordered responsive dataTable">');
		$this->table->set_heading('Opc.', 'Nombre', 'Perfil');
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
	function redireccion(){
		$is_logged = $this->input->post('is_logged');
		if($is_logged=='1'){
			redirect('inicio');
		}else{
			$data['message_error'] = TRUE;
			$this->load->view('login', $data);
		}
	}
	function logout(){
		$this->removeCache();
		$this->session->sess_destroy();
		redirect('login');
	}
	
	
}

/* End of file login.php */
/* Location: ./application/controllers/login.php */