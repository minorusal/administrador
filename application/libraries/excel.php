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
	private function defaultStyle_aviso(){
		$defaultStyle_aviso = array(
									'alignment' => array(
												'horizontal' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
									),
							        'fill' => array(
							            'type' => PHPExcel_Style_Fill::FILL_SOLID,
							            'color' => array('rgb' => 'FF6666'),
							        ),
									'font'  => array(
									        'bold'  => false,
									        'color' => array('rgb' => 'FFFFFF'),
									        'name'  => 'Verdana'
									        ),
									'borders' => array(
											'style' => PHPExcel_Style_Border::BORDER_THIN
										)
								);
		return $defaultStyle_aviso;
	}

	public function receta_generate_xlsx($params = array(), $save = false){
		$title               = (array_key_exists('title',$params)) ? $params['title'] : 'IS_XLSX';
		$items_recetas       = (array_key_exists('items_receta',$params)) ? $params['items_receta'] : false;
		$headers_receta      = (array_key_exists('headers_receta',$params)) ? $params['headers_receta'] : false;
		$items_valores       = (array_key_exists('items_valores',$params)) ? $params['items_valores'] : false;
		$headers_valores     = (array_key_exists('headers_valores',$params)) ? $params['headers_valores'] : false;
		$headers_costo_total = (array_key_exists('headers_costo_total',$params)) ? $params['headers_costo_total'] : false;
		$items_costo_total   = (array_key_exists('items_costo_total',$params)) ? $params['items_costo_total'] : false;
		$preparacion         = (array_key_exists('preparacion',$params)) ? $params['preparacion'] : false;
		
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
			
			$countHeadersReceta = count($params['headers_receta'])+64;
			$columnReceta       = chr($countHeadersReceta).'3';

			$total_valores = (count($params['items_valores']));
			$inicio_total  = 'D'.(count($params['items_valores'])+7);
			$unidad_total  = 'D'.(count($params['items_valores'])+6);
			
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setName('Candara');
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(22);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$objPHPExcel->getActiveSheet()->getStyle("A1:".chr($countHeadersReceta).'1')->applyFromArray($this->defaultStyle_headers());

			/*$objPHPExcel->getActiveSheet()->mergeCells('G1:K1')->getStyle("G1:K1")->applyFromArray($this->defaultStyle_aviso());
			$objPHPExcel->getActiveSheet()->mergeCells('G2:K2')->getStyle("G2:K2")->applyFromArray($this->defaultStyle_aviso());
			$objPHPExcel->getActiveSheet()->mergeCells('G3:K3')->getStyle("G3:K3")->applyFromArray($this->defaultStyle_aviso());
			$objPHPExcel->getActiveSheet()->mergeCells('G4:K4')->getStyle("G4:K4")->applyFromArray($this->defaultStyle_aviso());*/
			
			$objPHPExcel->getActiveSheet()->setCellValue('C1', $title);
	        $objPHPExcel->setActiveSheetIndex(0);
	        
	      	$objPHPExcel->getActiveSheet()->fromArray($params['headers_receta'], null, 'A3');
	      	$objPHPExcel->getActiveSheet()->getStyle("A3:$columnReceta")->applyFromArray($this->defaultStyle_headers());

	      	$objPHPExcel->getActiveSheet()->fromArray($params['headers_valores'], null, 'A6');
	      	$objPHPExcel->getActiveSheet()->getStyle("A6:AC6")->applyFromArray($this->defaultStyle_headers());
	      	
	        foreach(range('A',$columnReceta) as $columnID) {
			    $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
			}
			
			for ($i="A" ; $i!="AE" ; $i++) { 
    			$objPHPExcel->getActiveSheet()->getColumnDimension($i)->setAutoSize(true);
			}

			foreach(range('A',$columnReceta) as $columnID) {
			    $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
			} 			
	      	
	      	$items = $objPHPExcel->getActiveSheet()->fromArray($params['items_receta'], null, 'A4'); 
			$objPHPExcel->setActiveSheetIndex(0);

			$items = $objPHPExcel->getActiveSheet()->fromArray($params['items_valores'], null, 'A7'); 
			$objPHPExcel->setActiveSheetIndex(0);

			for($i='E';$i!='AD';$i++){
				$objPHPExcel->getActiveSheet()->mergeCells('K'.(count($params['items_valores'])+9).':O'.(count($params['items_valores'])+9));
				$objPHPExcel->getActiveSheet()->setCellValue('E'.(count($params['items_valores'])+7), 'Costo Total $');
				$objPHPExcel->getActiveSheet()->setCellValue('F'.(count($params['items_valores'])+7), '(g.)');
				$objPHPExcel->getActiveSheet()->setCellValue('G'.(count($params['items_valores'])+7), '(g.)');
				$objPHPExcel->getActiveSheet()->setCellValue('H'.(count($params['items_valores'])+7), '(kcal.)');
				$objPHPExcel->getActiveSheet()->setCellValue('I'.(count($params['items_valores'])+7), '(g.)');
				$objPHPExcel->getActiveSheet()->setCellValue('J'.(count($params['items_valores'])+7), '(g.)');
				$objPHPExcel->getActiveSheet()->setCellValue('K'.(count($params['items_valores'])+7), '(g.)');
				$objPHPExcel->getActiveSheet()->setCellValue('L'.(count($params['items_valores'])+7), '(g.)');
				$objPHPExcel->getActiveSheet()->setCellValue('M'.(count($params['items_valores'])+7), '(µg RE.)');
				$objPHPExcel->getActiveSheet()->setCellValue('N'.(count($params['items_valores'])+7), '(mg.)');
				$objPHPExcel->getActiveSheet()->setCellValue('O'.(count($params['items_valores'])+7), '(mg.)');
				$objPHPExcel->getActiveSheet()->setCellValue('P'.(count($params['items_valores'])+7), '(mg.)');
				$objPHPExcel->getActiveSheet()->setCellValue('Q'.(count($params['items_valores'])+7), '(mg.)');
				$objPHPExcel->getActiveSheet()->setCellValue('R'.(count($params['items_valores'])+7), '(g.)');
				$objPHPExcel->getActiveSheet()->setCellValue('S'.(count($params['items_valores'])+7), '(.)');
				$objPHPExcel->getActiveSheet()->setCellValue('T'.(count($params['items_valores'])+7), '(.)');
				$objPHPExcel->getActiveSheet()->setCellValue('U'.(count($params['items_valores'])+7), '(mg.)');
				$objPHPExcel->getActiveSheet()->setCellValue('V'.(count($params['items_valores'])+7), '(mg.)');
				$objPHPExcel->getActiveSheet()->setCellValue('W'.(count($params['items_valores'])+7), '(mcg.)');
				$objPHPExcel->getActiveSheet()->setCellValue('X'.(count($params['items_valores'])+7), '(mg.)');
				$objPHPExcel->getActiveSheet()->setCellValue('Y'.(count($params['items_valores'])+7), '(mg.)');
				$objPHPExcel->getActiveSheet()->setCellValue('Z'.(count($params['items_valores'])+7), '(g.)');
				$objPHPExcel->getActiveSheet()->setCellValue('AA'.(count($params['items_valores'])+7), '(g.)');
				$objPHPExcel->getActiveSheet()->setCellValue('AB'.(count($params['items_valores'])+7), '(g.)');
				$objPHPExcel->getActiveSheet()->setCellValue('AC'.(count($params['items_valores'])+7), '(g.)');
				$objPHPExcel->getActiveSheet()->setCellValue('E'.(count($params['items_valores'])+9), 'Costo Porción $');
				$objPHPExcel->getActiveSheet()->getStyle($i.(count($params['items_valores'])+8).':AC'.(count($params['items_valores'])+8))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle($i.(count($params['items_valores'])+10).':AC'.(count($params['items_valores'])+10))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle($i.(count($params['items_valores'])+7).':'.$i.(count($params['items_valores'])+7))->applyFromArray($this->defaultStyle_headers());
				$objPHPExcel->getActiveSheet()->getStyle($i.(count($params['items_valores'])+9).':'.$i.(count($params['items_valores'])+9))->applyFromArray($this->defaultStyle_headers());
				$objPHPExcel->getActiveSheet()->setCellValue($i.(count($params['items_valores'])+8),'=SUM('.$i.'7:'.$i.(count($params['items_valores'])+7).')');
				$objPHPExcel->getActiveSheet()->setCellValue($i.(count($params['items_valores'])+10),'=('.$i.(count($params['items_valores'])+8).'/D4)');

				
				$objPHPExcel->setActiveSheetIndex(0);
			}

			$objPHPExcel->getActiveSheet()->getStyle('A'.(count($params['items_valores'])+12).':O'.(count($params['items_valores'])+12))->applyFromArray($this->defaultStyle_headers());
			$objPHPExcel->getActiveSheet()->mergeCells('A'.(count($params['items_valores'])+12).':O'.(count($params['items_valores'])+12));
			$objPHPExcel->getActiveSheet()->setCellValue('A'.(count($params['items_valores'])+12), 'PREPARACIÓN');
			$objPHPExcel->setActiveSheetIndex(0);

			$objPHPExcel->getActiveSheet()->getRowDimension((count($params['items_valores'])+13))->setRowHeight(40);
			$objPHPExcel->getActiveSheet()->getStyle('A'.(count($params['items_valores'])+13).':O'.(count($params['items_valores'])+13))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->mergeCells('A'.(count($params['items_valores'])+13).':O'.(count($params['items_valores'])+13));
			$objPHPExcel->getActiveSheet()->setCellValue('A'.(count($params['items_valores'])+13),$params['preparacion'][0][0]); 
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