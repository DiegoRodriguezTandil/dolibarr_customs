<?php
    
    function saveExcel($objPHPExcel){
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
    
    function setTotals(&$objPHPExcel,$row){
        $totalRow=$row+2;
        
        $objPHPExcel
        ->setActiveSheetIndex(0)
        ->setCellValue('D'.$totalRow, "Importe Total(USD)");
        
        $objPHPExcel->getActiveSheet()
        ->getStyle('E'.$totalRow)
        ->getNumberFormat()
        ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_CUSTOM_USD);
        
        $objPHPExcel
        ->setActiveSheetIndex(0)
        ->setCellValue('E'.$totalRow, "=SUM(E2:E{$row})");
        
    }
    
    function addValues(&$objPHPExcel,$arrayExport,&$row){
        if( !empty($arrayExport) && is_array($arrayExport) ) {
            foreach ($arrayExport as $arr=>$arraId){
                if(!empty($arraId)){
                    $tipoDoc = ( !empty($arr) ) ? $arr:"";
                    foreach ($arraId as $id=>$arraData) {
                        $codigo          = ( !empty($arraData[0]) && is_array($arraData) && array_key_exists(0,$arraData) && !empty($arraData[0]) )? $arraData[0]:"";
                        $divisa          = ( !empty($arraData[2]) && is_array($arraData) && array_key_exists(2,$arraData) && !empty($arraData[2]) )? $arraData[2]:"";
                        if ( !empty($arraData[3]) && is_array($arraData) && array_key_exists(3,$arraData) && !empty($arraData[3]) ){
                            $importeOriginal = $arraData[3];
                        }else{
                            $importeOriginal=0;
                        }
                        if ( !empty($arraData[10]) && is_array($arraData) && array_key_exists(10,$arraData) && !empty($arraData[10]) ){
                            $importeDolares = $arraData[10];
                        }else{
                            $importeDolares=0;
                        }
                        $row++;
                        $objPHPExcel
                        ->setActiveSheetIndex(0)
                        ->setCellValue('A'.$row, $tipoDoc);
                        $objPHPExcel
                        ->setActiveSheetIndex(0)
                        ->setCellValue('B'.$row, $codigo)
                        ->setCellValue('C'.$row, $divisa)
                        ->setCellValue('D'.$row, $importeOriginal)
                        ->setCellValue('E'.$row, $importeDolares);
                        
                    }
                }
            }
        }
    }
    
    function generalProperties(&$objPHPExcel){
        $objPHPExcel
        ->getProperties()
        ->setCreator("Maarten Balliauw")
        ->setLastModifiedBy("Maarten Balliauw")
        ->setTitle("Office 2007 XLSX Test Document")
        ->setSubject("Office 2007 XLSX Test Document")
        ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("Test result file");
        // set titles
        $objPHPExcel
        ->setActiveSheetIndex(0)
        ->setCellValue('A1', 'Documento');
        $objPHPExcel
        ->setActiveSheetIndex(0)
        ->setCellValue('B1', 'Código(Ref)');
        $objPHPExcel
        ->setActiveSheetIndex(0)
        ->setCellValue('C1', 'Divísa');
        $objPHPExcel
        ->setActiveSheetIndex(0)
        ->setCellValue('D1', 'Importe Original');
        $objPHPExcel
        ->setActiveSheetIndex(0)
        ->setCellValue('E1', 'Importe Dolares');
        
        $objPHPExcel
        ->setActiveSheetIndex(0)
        ->getColumnDimension('A')
        ->setAutoSize(true);
        
        $objPHPExcel
        ->setActiveSheetIndex(0)
        ->getColumnDimension('B')
        ->setAutoSize(true);
        
        $objPHPExcel
        ->setActiveSheetIndex(0)
        ->getColumnDimension('C')
        ->setAutoSize(true);
        
        $objPHPExcel
        ->setActiveSheetIndex(0)
        ->getColumnDimension('D')
        ->setAutoSize(true);
        
        $objPHPExcel
        ->setActiveSheetIndex(0)
        ->getColumnDimension('E')
        ->setAutoSize(true);
        
        $objPHPExcel
        ->setActiveSheetIndex(0)
        ->getColumnDimension('F')
        ->setAutoSize(true);
        
        //Alignmen
        $objPHPExcel
         ->setActiveSheetIndex(0)
         ->getStyle('A')
         ->getAlignment()
         ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    
        $objPHPExcel
         ->setActiveSheetIndex(0)
         ->getStyle('B')
         ->getAlignment()
         ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    
        $objPHPExcel
         ->setActiveSheetIndex(0)
         ->getStyle('C')
         ->getAlignment()
         ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    
        $objPHPExcel
         ->setActiveSheetIndex(0)
         ->getStyle('D')
         ->getAlignment()
         ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    
        $objPHPExcel
         ->setActiveSheetIndex(0)
         ->getStyle('E')
         ->getAlignment()
         ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        
        $objPHPExcel->getActiveSheet()->setTitle('Resultados de Proyecto');
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="01simple.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        
    }
    
    function formatExcelRows(&$objPHPExcel,$row){
        
        $objPHPExcel->getActiveSheet()
        ->getStyle('D1:D'.$row)
        ->getNumberFormat()
        ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
    
        $objPHPExcel->getActiveSheet()
        ->getStyle('E1:E'.$row)
        ->getNumberFormat()
        ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_CUSTOM_USD);
    }
    
    function exportExcel($objPHPExcel,$arrayExport){
        $row = 1;
        generalProperties($objPHPExcel);
        addValues($objPHPExcel,$arrayExport,$row);
        formatExcelRows($objPHPExcel,$row);
        saveExcel($objPHPExcel);
    }


?>