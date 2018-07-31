<?php
    /**
     * Created by PhpStorm.
     * User: superq
     * Date: 31/07/18
     * Time: 12:19
     */
    require_once DOL_DOCUMENT_ROOT.'/includes/phpexcel/PHPExcel.php';
    class Phpexcelconfiguration // extends CommonObject
    {
         var $objPHPExcel ;
        /**
         *  Constructor
         *
         * @param    DoliDb $db Database handler
         */
        function __construct()
        {
            $this->objPHPExcel = new \PHPExcel();
        }
    
       public function getObjPHPExcel(){
                return $this->objPHPExcel;
        }
        
        
        function setExcelFormat($arraySettings=null){
            $this->objPHPExcel->setActiveSheetIndex(0)
             ->setCellValue('A0', 'Documento')
             ->setCellValue('B0', 'Código(Ref)')
             ->setCellValue('C0', 'Divísa')
             ->setCellValue('D0', 'Importe Original')
             ->setCellValue('e0', 'Importe Dolares')
             ->setCellValue('f0', 'Importe Total(USD)');
            $this->objPHPExcel->getActiveSheet()->setTitle("Reporte Resultado");
        }
    
       /* function setExcelFormat2($arraySettings=null){
        
            if( (!empty($arraySettings) ) && (is_array($arraySettings)) && (array_key_exists('title',$arraySettings))) $this->objPHPExcel->getActiveSheet()->setTitle("Reporte Resultado");
        
            var_dump((!empty($arraySettings));
            var_dump(is_array($arraySettings));
            var_dump(array_key_exists('cells',$arraySettings));
            var_dump(!empty($arraySettings['cells']));
            var_dump(is_array($arraySettings['cells']));
            if( (!empty($arraySettings) ) && (is_array($arraySettings)) && (array_key_exists('cells',$arraySettings)) && (!empty($arraySettings['cells'])) && (is_array($arraySettings['cells'])) ) {
                foreach ($arraySettings['cells'] as $celda=>$nombre){
                    $celda        = $celda;
                    $description  = $nombre;
                    $this->objPHPExcel->setActiveSheetIndex(0)->setCellValue($celda, $description);
                }
            }
            $this->objPHPExcel->setActiveSheetIndex(0);
        }*/
        function setExcelProperties(){
            // Set document properties
            $this->objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
             ->setLastModifiedBy("Maarten Balliauw")
             ->setTitle("Office 2007 XLSX Test Document")
             ->setSubject("Office 2007 XLSX Test Document")
             ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
             ->setKeywords("office 2007 openxml php")
             ->setCategory("Test result file");
    
            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="01simple.xlsx"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');
    
            // If you're serving to IE over SSL, then the following may be needed
            header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0
        }
    
        //Guarda un viaje en una fila de excel
       // function saveTripInExcel($date,$importeFinal,$comison,$row,$objPHPExcel){
        function setDataExcel(){
            $this->objPHPExcel->setActiveSheetIndex(0)
             ->setCellValue('A2', 'hola')
             ->setCellValue('B2', 'prueba')
             ->setCellValue('C2', 'fran')
             ->setCellValue('D2', 'world!');
           // $this->objPHPExcel->setActiveSheetIndex(0)->setCellValue('A', "hola");
            /*$this->objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $importeFinal);
            $this->objPHPExcel->getActiveSheet()->setCellValue('C'.$row, round($comison,2));
            */

        }
        //Guarda un viaje en una fila de excel
        function saveRowExcel($arrayRow,$row){
            if( (!empty($arrayRow) )  &&  (is_array($arrayRow)) ){
                if( !empty($arrayRow['Documento']) && array_key_exists('Documento',$arrayRow))
                    var_dump("Documento");
                    $this->objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $arrayRow["Documento"]);
                
                if( !empty($arrayRow['Codigo']) && array_key_exists('Codigo',$arrayRow))
                    var_dump("Codigo");
                    $this->objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $arrayRow["Codigo"]);
                
                if( !empty($arrayRow['Divisa']) && array_key_exists('Divisa',$arrayRow))
                    var_dump("Divisa");
                    $this->objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $arrayRow["Divisa"]);
                
                if( !empty($arrayRow['ImporteOriginal']) && array_key_exists('ImporteOriginal',$arrayRow))
                    var_dump("ImporteOriginal");
                    $this->objPHPExcel->getActiveSheet()->setCellValue('D'.$row, round($arrayRow["ImporteOriginal"]),2 );
                
                if( !empty($arrayRow['ImporteDolares']) && array_key_exists('ImporteDolares',$arrayRow))
                    var_dump("ImporteDolares");
                    $this->objPHPExcel->getActiveSheet()->setCellValue('E'.$row,round( $arrayRow["ImporteDolares"]),2 );
        
                $this->objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);
            }
        }

        

    
        //Funcion que setea el formato de las columnas correctamente
        function formatExcelRows($objPHPExcel,$row){
            $filaInfo = $row+1;
            $filaTotal = $filaInfo+1;
        
            $objPHPExcel->getActiveSheet()->getRowDimension($filaInfo)->setRowHeight(20);
            $objPHPExcel->getActiveSheet()->getRowDimension($filaTotal)->setRowHeight(20);
        
            $objPHPExcel->getActiveSheet()->setCellValue("C$filaTotal","=SUM(C8:C$row)");
            $objPHPExcel->getActiveSheet()->setCellValue("C$filaInfo","Total Comision:");
        
            $objPHPExcel->getActiveSheet()->setCellValue("B$filaTotal","=SUM(B8:B$row)");
            $objPHPExcel->getActiveSheet()->setCellValue("B$filaInfo","Total de Viajes:");
        
            $objPHPExcel->getActiveSheet()
             ->getStyle('C1:C'.$filaTotal)
             ->getNumberFormat()
             ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        
            $objPHPExcel->getActiveSheet()
             ->getStyle('B1:B'.$filaTotal)
             ->getNumberFormat()
             ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        }
        
        
    
        //funcion que formatea el excel corectamente y guarda el excel. Crea la carpeta de ser necesario
        function saveExcel(){
           // formatExcelRows($objPHPExcel,$row);
            $projectRef         = "pp";
            $fileName           = "export_resultado_projecto_{$projectRef}.xls";
            $path               = DOL_DATA_ROOT."/projet/resultado/";
            $fileNameWithPath   = $path.$fileName;
          
            //try{
    
            $objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit;;die();
               $objWriter->setTempDir($path) ;
               var_dump( $objWriter->save($fileNameWithPath));
            var_dump($fileNameWithPath);die();
            
            /*}catch (\PHPExcel_Writer_Exception $e){
            
                mkdir($path,0700);
                $objWriter->setTempDir($path);
                $objWriter->save($fileNameWithPath);
            }*/
            var_dump("Se guardo el excel .");
        }
    
    }