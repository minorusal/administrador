<?php
// Include the main TCPDF library (search for installation path).
require_once('tcpdf.php');

// create new PDF document 
$pdf = new TCPDF('P', 'mm', 'letter', true, 'UTF-8', false); #orientation,unit,format,unicode,encoding,diskcache,pdfa

// set document information
$pdf->SetCreator('iSolution.mx');
$pdf->SetAuthor('Oscar Maldonado');
$pdf->SetTitle('Prueba de TCPDF');
$pdf->SetSubject('Creacion de PDF');
$pdf->SetKeywords('PDF, iSolution, O3M');

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set default monospaced font
$pdf->SetDefaultMonospacedFont('courier');

// set margins 
$pdf->SetMargins(5, 5, 5); #left,top,right,keepmargins=false

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 5); #auto,margin_bottom

// set image scale factor
$pdf->setImageScale(1.25); #1.25

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('dejavusans', '', 11, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

// set text shadow effect
$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

// Set some content to print
$html = date('H.i:s').'<h1>Welcome to <a href="http://www.tcpdf.org" style="text-decoration:none;background-color:#CC0000;color:black;">&nbsp;<span style="color:black;">TC</span><span style="color:white;">PDF</span>&nbsp;</a>!</h1>
<i>This is the first example of TCPDF library.</i>
<p>This text is printed using the <i>writeHTMLCell()</i> method but you can also use: <i>Multicell(), writeHTML(), Write(), Cell() and Text()</i>.</p>
<p>Please check the source code documentation and other examples for further information.</p>
<p style="color:#CC0000;">TO IMPROVE AND EXPAND TCPDF I NEED YOUR SUPPORT, PLEASE <a href="http://sourceforge.net/donate/index.php?group_id=128076">MAKE A DONATION!</a></p>';

// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('test.pdf', 'I');
?>