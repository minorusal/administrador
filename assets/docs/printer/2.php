<?php

$printer = "\\\\192.168.230.14\\EPSON TM-T20II Receipt"; 
// $printer = "PDFCreator"; 
$filename = 'ticket.txt';
// $filename = '1.pdf';
// ob_start();
// header("Content-type: application/pdf");
// readfile($filename); 
// $file = ob_get_contents();
// ob_clean();
// echo $file;


if($ph = printer_open($printer)){ 
   // Get file contents 
   $fh = fopen($filename, "rb"); 
   $content = fread($fh, filesize($filename)); 
   fclose($fh); 
	// Inicio de doc
	printer_start_doc($ph);
	printer_start_page($ph);

	// Set print mode to RAW and send PDF to printer 
	printer_set_option($ph, PRINTER_MODE, "RAW"); 
	// Fuente
	$arial = printer_create_font("Arial", 148, 76, PRINTER_FW_MEDIUM, false, false, false, 0);    
	printer_select_font($ph, $arial);
	// Imagen
	printer_draw_bmp($ph, "logo.bmp", 50, 50, 200, 50);
	// Contenido
	printer_write($ph, $content); 
	printer_end_page($ph);
	printer_end_doc($ph);
	printer_close($ph); 
} 
else "Couldn't connect..."; 
echo $content;
?>