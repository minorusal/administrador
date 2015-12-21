<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class edit_profile extends Base_Controller {
	public function __construct(){
		parent::__construct();
        $this->lang_load("edit_profile");
	}
	public function index(){

		$nombre      = $this->session->userdata('nombre');
		$paterno     = $this->session->userdata('paterno');
		$materno     = $this->session->userdata('materno');
		$telefono    = $this->session->userdata('telefono');
		$mail        = $this->session->userdata('mail');
		$avatar_user = $this->session->userdata('avatar_user');

		$data['icon']           = 'iconfa-user';
		$data['titulo_modulo']  = $this->lang_item('Admincontrol', false);;
		$data['titulo_seccion'] = $this->lang_item('edit_profile', false);;
		$data['lbl_nombre']     = $this->lang_item('lbl_nombre', false);
		$data['lbl_paterno']    = $this->lang_item('lbl_paterno', false);
		$data['lbl_materno']    = $this->lang_item('lbl_materno', false);
		$data['lbl_telefono']   = $this->lang_item('lbl_telefono', false);
		$data['lbl_email']      = $this->lang_item('lbl_email', false);
		$data['val_nombre']     = $nombre;
		$data['val_paterno']    = $paterno;
		$data['val_materno']    = $materno;
		$data['val_mail']       = $mail;
		$data['val_telefono']   = $telefono;

		$this->load_view('edit_profile', $data);
	}

}
