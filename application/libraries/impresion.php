<?php
/**
* Descripción: 	Contruye y envía un documento a la impresora 8solo funciona en Windows)
* @author:		Oscar Maldonado - O3M
* Creación: 	2015-05-22
* Modificación: 2015-05-25
*/
class impresion {

	private $respuesta; #Respuesta
	private $impresora; #Nombre de impresora
	private $contenido, $texto, $formato; #Contenido de documento
	private $fuente, $font_barras; #Formato de texto
	private $logo, $codebar, $codeqr; # Imagenes
	private $fh, $ph; #Manejadores de Impresora y archivo

	public function __construct(){
		$this->impresora 	= "\\\\192.168.230.14\\EPSON TM-T20II Receipt";
		// $this->logo 	 	= "assets/images/logo.bmp";
		$this->fuente 		= "Arial";
		$this->font_barras	= 'Code 128';
		// $this->font_barras	= 'Free 3 of 9';
		$this->respuesta 	= false;
		$this->formato 		= false;
		$this->contenido 	= '';
		$this->codebar 		= false;
		$this->codeqr 		= false;
	}

	// function impresoras($id=false){
	// // Listado de impresoras disponibles
	// 	switch ($id) {
	// 		case 1:	 $this->impresora = "\\\\192.168.230.14\\EPSON TM-T20II Receipt"; break;
	// 		case 2:	 $this->impresora = "PDFCreator"; break;
	// 		default: $this->impresora = "PDFCreator"; break;
	// 	}
	// 	return $this->impresora;
	// }

	public function enviar_a_impresora($data=array()){
	// Envia documento a impresora via printer.dll (Windows)
		// $this->impresora 	= (isset($data['impresora']))?$this->impresoras($data['impresora']):$this->impresoras();
		$this->impresora 	= (isset($data['impresora']))?$data['impresora']:$this->impresora;
		$this->logo 	 	= (isset($data['logo']))?$data['logo']:"assets/images/logo.bmp";
		$this->contenido 	= (isset($data['contenido']))?$data['contenido']:'';
		$this->formato 		= (isset($data['formato']))?$data['formato']:'';
		$this->codebar 		= (isset($data['codebar']))?$data['codebar']:'';
		$this->codeqr 		= (isset($data['codeqr']))?$data['codeqr']:'';

		if($this->formato){
			// Lee archivo y extrae contenido para enviarlo a la impresora
			$this->fh = fopen($this->contenido, "r");
			while(!feof($this->fh)) {
				$this->texto [] = fgets($this->fh);
			}
			fclose($this->fh);
			$isfile = true;
		}else{
			$this->texto = $this->contenido;
		}

		if($this->impresora && $this->texto){
			if($this->ph = printer_open($this->impresora)){ #Crea manejador de impresora			
				#Contrucción de documento en impresora
				// Inicio de doc y page
				printer_start_doc($this->ph);
				printer_start_page($this->ph);
				// Set print mode to RAW and send PDF to printer 
				printer_set_option($this->ph, PRINTER_MODE, "RAW"); 
				if($this->formato){
					// Imagen BMP - Logo
					printer_draw_bmp($this->ph, $this->logo, 155, 0, 300, 70);
					// Crear formato de texto
					$estilo = printer_create_font($this->fuente, 25, 11, 500, false, false, false, 0);    
					printer_select_font($this->ph, $estilo);
					// Contenido
					$salto_linea = 80;
					if($isfile){
						$txtArray = $this->texto;
						for($i=0; $i<count($txtArray); $i++){
							printer_draw_text($this->ph, $txtArray[$i], 10, $salto_linea); #Izq; Alto
							$salto_linea+=20;
						}					
					}else{						
						printer_draw_text($this->ph, $this->texto, 10, $salto_linea);						
					}
					// Eliminar formato de texto
					printer_delete_font($estilo);
					// Codigo de Barras
					if($this->codebar){
						// printer_draw_bmp($this->ph, $this->logo, 10, $salto_linea, 300, 70);
						$barras = printer_create_font($this->font_barras, 100, 30, 500, false, false, false, 0);	
						printer_select_font($this->ph, $barras);
						printer_draw_text($this->ph, $this->codebar, 100, $salto_linea);
						printer_delete_font($barras);
						$salto_linea+=110;
					}
					// Codigo QR
					if($this->codeqr){
						printer_draw_bmp($this->ph, $this->codeqr, 190, $salto_linea, 200, 200);
						$salto_linea+=210;						
					}
					// Footer
					$estilo = printer_create_font($this->fuente, 17, 8, 500, false, false, false, 0);    
					printer_select_font($this->ph, $estilo);
					printer_draw_text($this->ph, date('Y-m-d H:i:s'), 204, $salto_linea);
					printer_delete_font($estilo);
				}else{
					printer_write($this->ph, $this->texto); #texto sin formato
				}				
				// Fin de documento y página
				printer_end_page($this->ph);
				printer_end_doc($this->ph);
				// Cierra archivo y envía a imprimir
				printer_close($this->ph); 
				$this->respuesta = true;
			}
		}
		return $this->respuesta;
	}
}

#EJEMPLO
// $impData = array(
// 	 'contenido' 	=> 'assets\tmp\ticket.txt'
// 	// ,'impresora' 	=> 'PDFCreator'
// 	,'formato' 		=> true
// 	,'codebar' 		=> '1234567890123'
// 	);
// $p = new Impresion();
// if($p->enviar_a_impresora($impData)){
// 	echo "Impresi&oacute;n enviada: ".date('Y-m-d H:i:s');
// }
/*-O3M-*/
?>