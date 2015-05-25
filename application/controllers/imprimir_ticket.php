<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class imprimir_ticket extends Base_Controller { 
/**
* Nombre:		Imprime ticket de venta en POS
* Ubicación:	General
* Descripción:	Prueba de impresión
* @author:		Oscar Maldonado - OM
* Creación: 	2015-05-25
* Modificación:	
*/
	
	public function __construct(){
		parent::__construct();

	}

	public function test(){
	// Prueba de impresión
		$impData = array(
			 'contenido' 	=> 'assets\tmp\ticket.txt'
			,'logo' 		=> 'assets/images/logo.bmp'
			// ,'impresora' 	=> 'PDFCreator'
			,'formato' 		=> true
			,'codebar' 		=> '1234567890123'
			,'codeqr' 		=> 'assets/images/qr_isolution.bmp'			
			);
		if($this->impresion->enviar_a_impresora($impData)){
			echo "Impresi&oacute;n enviada: ".date('Y-m-d H:i:s');
		}
	}
}
?>