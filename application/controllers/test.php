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
		$txt = "iSolution.mx";
		$this->barcode->barcode_img_tipo='jpg';
		if($this->barcode->create($txt)){
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
		echo $this->config_vars->load_vars();
	}

	public function directorio(){
	// Lee contenido de directorio
		print_r($this->config_vars->lee_directorio('assets/cfg/'));
	}

	public function html2pdf(){
	// Lee contenido de directorio
		// $html = file_get_contents("assets/tmp/t.html");
		$html = '<table border="1" cellspacing="3" width="100%"><tbody>';
		for($i=0; $i<=50; $i++){
			$html .= '<tr>
						<td>'.$i.'</td>
						<td>'.rand($i,$i*date('s')).'</td>
						<td>'.date('Y-m-d H:i:s').'</td>
						<td>'.md5(str_shuffle('wefFDSFeiwihñygyuuyGu97t')).'</td>
						<td>'.str_shuffle('pi8ybDdtfhjhUg5dtiLJ8FrvIUgRxc').'</td>
					</tr>';
		}
		$html .= '</tbody></table>';
		// dump_var($html);
		$arrayPDF = array(
						 'html' 	=> $html
						,'output'	=> 'F'
						,'archivo' 	=> false
					);
		echo 'Proceso iniciado a las: '.date('Y-m-d H:i:s').'<hr/>';
		ob_start();
		if(!$pdfFile=$this->html2pdf->crear($arrayPDF)){
			echo "Error al crear documento PDF.";
		}else{echo "Archivo Creado a las ".date('Y-m-d H:i:s').' -> '.'<a href="'.$pdfFile['uri'].'">'.$pdfFile['uri'].'</a>';}
		$respuesta = ob_get_contents();
		ob_end_clean();
		echo $respuesta;
		echo '<hr/>Proceso terminado a las: '.date('Y-m-d H:i:s');		
	}


	public function mailing(){

		
	    $config['protocol']  = 'smtp';
	    $config['smtp_host'] = 'ssl://smtp.gmail.com';
	    $config['smtp_port'] = 465;
	    $config['smtp_user'] = 'admcontrol.gcontempo@gmail.com';
	    $config['smtp_pass'] = '@dmincontrol';
	    $config['mailtype']  = 'html';
			
			
		$this->email->initialize($config);

			// Set to, from, message, etc.
		$this->email->from('jorge.martinez@isolution.mx', 'Prueba');
		$list = array(	
						 'jorge.martinez@isolution.mx'
						,'alvaro.enciso@isolution.mx'
						,'oscar.maldonado@isolution.mx'
						,'daniel.salazar@isolution.mx'
					);
		$this->email->to($list);
		//$this->email->reply_to('my-email@gmail.com', 'Explendid Videos');


		$this->email->subject('Correo de Prueba');
		$this->email->message('Probando la clase email');	

		$this->email->send();

		echo $this->email->print_debugger();

	}
}
?>