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
        function setExcelFormat($arraySettings=0){
            
           /* if( (!empty($arraySettings) ) && (is_array($arraySettings)) && (array_key_exists('title',$arraySettings))) $this->objPHPExcel->getActiveSheet()->setTitle("Reporte Resultado");
    
            if( (!empty($arraySettings) ) && (is_array($arraySettings)) && (array_key_exists('cells',$arraySettings)) && (!empty($arraySettings['cells'])) && (is_array($arraySettings['cells'])) ) {
                if(){
                    $this->objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
                }
            }
            */
            $this->objPHPExcel->getActiveSheet()->setTitle("Reporte Resultado");
            $this->objPHPExcel->setActiveSheetIndex(0);
    
        }
        
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
        //crea un archivo excel nuevo y crea la cabecera adecuada
        function cleanExcel($cleanPercentage,$movilNumber,$titular,$NroFactura,$totalFactura,$cuit,$date){
            
            setExcelFormat();
            $this->objPHPExcel->getActiveSheet()->setCellValue('A1', "Perido: ");
           /* $objPHPExcel->getActiveSheet()->setCellValue('B1', "{$date->format("Y-m")}");
            $objPHPExcel->getActiveSheet()->setCellValue('A2', "Nombre Titular");
            $objPHPExcel->getActiveSheet()->setCellValue('B2', "$titular");
            $objPHPExcel->getActiveSheet()->setCellValue('C2', "Cuit: $cuit");
            $objPHPExcel->getActiveSheet()->setCellValue('A3', "Movil  $movilNumber");
            $objPHPExcel->getActiveSheet()->setCellValue('A4', "Factura: $NroFactura");
            $objPHPExcel->getActiveSheet()->setCellValue('A5', "Total:");
            $objPHPExcel->getActiveSheet()->setCellValue('B5', "$totalFactura");
           */
            $this->objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
            /*
            $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
            $objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(20);
            $objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(20);
            $objPHPExcel->getActiveSheet()->getRowDimension('5')->setRowHeight(20);
            $objPHPExcel->getActiveSheet()->getRowDimension('6')->setRowHeight(20);
            $objPHPExcel->getActiveSheet()->getRowDimension('7')->setRowHeight(20);
            $objPHPExcel->getActiveSheet()->getRowDimension('8')->setRowHeight(20);
            */
            
            
            
            return $this->objPHPExcel;
        }
    
        //Guarda un viaje en una fila de excel
       // function saveTripInExcel($date,$importeFinal,$comison,$row,$objPHPExcel){
        function setDataExcel(){
            $this->objPHPExcel->setActiveSheetIndex(0)
             ->setCellValue('A1', 'hola')
             ->setCellValue('B2', 'prueba')
             ->setCellValue('C1', 'fran')
             ->setCellValue('D2', 'world!');
           // $this->objPHPExcel->setActiveSheetIndex(0)->setCellValue('A', "hola");
            /*$this->objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $importeFinal);
            $this->objPHPExcel->getActiveSheet()->setCellValue('C'.$row, round($comison,2));
            */

        }
    
        //convierte la informacion de un viaje para que se pueda guardar en el excel
        function saveTripInfo($trip,$row,$objPHPExcel){
            $fecha_hora = new \DateTime($trip['fecha_hora']);
            $importeFinal = $trip['importe_final_ajustado'];
            $comison   = $trip['comision_ajustada'];
            saveTripInExcel($fecha_hora,$importeFinal,$comison,$row,$objPHPExcel);
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