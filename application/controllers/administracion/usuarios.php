<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class usuarios extends Base_Controller { 

	var $uri_modulo     = 'administracion/';
	var $uri_submodulo  = 'usuarios/';
	var $view_content   = 'content';

	public function __construct(){
		parent::__construct();
		$this->load->model($this->uri_modulo.'usuarios_model');
		$this->load->model('administracion/sucursales_model','sucur_model');
		$this->load->model('administracion/empresas_model','empresas_model');
		$this->lang->load("administracion/usuarios","es_ES");
	}
	public function config_tabs(){
		$pagina = (is_numeric($this->uri_segment_end()) ? $this->uri_segment_end() : "");
		$config_tab['names']    = array($this->lang_item("agregar_usuario"), 
										$this->lang_item("listado_usuario"),
										$this->lang_item("detalle_usuario")); 
		$config_tab['links']    = array('administracion/usuarios/agregar_usuario', 
										'administracion/usuarios/listado_usuarios/'.$pagina,
										'detalle_usuario'); 
		$config_tab['action']   = array('load_content',
										'load_content',
										'');
		$config_tab['attr']     = array('','', array('style' => 'display:none'));

		return $config_tab;
	}
	private function uri_view_principal(){
		return $this->uri_modulo.$this->view_content;
	}
	public function index(){
		
		$view_listado_usuarios 	= $this->listado_usuarios();
		$data['titulo_seccion']   = $this->lang_item("usuario");
		$data['titulo_submodulo'] = $this->lang_item("titulo_submodulo");
		$data['icon']             = 'fa fa-users';
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),2,$view_listado_usuarios);
		
		$js['js'][]     = array('name' => 'usuarios', 'dirname' => 'administracion');
		$this->load_view($this->uri_view_principal(), $data, $js);
	}
	/*public function agregar_usuarios(){
		// Listas
		$dropArray = array(
					 'data'		=> $this->ent_model->get_entidades_default()
					,'value' 	=> 'id_administracion_entidad'
					,'text' 	=> array('clave_corta','entidad')
					,'name' 	=> "lts_entidades"
					,'class' 	=> "requerido"
				);
		$dropArray2 = array(
					 'data'		=> $this->sucur_model->db_get_data()
					,'value' 	=> 'id_sucursal'
					,'text' 	=> array('sucursal')
					,'name' 	=> "lts_sucursales"
					,'class' 	=> "requerido"
				);
		$lts_entidades  = dropdown_tpl($dropArray); 
		$lts_sucursales  = dropdown_tpl($dropArray2); 
		$data_1['nombre_cliente'] =	$this->lang_item("nombre_cliente");
		$data_1['rfc'] 			  =	$this->lang_item("rfc_clientes");
		$data_1['razon_social']   =	$this->lang_item("razon_social");
		$data_1['clave_corta'] 	  =	$this->lang_item("clave_corta");
		$data_1['calle']  		  =	$this->lang_item("calle");
		$data_1['num_int'] 		  =	$this->lang_item("num_int");
		$data_1['num_ext'] 	 	  =	$this->lang_item("num_ext");
		$data_1['colonia'] 		  =	$this->lang_item("colonia");
		$data_1['municipio'] 	  =	$this->lang_item("municipio");
		$data_1['entidad'] 		  =	$this->lang_item("entidad");
		$data_1['sucursal'] 	  =	$this->lang_item("sucursal");
		$data_1['cp'] 			  =	$this->lang_item("cp");
		$data_1['telefonos'] 	  =	$this->lang_item("telefonos");
		$data_1['email'] 		  =	$this->lang_item("email");
		$data_1['contacto'] 	  =	$this->lang_item("contacto");
		$data_1['button_save']    = form_button(array('class'=>"btn btn-primary",'name' => 'save_cliente','onclick'=>'insert_cliente()' , 'content' => $this->lang_item("btn_guardar") ));
		$data_1['button_reset']   = form_button(array('class'=>"btn btn-primary",'name' => 'reset','value' => 'reset','onclick'=>'clean_formulario()','content' => $this->lang_item("btn_limpiar")));
		$data_1['dropdown_entidad'] = $lts_entidades;
		$data_1['dropdown_sucursal'] = $lts_sucursales;

		if($this->ajax_post(false)){
			echo json_encode($this->load_view_unique($this->uri_modulo.$this->uri_submodulo.'clientes_save', $data_1, true));
		}else{
			return $this->load_view_unique($this->uri_modulo.$this->uri_submodulo.'clientes_save', $data_1, true);
		}
	}
	*/
	public function listado_usuarios($offset = 0){
		$data_tab_2  = "";
		$filtro      = ($this->ajax_post('filtro')) ? $this->ajax_post('filtro') : "";
		$uri_view    = $this->uri_modulo.'listado';
		$limit       = 5;
		$uri_segment = $this->uri_segment(); 

		$lts_content =$this->usuarios_model->get_usuarios($limit,$offset,$filtro);
		$total_rows  = count($this->usuarios_model->get_usuarios($limit, $offset, $filtro, false));
		$url         = base_url($this->uri_modulo.$this->uri_submodulo.'listado_usuarios');

		$paginador   = $this->pagination_bootstrap->paginator_generate($total_rows, $url, $limit, $uri_segment, array('evento_link' => 'onclick', 'function_js' => 'load_content', 'params_js'=>'1'));

		if($total_rows>0){
			foreach ($lts_content as $value) {
				$atrr = array(
								'href' => '#',
							  	'onclick' => 'detalle_usuario('.$value['id_usuario'].')'
						);


				$tbl_data[] = array('id'      => $value['nombre'],
									'nombre'  => tool_tips_tpl($value['nombre'].' '.$value['paterno'].' '.$value['materno'], $this->lang_item("tool_tip"), 'right' , $atrr),
									'perfil'  => $value['perfil'],
									'pais'    => $value['pais'],
									'empresa' => $value['empresa'],
									'sucursal'=> $value['sucursal']);
			}

			$tbl_plantilla = array ('table_open'  => '<table class="table table-bordered responsive ">');
		
			$this->table->set_heading(	$this->lang_item("nombre"),
										$this->lang_item("nombre"),
										$this->lang_item("perfil"),
										$this->lang_item("pais"),
										$this->lang_item("empresa"),
										$this->lang_item("sucursal"));

			$this->table->set_template($tbl_plantilla);
			$tabla = $this->table->generate($tbl_data);
			/*$buttonTPL = array( 'text'       => $this->lang_item("btn_xlsx"), 
							'iconsweets' => 'iconsweets-excel',
							'href'       => base_url($this->uri_modulo.$this->uri_submodulo.'export_xlsx?filtro='.base64_encode($filtro))
							);*/
		}else{
			$msg   = $this->lang_item("msg_query_null");
			$tabla = alertas_tpl('', $msg ,false);
			//$buttonTPL = "";
		}
		
		/*$buttonIPX = array( 'text'       => $this->lang_item("btn_xlsx"), 
							'iconsweets' => 'iconsweets-excel',
							'href'       => base_url($this->uri_modulo.$this->uri_submodulo.'import_xlsx')
							);*/

		$data_tab_2['filtro']    = ($filtro!="") ? sprintf($this->lang_item("msg_query_search"),$total_rows , $filtro) : "";
		//$data_tab_2['export']    = button_tpl($buttonTPL);
		//$data_tab_2['import']    = button_tpl($buttonIPX);
		$data_tab_2['tabla']     = $tabla;
		$data_tab_2['paginador'] = $paginador;
		$data_tab_2['item_info'] = $this->pagination_bootstrap->showing_items($limit, $offset, $total_rows);

		if($this->ajax_post(false)){
			echo json_encode($this->load_view_unique($uri_view,$data_tab_2,true));
		}else{
			return $this->load_view_unique($uri_view , $data_tab_2, true);
		}
	}
	public function detalle_usuario(){
		$id_usuario       = $this->ajax_post('id_usuario');
		$detalle_usuario  = $this->usuarios_model->get_usuario_unico($id_usuario);
		$uri_view   	  = $this->uri_modulo.$this->uri_submodulo.'usuario_edit';
        $dropArray = array(
					 'data'		 => $this->sucur_model->db_get_data()
					 ,'selected' => $detalle_usuario[0]['id_sucursal']
					,'value' 	 => 'id_sucursal'
					,'text' 	 => array('sucursal')
					,'name' 	 => "lts_sucursales"
					,'class' 	 => "requerido"
				);
		 $dropArray2 = array(
			 'data'		 => $this->empresas_model->db_get_data()
			 ,'selected' => $detalle_usuario[0]['id_empresa']
			,'value' 	 => 'id_empresa'
			,'text' 	 => array('empresa')
			,'name' 	 => "lts_sucursales"
			,'class' 	 => "requerido"
		);         	
		 
		
       	$lts_sucursal              = dropdown_tpl($dropArray);
       	$lts_empresa               = dropdown_tpl($dropArray2);
		$data_tab_3['nombre']      = $this->lang_item("nombre");
		$data_tab_3['paterno']     = $this->lang_item("paterno");
		$data_tab_3['materno'] 	   = $this->lang_item("materno");
		$data_tab_3['perfil']      = $this->lang_item("perfil");
		$data_tab_3['registro'] 	   = $this->lang_item("registro");
		$data_tab_3['telefono']    = $this->lang_item("telefono");
		$data_tab_3['mail'] 	   = $this->lang_item("mail");
		$data_tab_3['pais'] 	   = $this->lang_item("pais");
		$data_tab_3['button_save'] = form_button(array('class'=>"btn btn-primary",'name' => 'update_usuario','onclick'=>'update_usuario()' , 'content' => $this->lang_item("btn_guardar") ));
		//DATA
		$data_tab_3['id_usuario']        = $detalle_usuario[0]['id_usuario'];
		$data_tab_3['nombre_val']        = $detalle_usuario[0]['nombre'];
		$data_tab_3['paterno_val']       = $detalle_usuario[0]['paterno'];
		$data_tab_3['materno_val']       = $detalle_usuario[0]['materno'];
		$data_tab_3['perfil_val']        = $detalle_usuario[0]['perfil'];
		$data_tab_3['registro_val']      = $detalle_usuario[0]['registro'];
		$data_tab_3['telefono_val']      = $detalle_usuario[0]['telefono'];
		$data_tab_3['mail_val']          = $detalle_usuario[0]['mail'];
		$data_tab_3['pais_val']          = $detalle_usuario[0]['pais'];
		$data_tab_3['dropdown_empresa']  = $lts_empresa;
		$data_tab_3['dropdown_sucursal'] = $lts_sucursal;

		//$this->load_database('global_system');
       // $this->load->model('users_model');
    	
    	//$usuario_registro                  = $this->users_model->search_user_for_id($detalle_usuario[0]['id_usuario']);
    	//$usuario_name 				       = text_format_tpl($usuario_registro[0]['name'],"u");
    	//$data_tab_3['val_usuarios_registro']  = $usuario_name ;

    	/*if($detalle_cliente[0]['edit_id_usuario']){
        	$usuario_registro           = $this->users_model->search_user_for_id($detalle_cliente[0]['edit_id_usuario']);
        	$usuario_name 				= text_format_tpl($usuario_registro[0]['name'],"u");
        	$data_tab_3['val_ultima_modificacion']= sprintf($this->lang_item('val_ultima_modificacion', false), $this->timestamp_complete($detalle_cliente[0]['edit_timestamp']), $usuario_name);
    	}else{
    		$usuario_name = '';
    		$data_tab_3['val_ultima_modificacion']= $this->lang_item('lbl_sin_modificacion', false);
    	}*/
    	//$data_tab_3['registro_por']    	= $this->lang_item("registro_por",false);
      	//$data_tab_3['usuario_registro']	= $usuario_name;

		echo json_encode( $this->load_view_unique($uri_view ,$data_tab_3, true));
	}

}