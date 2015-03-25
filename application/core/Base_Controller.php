<?php
class Base_Controller extends CI_Controller {
 
    public function __construct() {
        parent::__construct();
    }

    /**
    * Carga la base de datos de acuerdo al pais de origen
    * del usuario (mx,cr,etc)
    * @param string $db
    * @return void
    */
    public function load_database($bd){
    	$load = $this->load->database($bd,TRUE);
    	if(!$load){
    		return true;
    	}
    }

    /**
    * unifica las vistas header & footer con la vista parseada
    * de la seccion seleccionada
    * @param string $view
    * @param array $data
    * @return void
    */
    public function load_view($view, $data){
		$this->load->view('includes/header'); 
		$this->parser->parse($view, $data);
		$this->load->view('includes/footer'); 
	}

    /**
	* imprime un arreglo formateado para debug
	* @return array $array
	*/
	public function print_format($array){
		echo '<pre>';
		print_r($array);
		echo '</pre>';
	}

	/**
	* convierte un objeto a un arreglo
	* @param object $obj
	* @return array
	*/

	public function object_to_array($obj){
		$reaged = (array)$obj;
		foreach($reaged as $key => &$field){
			if(is_object($field))
				$field = $this->object_to_array($field);
		}
		return $reaged;
	}

	/**
	* elimina el cache almacenado
	* @return void
	*/
	public function removeCache(){
        $this->output->set_header('Last-Modified:'.gmdate('D, d M Y H:i:s').'GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header('Cache-Control: post-check=0, pre-check=0',false);
        $this->output->set_header('Pragma: no-cache');
    }

    
}

?>