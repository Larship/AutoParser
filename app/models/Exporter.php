<?php
class Exporter
{
	/**
	 * Метод производит выгрузку в формате Excel данных об автомобилях.
	 * 
	 * @param string $_title Заголовок выгружаемого документа.
	 * @param array $_exportData Массив данных для выгрузки.
	 */
	public static function export($_title, $_exportData)
	{
		$phpExcelObj = new PHPExcel();
		$phpExcelObj->getActiveSheet()->setTitle('Список');
		$phpExcelObj->getProperties()->
			setCreator('Коваленко Антон Валентинович')->
			setLastModifiedBy('Коваленко Антон Валентинович')->
			setTitle($_title);
		
		$dataArr = array();
		$dataArr[] = array('ID объявления', 'Дата', 'Изображение', 'Марка и модель', 'Год выпуска', 'Характеристики', 'Пробег, тыс. км.', 'Цена, руб.', 'Город');
		
		$rowCount = 1;
		
		foreach($_exportData as $dataRow)
		{
			$dataArr[] = array($dataRow['advertId'], $dataRow['date'], $dataRow['carImageUrl'], $dataRow['carName'], $dataRow['carYear'], $dataRow['carFeatures'], $dataRow['carMileage'], $dataRow['price'], $dataRow['city']);
			$rowCount++;
		}
		
		$phpExcelObj->getActiveSheet()->fromArray($dataArr, NULL, 'A1');
		$phpExcelObj->getActiveSheet()->getColumnDimension('A')->setWidth(15);
		$phpExcelObj->getActiveSheet()->getColumnDimension('B')->setWidth(10);
		$phpExcelObj->getActiveSheet()->getColumnDimension('C')->setWidth(30);
		$phpExcelObj->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$phpExcelObj->getActiveSheet()->getColumnDimension('E')->setWidth(15);
		$phpExcelObj->getActiveSheet()->getColumnDimension('F')->setWidth(30);
		$phpExcelObj->getActiveSheet()->getColumnDimension('G')->setWidth(17);
		$phpExcelObj->getActiveSheet()->getColumnDimension('H')->setWidth(12);
		$phpExcelObj->getActiveSheet()->getColumnDimension('I')->setWidth(20);
		$phpExcelObj->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$phpExcelObj->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
		$phpExcelObj->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
		$phpExcelObj->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
		$phpExcelObj->getActiveSheet()->getStyle('E1')->getFont()->setBold(true);
		$phpExcelObj->getActiveSheet()->getStyle('F1')->getFont()->setBold(true);
		$phpExcelObj->getActiveSheet()->getStyle('G1')->getFont()->setBold(true);
		$phpExcelObj->getActiveSheet()->getStyle('H1')->getFont()->setBold(true);
		$phpExcelObj->getActiveSheet()->getStyle('I1')->getFont()->setBold(true);
		$phpExcelObj->getActiveSheet()->setSelectedCell('A1');
		
		// Вставка изображений -->
		$objWriter = new PHPExcel_Writer_Excel2007($phpExcelObj);
		
		for($i = 2; $i <= $rowCount; $i++)
		{
			$localImageName = 'public/images/img_' . $i . '.jpg';
			
			$imageUrl = $phpExcelObj->getActiveSheet()->getCell('C' . $i)->getValue();
			$rawImageData = HTTP::curlGET($imageUrl);

			$fp = fopen($localImageName, 'w+');
			
			if($fp !== false)
			{
				fwrite($fp, $rawImageData);
				fclose($fp);
			}
			
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setPath($localImageName);
			$objDrawing->setCoordinates('C' . $i);
			$objDrawing->setWorksheet($phpExcelObj->getActiveSheet());
			
			$phpExcelObj->getActiveSheet()->getCell('C' . $i)->setValue('');
			
			$phpExcelObj->getActiveSheet()->getRowDimension($i)->setRowHeight(78);
			
//			unlink($localImageName);
		}
		// <-- Вставка изображений
		
		ob_start();
		$objWriter->save('php://output');
		$data = ob_get_clean();
		
		header('Pragma: no-cache');
		header('Content-Description: File Transfer');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $_title . '.xlsx"');
		header('Content-Length: ' . strlen($data));
		echo $data;
	}
}
