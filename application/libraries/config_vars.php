<?php /*O3M*/
/**
* Descripción:	Parsea archivo con datos de configuración incial y los
*				pone disponibles en variables globales.
* Creación:		2015-05-29
* @author		Oscar Maldonado - O3M
* @param 		$filename
* @return 		$cfg[], $db[], $var[]
*/
class config_vars {
	public $cfg, $db, $var;
	public function load_vars($filename='assets/cfg/config.ini') {
	#Carga información de configuración de un archivo config.ini.
		try {			
			if (file_exists($filename)) {
		        if ($handle = fopen($filename, 'r')) {
		        	$varsList = array('cfg','var','db');
		        	$val = '';
		            while (!feof($handle)) {
		                @list($type, $name, $value) = preg_split("/\||=/", fgets($handle), 3);                              
						$value = utf8_encode($value);
						if (trim($type)=='cfg') { 
						#CFG vars
							$this->cfg[trim($name)] = trim($value);
						}
						if (trim($type)=='var') { 
						#VAR vars
							$this->var[trim($name)] = trim($value);
						}
						if (trim($type)=='db') { 
						#DB vars
							$this->db[trim($name)] = trim($value);					
						}				
						if (in_array(trim($type),$varsList)) { 
						#Print for Debug
						 	$val.=$type.' | '.$name.' = '.$value."<br/>\n\r";
						}
		            }	            
		        }	        
				return $val;
			}else{
				$msj = "¡ERROR CRÍTICO!<br/> No se ha logrado cargar el archivo de configuración, por favor, contacte al administrador del sistema.<br/>";
		    	throw new Exception($msj, 1);    	
		    }	
		} catch (Exception $e) {		
			print($e->getMessage());
			return false;
		}	   
	}
}
// config_vars::load_file();
/*O3M*/
?>