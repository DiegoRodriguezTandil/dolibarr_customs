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
        function setExcelFormat($arraySettings){
            
            $this->objPHPExcel->setActiveSheetIndex(0);
           /* if( (!empty($arraySettings) ) && (is_array($arraySettings)) && (array_key_exists('title',$arraySettings))) $this->objPHPExcel->getActiveSheet()->setTitle("Reporte Resultado");
    
            if( (!empty($arraySettings) ) && (is_array($arraySettings)) && (array_key_exists('cells',$arraySettings)) && (!empty($arraySettings['cells'])) && (is_array($arraySettings['cells'])) ) {
                if(){
                    $this->objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
                }
            }
            */
            $this->objPHPExcel->getActiveSheet()->setTitle("Reporte Resultado");
            $this->objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
            $this->objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setAutoSize(true);
            $this->objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setAutoSize(true);
            $this->objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setAutoSize(true);
            $this->objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setAutoSize(true);
            $this->objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setAutoSize(true);
            $this->objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setAutoSize(true);
            $this->objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setAutoSize(true);
            $this->objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('J')->setAutoSize(true);
            $this->objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('K')->setAutoSize(true);
            $this->objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('L')->setAutoSize(true);
            
        
    
        }
    
       
    
    }