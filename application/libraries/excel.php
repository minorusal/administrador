<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH.'third_party/PHPExcel/PHPExcel.php');

class excel extends PHPExcel{
	
	public function __construct(){
		parent::__construct();
	}

	private $styleHeaders = array(
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
									        'size'  => 10,
									        'name'  => 'Verdana'
									        )
								);
	


	
	public function generate_excel($params = array()){
		
		$tittle = (isset($params['tittle']))?$params['tittle'] : 'IS XLSX';

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("IS Intelligent Solution")
								->setLastModifiedBy("IS Intelligent Solution")
								->setTitle($tittle)
								->setSubject("Office 2007 XLSX")
								->setDescription("Office 2007 XLSX Document")
								->setKeywords("office 2007 openxml");

		
		$countHeaders = count($params['headers'])+64;
		$column       = chr($countHeaders).'1';

        $objPHPExcel->setActiveSheetIndex(0);
        
      	$objPHPExcel->getActiveSheet()->fromArray($params['headers'], null, 'A1');
      	
      	$objPHPExcel->getActiveSheet()->getStyle("A1:$column")->applyFromArray($this->styleHeaders);
      	
      	$objPHPExcel->getDefaultStyle()->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
		$objPHPExcel->getDefaultStyle()->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
		$objPHPExcel->getDefaultStyle()->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
		$objPHPExcel->getDefaultStyle()->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

      	foreach(range('A',$column) as $columnID) {
		    $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
		}
      	
      	$objPHPExcel->getActiveSheet()->fromArray($params['items'], null, 'A2'); 
      	
	
		$objPHPExcel->setActiveSheetIndex(0);
	
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$params['tittle'].'.xlsx"');
	
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;

	}


}