<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH.'third_party/PHPExcel/PHPExcel.php');

class excel extends PHPExcel{
	
	public function __construct(){
		parent::__construct();
	}

	public function generate_excel(){
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("IS Intelligent Solution")
								->setLastModifiedBy("IS Intelligent Solution")
								->setTitle("Office 2007 XLSX Test Document")
								->setSubject("Office 2007 XLSX Test Document")
								->setDescription("Office 2007 XLSX Document")
								->setKeywords("office 2007 openxml");

		$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Hello')
            ->setCellValue('B2', 'world!')
            ->setCellValue('C1', 'Hello')
            ->setCellValue('D2', 'world!');

		// Miscellaneous glyphs, UTF-8
		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A4', 'Miscellaneous glyphs')
		            ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');

		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Simple');


		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);

		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="01simple.xlsx"');
	

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;

	}
}