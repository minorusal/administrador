<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Base_Model extends CI_Model {

	private $vars, $db1,$db2; #dbmodel
	public $tbl; #dbmodel

	public function __construct(){
		parent::__construct();	
		/*INICIO dbmodel*/
		// Crea arreglo $tbl[] con nombre de tablas dentro del archivo dbmodel.cfg
		$this->vars		= new config_vars();
        $this->vars->load_vars('assets/cfg/dbmodel.cfg');
        
		$this->db1                 = $this->vars->db['db1'];
		$this->tbl['claves']       = $this->db1.'.'.$this->vars->db['db1_tbl_claves']; 
		$this->tbl['empresas']     = $this->db1.'.'.$this->vars->db['db1_tbl_empresas']; 
		$this->tbl['modulos']      = $this->db1.'.'.$this->vars->db['db1_tbl_modulos']; 		
		$this->tbl['paises']       = $this->db1.'.'.$this->vars->db['db1_tbl_paises']; 
		$this->tbl['perfiles']     = $this->db1.'.'.$this->vars->db['db1_tbl_perfiles']; 
		$this->tbl['personales']   = $this->db1.'.'.$this->vars->db['db1_tbl_personales']; 
		$this->tbl['secciones']    = $this->db1.'.'.$this->vars->db['db1_tbl_secciones']; 
		$this->tbl['submodulos']   = $this->db1.'.'.$this->vars->db['db1_tbl_submodulos']; 
		$this->tbl['sucursales']   = $this->db1.'.'.$this->vars->db['db1_tbl_sucursales']; 
		$this->tbl['usuarios']     = $this->db1.'.'.$this->vars->db['db1_tbl_usuarios']; 
		$this->tbl['menu1']        = $this->db1.'.'.$this->vars->db['db1_tbl_menu_n1']; 
		$this->tbl['menu2']        = $this->db1.'.'.$this->vars->db['db1_tbl_menu_n2']; 
		$this->tbl['menu3']        = $this->db1.'.'.$this->vars->db['db1_tbl_menu_n3']; 
		$this->tbl['vw_personal']  = $this->db1.'.'.$this->vars->db['db1_vw_personal']; 
    
	}


	public function last_id(){
		return $this->db->insert_id();
	}
	public function row_exist($table, $row, $debug=false){
    	$this->db->select();
		$this->db->from($table);
		$this->db->where($row);
		$query = $this->db->get();
		if($debug){
			print_debug($query->result_array());
		}
		if($query->num_rows >= 1){
			return true;
		}else{
			return false;
		}
    }

    public function enabled_item($table, $clauses){ 	
    	$item  = array('activo' => 0);
		$query = $this->db->update_string($table, $item, $clauses);
		$query = $this->db->query($query);
		return $query;
    }
    public function update_item($tbl, $data, $id_row, $condicion = '') {
    	if(array_key_exists($id_row, $data)){
	    	$route = $this->uri->uri_string();
	    	$log   = array(	 'route'      => $route,
		    				 'type'       => 'UPDATE',
		    				 'tabla'      => $tbl,
		    				 'id_row'     => $data[$id_row],
		    				 'data_row'   => array_2_string_format($data,'=',','),
		    				 'id_usuario' => $data['edit_id_usuario'],
		    				 'timestamp'  => $data['edit_timestamp']
		    			);
	    	$log = $this->db->insert_string($this->tbl['administracion_movimientos'], $log);
	    	$log = $this->db->query($log);
	    	if($log){
	    		$update = $this->db->update_string($tbl, $data, $condicion);
	    		$update = $this->db->query($update);
	    	}else{
	    		$update = false;
	    	}
	    	return $update;
	    }else{
	    	return false;
	    }
    }
    public function insert_item($tbl, $data = array(), $last_id = false){
   		if(isset($data['id_usuario_reg'],$data)){unset($data['id_usuario']);}else{$data['id_usuario'];}
    	$insert  = $this->db->insert_string($tbl, $data);
    	$insert  = $this->db->query($insert);
    	if($insert){
    		$id_row  = $this->db->insert_id();
	    	$route   = $this->uri->uri_string();
	    	$log     = array(	 'route'      => $route,
			    				 'type'       => 'INSERT',
			    				 'tabla'      => $tbl,
			    				 'id_row'     => $id_row,
			    				 'data_row'   => array_2_string_format($data,'=',','),
			    				 'id_usuario' => (isset($data['id_usuario']))?$data['id_usuario']:$data['id_usuario_reg'],
			    				 'timestamp'  => $data['timestamp']
			    			);
	    	$log   = $this->db->insert_string($this->tbl['administracion_movimientos'], $log);
	    	$log   = $this->db->query($log);
    	}else{
    		$insert = false;
    	}
    	if($last_id){
    		return $id_row;
    	}
    	return $insert;
    }
    public function logs(){

    }
}

?>