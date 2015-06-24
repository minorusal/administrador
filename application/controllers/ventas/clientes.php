<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class clientes extends Base_Controller { 

	var $uri_modulo     = 'ventas/';
	var $uri_submodulo  = 'clientes/';
	var $view_content   = 'content';

	public function __construct(){
		parent::__construct();
		$this->load->model($this->uri_modulo.'clientes_model');
		$this->load->model('administracion/entidades_model','ent_model');
		$this->load->model('administracion/sucursales_model','sucur_model');
		$this->lang->load("ventas/clientes","es_ES");
	}
	public function config_tabs(){
		$pagina = (is_numeric($this->uri_segment_end()) ? $this->uri_segment_end() : "");
		$config_tab['names']    = array($this->lang_item("agregar_cliente"), 
										$this->lang_item("listado_cliente"),
										$this->lang_item("detalle_cliente")); 
		$config_tab['links']    = array('ventas/clientes/agregar', 
										'ventas/clientes/listado/'.$pagina,
										'detalle'); 
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
		$view_listado 	= $this->listado();

		$data['titulo_seccion']   = $this->lang_item("cliente");
		$data['titulo_submodulo'] = $this->lang_item("titulo_submodulo");
		$data['icon']             = 'fa fa-users';
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),2,$view_listado);
		
		$js['js'][]     = array('name' => 'clientes', 'dirname' => 'ventas');
		$this->load_view($this->uri_view_principal(), $data, $js);
	}
	public function agregar(){
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
		$data_1['apellido_paterno'] =	$this->lang_item("apellido_paterno");
		$data_1['apellido_materno'] =	$this->lang_item("apellido_materno");
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
		$data_1['button_save']    = form_button(array('class'=>"btn btn-primary",'name' => 'save_cliente','onclick'=>'insert()' , 'content' => $this->lang_item("btn_guardar") ));
		$data_1['button_reset']   = form_button(array('class'=>"btn btn-primary",'name' => 'reset','value' => 'reset','onclick'=>'clean_formulario()','content' => $this->lang_item("btn_limpiar")));
		$data_1['dropdown_entidad'] = $lts_entidades;
		$data_1['dropdown_sucursal'] = $lts_sucursales;

		if($this->ajax_post(false)){
			echo json_encode($this->load_view_unique($this->uri_modulo.$this->uri_submodulo.'clientes_save', $data_1, true));
		}else{
			return $this->load_view_unique($this->uri_modulo.$this->uri_submodulo.'clientes_save', $data_1, true);
		}
	}
	public function listado($offset = 0){
		$data_tab_2  = "";
		$filtro      = ($this->ajax_post('filtro')) ? $this->ajax_post('filtro') : "";
		$uri_view    = $this->uri_modulo.'listado';
		$limit       = 10;
		$uri_segment = $this->uri_segment(); 

		$lts_content =$this->clientes_model->consulta_clientes($limit,$offset,$filtro);
		$total_rows  = count($this->clientes_model->consulta_clientes($limit, $offset, $filtro, false));
		$url         = base_url($this->uri_modulo.$this->uri_submodulo.'listado');

		$paginador   = $this->pagination_bootstrap->paginator_generate($total_rows, $url, $limit, $uri_segment, array('evento_link' => 'onclick', 'function_js' => 'load_content', 'params_js'=>'1'));

		if($total_rows>0){
			foreach ($lts_content as $value) {
				$atrr = array(
								'href' => '#',
							  	'onclick' => 'detalle('.$value['id_ventas_clientes'].')'
						);

				$tbl_data[] = array('id'              => $value['nombre'],
									'nombre_cliente'  => tool_tips_tpl($value['nombre'].' '.$value['paterno'].' '.$value['materno'], $this->lang_item("tool_tip"), 'right' , $atrr),
									'razon_social'    => $value['razon_social'],
									'clave_corta'     => $value['clave_corta'],
									'rfc'  			  => $value['rfc'],
									'telefonos'       => $value['telefonos'],
									'id_entidad'      => $value['entidad'],
									'id_sucursal'     => $value['sucursal']);
			}

			$tbl_plantilla = set_table_tpl();
		
			$this->table->set_heading(	$this->lang_item("nombre_cliente"),
										$this->lang_item("nombre_cliente"),
										$this->lang_item("razon_social"),
										$this->lang_item("clave_corta"),
										$this->lang_item("rfc_clientes"),
										$this->lang_item("telefonos"),
										$this->lang_item("entidad"),
										$this->lang_item("sucursal"));
			$this->table->set_template($tbl_plantilla);
			$tabla = $this->table->generate($tbl_data);
			$buttonTPL = array( 'text'       => $this->lang_item("btn_xlsx"), 
							'iconsweets' => 'iconsweets-excel',
							'href'       => base_url($this->uri_modulo.$this->uri_submodulo.'export_xlsx?filtro='.base64_encode($filtro))
							);
		}else{
			$msg   = $this->lang_item("msg_query_null");
			$tabla = alertas_tpl('', $msg ,false);
			$buttonTPL = "";
		}
		
		/*$buttonIPX = array( 'text'       => $this->lang_item("btn_xlsx"), 
							'iconsweets' => 'iconsweets-excel',
							'href'       => base_url($this->uri_modulo.$this->uri_submodulo.'import_xlsx')
							);*/

		$data_tab_2['filtro']    = ($filtro!="") ? sprintf($this->lang_item("msg_query_search"),$total_rows , $filtro) : "";
		$data_tab_2['export']    = button_tpl($buttonTPL);
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
	public function insert(){
		$incomplete  	= $this->ajax_post('incomplete');
		if($incomplete>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			echo json_encode('0|'.alertas_tpl('error', $msg ,false));
		}else{
			$clave_corta= $this->ajax_post('clave_corta');
			$existe = count($this->clientes_model->get_existencia_cliente($clave_corta));
			
			if($existe>0){
				$msg = $this->lang_item("msg_err_clv",false);
				echo json_encode('0|'.alertas_tpl('', $msg ,false));
			}else{
				$data_insert = array('nombre' 		  => $this->ajax_post('nombre'),
									 'paterno'	  	  => $this->ajax_post('paterno'),
									 'materno'	  	  => $this->ajax_post('materno'),
									 'razon_social'	  => $this->ajax_post('razon_social'),
									 'clave_corta'	  => $this->ajax_post('clave_corta'),
									 'rfc'	          => $this->ajax_post('rfc'),
									 'calle'	      => $this->ajax_post('calle'),
									 'num_int'		  => $this->ajax_post('num_int'),
									 'num_ext'		  => $this->ajax_post('num_ext'),
									 'colonia' 		  => $this->ajax_post('colonia'),
									 'municipio' 	  => $this->ajax_post('municipio'),
									 'id_entidad' 	  => $this->ajax_post('id_entidad'),
									 'id_sucursal' 	  => $this->ajax_post('id_sucursal'),
									 'cp' 			  => $this->ajax_post('cp'),
									 'telefonos' 	  => $this->ajax_post('telefonos'),
									 'email' 		  => $this->ajax_post('email'),
									 'timestamp'  	  => $this->timestamp(),
									 'id_usuario'     => $this->session->userdata('id_usuario'),
									 'activo'         => 1);

				$insert = $this->clientes_model->insert_cliente($data_insert);

				if($insert){
					$msg = $this->lang_item("msg_insert_success",false);
					echo json_encode('1|'.alertas_tpl('success', $msg ,false));
				}else{
					$msg = $this->lang_item("msg_err_clv",false);
					echo json_encode('0|'.alertas_tpl('', $msg ,false));
				}
			}
			
		}
	}
	public function detalle(){
		$id_cliente       = $this->ajax_post('id_cliente');
		$detalle  = $this->clientes_model->get_cliente_unico($id_cliente);
		$uri_view   	  = $this->uri_modulo.$this->uri_submodulo.'cliente_edit';
		// Listas
		$dropArray = array(
					 'data'		 => $this->ent_model->get_entidades_default()
					 ,'selected' => $detalle[0]['id_entidad']
					,'value' 	 => 'id_administracion_entidad'
					,'text' 	 => array('clave_corta','entidad')
					,'name' 	 => "lts_entidades"
					,'class' 	 => "requerido"
				);
        $dropArray2 = array(
					 'data'		=> $this->sucur_model->db_get_data()
					 ,'selected' => $detalle[0]['id_sucursal']
					,'value' 	=> 'id_sucursal'
					,'text' 	=> array('sucursal')
					,'name' 	=> "lts_sucursales"
					,'class' 	=> "requerido"
				);
       	$lts_entidades  = dropdown_tpl($dropArray);
       	$lts_sucursal   = dropdown_tpl($dropArray2);

		$data_tab_3['nombre_cliente']  		   = $this->lang_item("nombre_cliente");
		$data_tab_3['apellido_paterno']		   = $this->lang_item("apellido_paterno");
		$data_tab_3['apellido_materno']		   = $this->lang_item("apellido_materno");
		$data_tab_3['razon_social']    		   = $this->lang_item("razon_social");
		$data_tab_3['clave_corta'] 	   		   = $this->lang_item("clave_corta");
		$data_tab_3['rfc']             		   = $this->lang_item("rfc");
		$data_tab_3['calle'] 	       		   = $this->lang_item("calle");
		$data_tab_3['num_int']  	   		   = $this->lang_item("num_int");
		$data_tab_3['num_ext'] 	 	   		   = $this->lang_item("num_ext");
		$data_tab_3['colonia'] 		   		   = $this->lang_item("colonia");
		$data_tab_3['municipio'] 	   		   = $this->lang_item("municipio");
		$data_tab_3['entidad'] 		   		   = $this->lang_item("entidad");
		$data_tab_3['sucursal'] 	   		   = $this->lang_item("sucursal");
		$data_tab_3['cp'] 			   		   = $this->lang_item("cp");
		$data_tab_3['telefonos'] 	   		   = $this->lang_item("telefonos");
		$data_tab_3['email'] 		   		   = $this->lang_item("email");
		$data_tab_3['timestamp'] 	   		   = $this->lang_item("fecha_registro");
		$data_tab_3['lbl_ultima_modificacion'] = $this->lang_item('lbl_ultima_modificacion', false);
		$data_tab_3['button_save']     		   = form_button(array('class'=>"btn btn-primary",'name' => 'update_cliente','onclick'=>'update()' , 'content' => $this->lang_item("btn_guardar") ));
		//DATA
		$data_tab_3['id_cliente']    		   = $detalle[0]['id_ventas_clientes'];
		$data_tab_3['cliente_value'] 		   = $detalle[0]['nombre'];
		$data_tab_3['paterno_value'] 		   = $detalle[0]['paterno'];
		$data_tab_3['materno_value'] 		   = $detalle[0]['materno'];
		$data_tab_3['rs_value']      		   = $detalle[0]['razon_social'];
		$data_tab_3['clave_value']   		   = $detalle[0]['clave_corta'];
		$data_tab_3['rfc_value']     		   = $detalle[0]['rfc'];
		$data_tab_3['calle_value']   		   = $detalle[0]['calle'];
		$data_tab_3['num_int_value'] 		   = $detalle[0]['num_int'];
		$data_tab_3['num_ext_value'] 		   = $detalle[0]['num_ext'];
		$data_tab_3['colonia_value'] 		   = $detalle[0]['colonia'];
		$data_tab_3['municipio_value'] 		   = $detalle[0]['municipio'];
		$data_tab_3['dropdown_entidad']   	   = $lts_entidades;
		$data_tab_3['dropdown_sucursal']   	   = $lts_sucursal;
		$data_tab_3['cp_value']    	   		   = $detalle[0]['cp'];
		$data_tab_3['telefonos_value'] 		   = $detalle[0]['telefonos'];
		$data_tab_3['val_email']           	   = $detalle[0]['email'];
		$data_tab_3['timestamp_value'] 		   = $detalle[0]['timestamp'];

		$this->load_database('global_system');
        $this->load->model('users_model');
    	
    	$usuario_registro                  = $this->users_model->search_user_for_id($detalle[0]['id_usuario']);
    	$usuario_name 				       = text_format_tpl($usuario_registro[0]['name'],"u");
    	$data_tab_3['val_usuarios_registro']  = $usuario_name ;

    	if($detalle[0]['edit_id_usuario']){
        	$usuario_registro           = $this->users_model->search_user_for_id($detalle[0]['edit_id_usuario']);
        	$usuario_name 				= text_format_tpl($usuario_registro[0]['name'],"u");
        	$data_tab_3['val_ultima_modificacion']= sprintf($this->lang_item('val_ultima_modificacion', false), $this->timestamp_complete($detalle[0]['edit_timestamp']), $usuario_name);
    	}else{
    		$usuario_name = '';
    		$data_tab_3['val_ultima_modificacion']= $this->lang_item('lbl_sin_modificacion', false);
    	}
    	$data_tab_3['registro_por']    	= $this->lang_item("registro_por",false);
      	$data_tab_3['usuario_registro']	= $usuario_name;

		echo json_encode( $this->load_view_unique($uri_view ,$data_tab_3, true));
	}
	public function update(){
		$incomplete  	= $this->ajax_post('incomplete');
		if($incomplete>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			echo json_encode('0|'.alertas_tpl('error', $msg ,false));
		}else{
			$id_cliente  = $this->ajax_post('id_cliente');
			$data_insert = array('nombre' => $this->ajax_post('nombre'),
							 'paterno'=> $this->ajax_post('paterno'),
							 'materno'=> $this->ajax_post('materno'),
							 'razon_social'=> $this->ajax_post('razon_social'),
							 'clave_corta'=> $this->ajax_post('clave_corta'),
							 'rfc'=> $this->ajax_post('rfc'),
							 'calle'=> $this->ajax_post('calle'),
							 'num_int'=> $this->ajax_post('num_int'),
							 'num_ext'=> $this->ajax_post('num_ext'),
							 'colonia' => $this->ajax_post('colonia'),
							 'municipio' => $this->ajax_post('municipio'),
							 'id_entidad' => $this->ajax_post('id_entidad'),
							 'id_sucursal' => $this->ajax_post('id_sucursal'),
							 'cp' => $this->ajax_post('cp'),
							 'telefonos' => $this->ajax_post('telefonos'),
							 'email' => $this->ajax_post('email'),
							 'edit_timestamp'  	  => $this->timestamp(),
							 'edit_id_usuario'     => $this->session->userdata('id_usuario'));

			$update = $this->clientes_model->update_cliente($data_insert,$id_cliente);
			if($update){
				$msg = $this->lang_item("msg_update_success",false);
				echo json_encode('1|'.alertas_tpl('success', $msg ,false));
			}else{
				$msg = $this->lang_item("msg_err_clv",false);
				echo json_encode('0|'.alertas_tpl('', $msg ,false));
			}
		}
	}
	public function export_xlsx(){
		$filtro      = ($this->ajax_get('filtro')) ?  base64_decode($this->ajax_get('filtro') ): "";

		$lts_content = $this->clientes_model->consulta_clientes('', '', $filtro , false);
	
		if(count($lts_content)>0){
			foreach ($lts_content as $value) {
				$set_data[] = array(
									$value['id_ventas_clientes'],
									 $value['nombre'].' '.$value['paterno'].' '.$value['materno'],
									 $value['razon_social'],
									 $value['clave_corta'],
									 $value['rfc'],
									 $value['calle'],
									 $value['num_int'],
									 $value['num_ext'],
									 $value['colonia'],
									 $value['municipio'],
									 $value['entidad'],
									 $value['sucursal'],
									 $value['cp'],
									 $value['telefonos'],
									 $value['email'],
									 $value['timestamp']);
			}
			$set_heading = array(
									$this->lang_item("ID"),
									$this->lang_item("nombre_cliente"),
									$this->lang_item("razon_social"),
									$this->lang_item("clave_corta"),
									$this->lang_item("rfc"),
									$this->lang_item("calle"),
									$this->lang_item("num_int"),
									$this->lang_item("num_ext"),
									$this->lang_item("colonia"),
									$this->lang_item("municipio"),
									$this->lang_item("entidad"),
									$this->lang_item("sucursal"),
									$this->lang_item("cp"),
									$this->lang_item("telefonos"),
									$this->lang_item("email"),
									$this->lang_item("fecha_registro"));
		}

		$params = array(	'title'  => $this->lang_item("seccion"),
							'items'   => $set_data,
							'headers' => $set_heading
						);
		$this->excel->generate_xlsx($params);		
	}
	/*public function import_xlsx(){
		$ex=$this->excel->test();
		dump_var($ex);
	}*/
}