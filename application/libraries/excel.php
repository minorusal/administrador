<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH.'third_party/PHPExcel/PHPExcel.php');

class excel extends PHPExcel{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function generate_xlsx($params = array(), $debug = false){
		
		$title   = (array_key_exists('title',$params)) ? $params['title'] : 'IS_XLSX';
		$headers = (array_key_exists('headers',$params)) ? $params['headers'] : false;
		$items   = (array_key_exists('items',$params)) ? $params['items'] : false;

		if($headers && $items){
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("IS Intelligent Solution")
									->setLastModifiedBy("IS Intelligent Solution")
									->setTitle($title)
									->setSubject($title)
									->setDescription($title)
									->setKeywords("office 2007 openxml");

			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Logo');
			$objDrawing->setDescription('Logo');
			$objDrawing->setPath('./assets/images/logo.png');
			$objDrawing->setHeight(36);
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

			
			$countHeaders = count($params['headers'])+64;
			$column       = chr($countHeaders).'3';

			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setName('Candara');
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(22);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$objPHPExcel->getActiveSheet()->getStyle("A1:".chr($countHeaders).'1')->applyFromArray($this->defaultStyle_headers());
			$objPHPExcel->getActiveSheet()->setCellValue('C1', $title);
	        $objPHPExcel->setActiveSheetIndex(0);
	        
	      	$objPHPExcel->getActiveSheet()->fromArray($params['headers'], null, 'A3');

	      	$objPHPExcel->getActiveSheet()->getStyle("A3:$column")->applyFromArray($this->defaultStyle_headers());
	      	
	      	foreach(range('A',$column) as $columnID) {
			    $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
			}
	      	
	      	$objPHPExcel->getActiveSheet()->fromArray($params['items'], null, 'A4'); 
	      	
		
			$objPHPExcel->setActiveSheetIndex(0);
			if($debug==false){
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="'.$title.'.xlsx"');
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				$objWriter->save('php://output');
				exit;
			}
				
			
		}else{
			redirect('override_404');
		}
	}

	private function defaultStyle_headers(){
		$styleHeaders = array(
									'alignment' => array(
												'horizontal' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
									),
							        'fill' => array(
							            'type' => PHPExcel_Style_Fill::FILL_SOLID,
							            'color' => array('rgb' => '000000'),
							        ),
									'font'  => array(
									        'bold'  => true,
									        'color' => array('rgb' => 'FFFFFF'),
									        'name'  => 'Verdana'
									        )
								);
		return $styleHeaders;
	}
}