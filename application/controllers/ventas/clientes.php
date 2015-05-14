<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class clientes extends Base_Controller { 

	var $uri_modulo     = 'ventas/';
	var $uri_submodulo  = 'clientes/';
	var $view_content   = 'content';

	public function __construct(){
		parent::__construct();
		$this->load->model($this->uri_modulo.'clientes_model');
		$this->lang->load("ventas/clientes","es_ES");
	}

	public function config_tabs(){
		$pagina = (is_numeric($this->uri_segment_end()) ? $this->uri_segment_end() : "");
		$config_tab['names']    = array($this->lang_item("agregar_cliente"), 
										$this->lang_item("listado_cliente"),
										$this->lang_item("detalle_cliente")); 
		$config_tab['links']    = array('ventas/clientes/agregar_clientes', 
										'ventas/clientes/listado_clientes/'.$pagina,
										'detalle_cliente'); 
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
		$view_listado_clientes 	= $this->listado_clientes();

		$data['titulo_seccion']   = $this->lang_item("cliente");
		$data['titulo_submodulo'] = $this->lang_item("titulo_submodulo");
		$data['icon']             = 'fa fa-users';
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),2,$view_listado_clientes);
		
		$js['js'][]     = array('name' => 'clientes', 'dirname' => 'ventas');
		$this->load_view($this->uri_view_principal(), $data, $js);
	}

	public function agregar_clientes(){
		$lts_entidades  = dropdown_tpl(		$this->clientes_model->catalogo_entidades(), 
										'' ,
										'id_administracion_entidad', 
										array('clave_corta','entidad'),
										'lts_entidades', 
										'requerido'
									); 
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
		$data_1['cp'] 			  =	$this->lang_item("cp");
		$data_1['telefonos'] 	  =	$this->lang_item("telefonos");
		$data_1['email'] 		  =	$this->lang_item("email");
		$data_1['contacto'] 	  =	$this->lang_item("contacto");
		$data_1['button_save']    = form_button(array('class'=>"btn btn-primary",'name' => 'save_cliente','onclick'=>'insert_cliente()' , 'content' => $this->lang_item("btn_guardar") ));
		$data_1['button_reset']   = form_button(array('class'=>"btn btn-primary",'name' => 'reset','value' => 'reset','onclick'=>'clean_formulario()','content' => $this->lang_item("btn_limpiar")));
		$data_1['dropdown_entidad'] = $lts_entidades;

		if($this->ajax_post(false)){
			echo json_encode($this->load_view_unique($this->uri_modulo.$this->uri_submodulo.'agregar_clientes', $data_1, true));
		}else{
			return $this->load_view_unique($this->uri_modulo.$this->uri_submodulo.'agregar_clientes', $data_1, true);
		}
	}

	public function listado_clientes($offset = 0){
		$data_tab_2  = "";
		$filtro      = ($this->ajax_post('filtro')) ? $this->ajax_post('filtro') : "";
		$uri_view    = $this->uri_modulo.'listado';
		$limit       = 5;
		$uri_segment = $this->uri_segment(); 

		$lts_content =$this->clientes_model->consulta_clientes($limit,$offset,$filtro);
		$total_rows  = count($this->clientes_model->consulta_clientes($limit, $offset, $filtro, false));
		$url         = base_url($this->uri_modulo.$this->uri_submodulo.'listado');

		$paginador   = $this->pagination_bootstrap->paginator_generate($total_rows, $url, $limit, $uri_segment, array('evento_link' => 'onclick', 'function_js' => 'load_content', 'params_js'=>'1'));

		if($total_rows>0){
			foreach ($lts_content as $value) {
				$atrr = array(
								'href' => '#',
							  	'onclick' => 'detalle_articulo('.$value['id_ventas_clientes'].')'
						);

				$tbl_data[] = array('id'              => $value['nombre_cliente'],
									'nombre_cliente'  => tool_tips_tpl($value['nombre_cliente'], $this->lang_item("tool_tip"), 'right' , $atrr),
									'razon_social'    => $value['razon_social'],
									'clave_corta'     => $value['clave_corta'],
									'rfc'  			  => $value['rfc'],
									'telefonos'       => $value['telefonos']);
			}

			$tbl_plantilla = array ('table_open'  => '<table class="table table-bordered responsive ">');
		
			$this->table->set_heading(	$this->lang_item("nombre_cliente"),
										$this->lang_item("nombre_cliente"),
										$this->lang_item("razon_social"),
										$this->lang_item("clave_corta"),
										$this->lang_item("rfc_clientes"),
										$this->lang_item("telefonos"));
			$this->table->set_template($tbl_plantilla);
			$tabla = $this->table->generate($tbl_data);
		}else{
			$msg   = $this->lang_item("msg_query_null");
			$tabla = alertas_tpl('', $msg ,false);
		}
		$data_tab_2['filtro']    = ($filtro!="") ? sprintf($this->lang_item("msg_query_search"),$total_rows , $filtro) : "";
		$data_tab_2['tabla']     = $tabla;
		$data_tab_2['paginador'] = $paginador;
		$data_tab_2['item_info'] = $this->pagination_bootstrap->showing_items($limit, $offset, $total_rows);

		if($this->ajax_post(false)){
			echo json_encode($this->load_view_unique($uri_view,$data_tab_2,true));
		}else{
			return $this->load_view_unique($uri_view , $data_tab_2, true);
		}
	}

	public function insert_cliente(){
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
				$data_insert = array('nombre_cliente' => $this->ajax_post('nombre'),
									 'razon_social'=> $this->ajax_post('razon_social'),
									 'clave_corta'=> $this->ajax_post('clave_corta'),
									 'rfc'=> $this->ajax_post('rfc'),
									 'calle'=> $this->ajax_post('calle'),
									 'num_int'=> $this->ajax_post('num_int'),
									 'num_ext'=> $this->ajax_post('num_ext'),
									 'colonia' => $this->ajax_post('colonia'),
									 'municipio' => $this->ajax_post('municipio'),
									 'entidad' => $this->ajax_post('entidad'),
									 'cp' => $this->ajax_post('cp'),
									 'telefonos' => $this->ajax_post('telefonos'),
									 'email' => $this->ajax_post('email'),
									 'timestamp'  => $this->timestamp(),
									 'activo'  => 1);

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

	public function detalle_cliente(){
		$id_cliente       = $this->ajax_post('id_cliente');
		$detalle_cliente  = $this->clientes_model->get_cliente_unico($id_cliente);
		$lts_entidades  = dropdown_tpl(	$this->clientes_model->catalogo_entidades(), $detalle_cliente[0]['entidad'] ,'id_administracion_entidad', array('clave_corta','entidad'),'lts_entidades', 'requerido'); 
        
        $uri_view   				 = $this->uri_modulo.$this->uri_submodulo.'editar_cliente';
        $data_tab_3['id_cliente']    = $detalle_cliente[0]['id_ventas_clientes'];
		$data_tab_3['nombre_cliente']= $this->lang_item("nombre_cliente");
		$data_tab_3['cliente_value'] = $detalle_cliente[0]['nombre_cliente'];
		$data_tab_3['razon_social']  = $this->lang_item("razon_social");
		$data_tab_3['rs_value']      = $detalle_cliente[0]['razon_social'];
		$data_tab_3['clave_corta'] 	 = $this->lang_item("clave_corta");
		$data_tab_3['clave_value']   = $detalle_cliente[0]['clave_corta'];
		$data_tab_3['rfc']           = $this->lang_item("rfc");
		$data_tab_3['rfc_value']     = $detalle_cliente[0]['rfc'];
		$data_tab_3['calle'] 	     = $this->lang_item("calle");
		$data_tab_3['calle_value']   = $detalle_cliente[0]['calle'];
		$data_tab_3['num_int']  	 = $this->lang_item("num_int");
		$data_tab_3['num_int_value'] = $detalle_cliente[0]['num_int'];
		$data_tab_3['num_ext'] 	 	 = $this->lang_item("num_ext");
		$data_tab_3['num_ext_value'] = $detalle_cliente[0]['num_ext'];
		$data_tab_3['colonia'] 		 = $this->lang_item("colonia");
		$data_tab_3['colonia_value'] = $detalle_cliente[0]['colonia'];
		$data_tab_3['municipio'] 	 = $this->lang_item("municipio");
		$data_tab_3['municipio_value'] = $detalle_cliente[0]['municipio'];
		$data_tab_3['entidad'] 		   = $this->lang_item("entidad");
		$data_tab_3['dropdown_entidad']   = $lts_entidades;
		$data_tab_3['cp'] 			   = $this->lang_item("cp");
		$data_tab_3['cp_value']    	   = $detalle_cliente[0]['cp'];
		$data_tab_3['telefonos'] 	   = $this->lang_item("telefonos");
		$data_tab_3['telefonos_value'] = $detalle_cliente[0]['telefonos'];
		$data_tab_3['email'] 		   = $this->lang_item("email");
		$data_tab_3['email']           = $detalle_cliente[0]['email'];
		$data_tab_3['timestamp'] 	   = $this->lang_item("fecha_registro");
		$data_tab_3['timestamp_value'] = $detalle_cliente[0]['timestamp'];
		$data_tab_3['button_save']     = form_button(array('class'=>"btn btn-primary",'name' => 'update_cliente','onclick'=>'update_cliente()' , 'content' => $this->lang_item("btn_guardar") ));

		echo json_encode( $this->load_view_unique($uri_view ,$data_tab_3, true));
	}

	public function update_cliente(){
		$incomplete  	= $this->ajax_post('incomplete');
		if($incomplete>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			echo json_encode('0|'.alertas_tpl('error', $msg ,false));
		}else{
			$id_cliente  = $this->ajax_post('id_cliente');
			$data_insert = array('nombre_cliente' => $this->ajax_post('nombre'),
							 'razon_social'=> $this->ajax_post('razon_social'),
							 'clave_corta'=> $this->ajax_post('clave_corta'),
							 'rfc'=> $this->ajax_post('rfc'),
							 'calle'=> $this->ajax_post('calle'),
							 'num_int'=> $this->ajax_post('num_int'),
							 'num_ext'=> $this->ajax_post('num_ext'),
							 'colonia' => $this->ajax_post('colonia'),
							 'municipio' => $this->ajax_post('municipio'),
							 'entidad' => $this->ajax_post('entidad'),
							 'cp' => $this->ajax_post('cp'),
							 'telefonos' => $this->ajax_post('telefonos'),
							 'email' => $this->ajax_post('email'));

			$update = $this->clientes_model->update_cliente($data_insert,$id_cliente);
			if($update){
				$msg = $this->lang_item("msg_insert_success",false);
				echo json_encode('1|'.alertas_tpl('success', $msg ,false));
			}else{
				$msg = $this->lang_item("msg_err_clv",false);
				echo json_encode('0|'.alertas_tpl('', $msg ,false));
			}
		}
	}
}