<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH.'third_party/PHPExcel/PHPExcel.php');
require_once(APPPATH.'third_party/PHPExcel/PHPExcel/IOFactory.php');

class excel extends PHPExcel{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function generate_xlsx($params = array(), $save = false){
		
		$title   = (array_key_exists('title',$params)) ? $params['title'] : 'IS_XLSX';
		$headers = (array_key_exists('headers',$params)) ? $params['headers'] : false;
		$items   = (array_key_exists('items',$params)) ? $params['items'] : false;
		//print_debug($items);
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
	      	
	      	$items = $objPHPExcel->getActiveSheet()->fromArray($params['items'], null, 'A4'); 
			$objPHPExcel->setActiveSheetIndex(0);
				
			if($save){
				$pathfile  = 'assets/docs/'.$title.'.xlsx';
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
				rename(APPPATH.'libraries/excel.xlsx', $pathfile);
				return $pathfile;
			}else{
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="'.$title.'.xlsx"');
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				$objWriter->save('php://output');
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

	public function receta_generate_xlsx($params = array(), $save = false){
		
		$title   = (array_key_exists('title',$params)) ? $params['title'] : 'IS_XLSX';
		$items_recetas   = (array_key_exists('items_receta',$params)) ? $params['items_receta'] : false;
		$headers_receta  = (array_key_exists('headers_receta',$params)) ? $params['headers_receta'] : false;
		$items_valores   = (array_key_exists('items_valores',$params)) ? $params['items_valores'] : false;
		$headers_valores = (array_key_exists('headers_valores',$params)) ? $params['headers_valores'] : false;
		/*$headers = (array_key_exists('headers',$params)) ? $params['headers'] : false;
		$items   = (array_key_exists('items',$params)) ? $params['items'] : false;*/
		//$items   = (array_key_exists('items',$params)) ? $params['items'] : false;
		//print_debug($items);
		if($items_recetas && $headers_receta && $items_valores && $headers_valores){

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
			
			$countHeaders = count($params['headers_receta'])+64;
			$column       = chr($countHeaders).'3';

			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setName('Candara');
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(22);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$objPHPExcel->getActiveSheet()->getStyle("A1:".chr($countHeaders).'1')->applyFromArray($this->defaultStyle_headers());
			$objPHPExcel->getActiveSheet()->setCellValue('C1', $title);
	        $objPHPExcel->setActiveSheetIndex(0);
	        
	      	$objPHPExcel->getActiveSheet()->fromArray($params['headers_receta'], null, 'A3');
	      	$objPHPExcel->getActiveSheet()->getStyle("A3:$column")->applyFromArray($this->defaultStyle_headers());

	      	$objPHPExcel->getActiveSheet()->fromArray($params['headers_valores'], null, 'A6');
	      	$objPHPExcel->getActiveSheet()->getStyle("A6:$column")->applyFromArray($this->defaultStyle_headers());
	      	
	      	foreach(range('A',$column) as $columnID) {
			    $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
			}
	      	
	      	$items = $objPHPExcel->getActiveSheet()->fromArray($params['items_receta'], null, 'A4'); 
			$objPHPExcel->setActiveSheetIndex(0);

			$items = $objPHPExcel->getActiveSheet()->fromArray($params['items_valores'], null, 'A7'); 
			$objPHPExcel->setActiveSheetIndex(0);
				
			if($save){
				$pathfile  = 'assets/docs/'.$title.'.xlsx';
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
				rename(APPPATH.'libraries/excel.xlsx', $pathfile);
				return $pathfile;
			}else{
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="'.$title.'.xlsx"');
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				$objWriter->save('php://output');
				$objWriter->save('php://output');
				exit;
			}
			
		}else{
			redirect('override_404');
		}
	}
	public function test(){
		$inputFileName = 'application/xls/Catalogo_clientes.xlsx';

		//  Read your Excel workbook
		try{
		    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		   // var_dump($inputFileType);
		    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
		    $objPHPExcel = $objReader->load($inputFileName);
		    //var_dump($objPHPExcel);
		} catch(Exception $e) {
		    die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
		}

		//  Get worksheet dimensions
		$sheet = $objPHPExcel->getSheet(0); 
		$highestRow = $sheet->getHighestRow(); 
	 	$highestColumn = $sheet->getHighestColumn();

		//  Loop through each row of the worksheet in turn
		for ($row = 4; $row <= $highestRow; $row++){ 
		    //  Read a row of data into an array
		    $rowData[] = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
		                                    NULL,
		                                    TRUE,
		                                    FALSE);
		    //  Insert row data array into your database of choice here
		}
		return $rowData;

	}
}