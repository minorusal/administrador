<?php
ob_start();
// Texto
// $txt = "
// Linea 1 1 1 1 1  \r\n
// Linea 2 2 2 2 2 \r\n
// ( text to printer text to printer \r\n
// 	text to printer text to printer)
// \r\n
// \r\n
// \r\n
// \r\n
// \r\n
// \r\n";
// // Cut Paper
// $txt .= "\\x00\\x1Bi\\x00";
// $file = "http://localhost/pae/pruebas/printer/test.html";
// $gestor = fopen($file, "rb");
// $contenido = fread($gestor, filesize($file));
// fclose($gestor);
// Conexion a Impresora
// $handle = fopen("\\\\192.168.230.14\\EPSON TM-T20II Receipt", "w");
// fwrite($handle, $Content);
// fclose($handle);
// Archivo
$filename = '1.html';
// $filename = '1.pdf';
if(file_exists($filename)){
    $archivo=fopen($filename, 'rb');
    $contenido = fread($archivo, filesize($filename));
    fclose($archivo);
	// Conexion a Impresora
	$impresora = fopen("\\\\192.168.230.14\\EPSON TM-T20II Receipt", "w");
	fwrite($impresora, $contenido);
	fclose($impresora);
}else{ echo "No exixte: ".$filename;}
// 
echo $contenido;
$script = ob_get_contents();
ob_clean();
echo $script;

?>