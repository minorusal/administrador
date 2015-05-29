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
		$barras 	= ($barrasImg = $this->barcode->create($barras_txt))?true:false; #Crea jpg
		$barrasBmp 	= ($barras)?imagebmp('png',$barrasImg,'assets\tmp\barcode.bmp'):false; #Convierte jpg->bmp
		// Crea código QR
		$qr_txt 	= 'http://www.isolution.mx';
		$qr 		= ($qrImg = $this->codeqr->create($qr_txt))?true:false;
		$qrBmp 		= ($qr)?imagebmp('png',$qrImg,'assets\tmp\qrcode.bmp'):false; #Convierte jpg->bmp
		// Envía datos
		$impData = array(
			 'contenido' 	=> 'assets\tmp\ticket.txt' #Contenido de ticket
			,'logo' 		=> 'assets/images/logo.bmp' #Logo de empresa en BMP
			,'impresora' 	=> 'PDFCreator' #Nombre de impresora local
			,'formato' 		=> true #Formato de texto
			,'codebar' 		=> $barrasBmp #Envia BMP
			,'codeqr' 		=> $qrBmp #Envía BMP
			);		
		// Imprime ticket
		if($this->impresion->enviar_a_impresora($impData)){
			echo "Impresi&oacute;n enviada: ".date('Y-m-d H:i:s');
		}
		// Elimina imagenes generadas
		if($barrasImg) unlink($barrasImg);
		if($barrasBmp) unlink($barrasBmp);
		if($qrImg) unlink($qrImg);
		if($qrBmp) unlink($qrBmp);
	}

	public function barcode(){
	// Prueba de generacion de código de barras
		$this->barcode->barcode_img_tipo='jpg';
		if($this->barcode->create()){
			echo "Codigo de barras creado: ".date('Y-m-d H:i:s');
		}else{
			echo "No se creo";
		}
	}

	public function codeqr(){
	// Prueba de generacion de código QR
		$txt = "iSolution.mx";
		if($this->codeqr->create($txt)){
			echo "Codigo QR creado: ".date('Y-m-d H:i:s');
		}else{
			echo "No se creo";
		}
	}

	public function load_vars(){
	// Prueba carga de archivo config.ini
		echo $this->config_vars->load_file('assets/cfg/config.ini');
	}
}
?>