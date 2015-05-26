<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class test extends Base_Controller { 
/**
* Nombre:		test
* Ubicación:	General
* Descripción:	Controlador de pruebas
* @author:		Oscar Maldonado - OM
* Creación: 	2015-05-25
* Modificación:	
*/
	
	public function __construct(){
		parent::__construct();

	}

	public function imprimir_ticket(){
	// Prueba de impresión de ticket directamente a impresora
		// Crea codigo de barras
		$barras_txt = '0123456790123';
		$this->barcode->barcode_img_tipo='png';
		$barras = ($barrasImg = $this->barcode->create($barras_txt))?true:false; #Crea jpg
		$barrasBmp = ($barras)?imagebmp('png',$barrasImg,'assets\tmp\barcode.bmp'):false; #Convierte jpg->bmp
		// envia datos
		$impData = array(
			 'contenido' 	=> 'assets\tmp\ticket.txt'
			,'logo' 		=> 'assets/images/logo.bmp'
			// ,'impresora' 	=> 'PDFCreator'
			,'formato' 		=> true
			,'codebar' 		=> $barrasBmp #envia BMP
			// ,'codeqr' 		=> 'assets/images/qr_isolution.bmp'			
			);		
		// Imprime ticket
		if($this->impresion->enviar_a_impresora($impData)){
			echo "Impresi&oacute;n enviada: ".date('Y-m-d H:i:s');
		}
		// Elimina imagenes
		if($barrasImg) unlink($barrasImg);
		if($barrasBmp) unlink($barrasBmp);
	}

	public function barcode(){
	// Prueba de generacion de código de barras
		$this->barcode->barcode_img_tipo='jpg';
		$this->barcode->create();
	}

	public function qrcode(){
	// Prueba de generacion de código QR
	}
}
?>