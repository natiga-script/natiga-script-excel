<?php

if(!class_exists('PHPExcel')){
    require_once('PHPExcel.php');
}

/**
 * This class is responsible for reading excel files
 * 
 * @author Abood Nour
 * @copyright 2017
 * @uses PHPExcel to parse and read excel files
 * @version 1.00
 * */
class Natiga_Reader
{
    private $objReader;
    private $objPHPExcel;
    private $currentSheetIndex;
    const OPIMIZE_MEMORY = 0;
    const OPIMIZE_SPEED = 1;
    public static $empty_sheet_array = array(array('sheet_name' => '', 'data' => array(array())));
                
    /**
     * Class Constructor
     * Tries to read excel file
     * 
     * @uses PHPExcel_IOFactory::identify  to Identify file type
     * @uses PHPExcel_IOFactory::createReader to Create a new Reader instance based on file type
     * 
     * @return Natiga_Reader
     *  returns an instance of this class
     * */

    function __construct($filename,$optimization_method=self::OPIMIZE_SPEED)
    {
        if(file_exists($filename) && is_readable($filename)){
            try {
                if($optimization_method === self::OPIMIZE_SPEED){
                    PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized);
                }else{
                    PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip);
                    //If you are reading this, then you should know that there is a third option
                    //which is to store it in on disk but it will be much slower.
                }
                /**  Identify the type of $filename  **/
                $inputFileType = PHPExcel_IOFactory::identify($filename);
                /**  Create a new Reader of the type that has been identified  **/
                $this->objReader = PHPExcel_IOFactory::createReader($inputFileType);
                /**  Advise the Reader that we only want to load cell data  **/
                $this->getReaderObj()->setReadDataOnly(true);
                /**  Load $filename to a PHPExcel Object  **/
                $this->objPHPExcel = $this->getReaderObj()->load($filename);
                $this->currentSheetIndex = 0;
                return $this;
            }
            catch (PHPExcel_Reader_Exception $e) {
                //return false;
            }
        }
        //return false;
    }
    
    /**
     * Retrieve current PHPExcel instance
     * 
     * @return PHPExcel
     *  returns current PHPExcel instance
     * */
    function getExcelObj()
    {
        return $this->objPHPExcel;
    }
    
    /**
     * Retrieve current PHPExcel Reader instance
     * 
     * @return PHPExcel_Reader_IReader
     *  returns current PHPExcel Reader instance
     * */
    function getReaderObj()
    {
        return $this->objReader;
    }
    
    /**
     * Determine whether file was successfully parsed
     * 
     * @return bool
     *  returns true if file was read, false otherwise
     * */
     function hasNoErrors(){
        return is_object($this->getExcelObj());
     }
     
     /**
      * Disconnects sheets and frees resources
      * 
      * @return void
      * */
      function closeSession(){
        if($this->hasNoErrors()){
            $this->getExcelObj()->disconnectWorksheets();
        }
        unset($this->objPHPExcel,$this->objReader);
      }
    
    /**
     * Find Sheet by its name
     * Sets index of the sheet to retrieve to the one corresponding this sheet name
     * 
     * @param String @sheetName
     *  Name of the sheet you want to retrieve
     * 
     * @return Natiga_Reader
     *  returns current instance of this class
     * */

    function getSheetByName($sheetName)
    {
        if($this->hasNoErrors()){
            if (($i = array_search($sheetName, $this->objPHPExcel->getSheetNames())) !== false) {
                $this->currentSheetIndex = abs(intval($i));
            }else{
                $this->currentSheetIndex = null;
            }
        }
        return $this;
    }
    
    /**
     * Find Sheet by its index
     * Sets index of the sheet to retrieve to this value
     * 
     * @param int $i
     *  Sheet index
     * 
     * @return Natiga_Reader
     *  returns current instance of this class
     * */

    function getSheetByIndex($i)
    {
        $this->currentSheetIndex = abs(intval($i));
        return $this;
    }
    
    /**
     * Instructs class to retrieve all sheets in the file  
     * 
     * @return Natiga_Reader
     *  returns current instance of this class
     * */

    function getAllSheets()
    {
        $this->currentSheetIndex = '*';
        return $this;
    }
    
    /**
     * Converts column index to corresponding Excel column index format  
     * 
     * @param int $index
     *  Column Index starting from 1
     * 
     * @return String
     *  returns corresponding column index in Excel format
     * */ 
    public static function columnIndexToAlpha($index)
    {
        if(is_null($index)){
            return null;
        }
        $alpha = range('A', 'Z');
        $alphaIndex = '';
        $index = abs(intval($index)) - 1;
        $count_alpha = 26;
        do {
            $alphaIndex .= $alpha[($index % ($count_alpha))];
            $index -= $count_alpha;
        } while ($index >= 0);
        return $alphaIndex;
    }
    
    /**
     * Outputs previously specified sheet(s) into an array  
     * 
     * @uses Natiga_Reader::$currentSheetIndex to read this sheet
     * @param int|null $maxRows
     *  Number of rows to retrieve, null to retrieve them all
     * @param int $maxColumns
     *  Number of columns to retrieve, null to retrieve them all
     * @param int $startRow
     *  Determines the row to start from
     * @param int $startColumn
     *  Determines the column to start from
     * 
     * @return array
     *  returns the content of the sheet(s) in the form of an array
     * */ 
    function toArray($maxRows = null, $maxColumns = null, $startRow = 1, $startColumn =1)
    {
        $output = self::$empty_sheet_array;
        if($this->hasNoErrors() && $this->currentSheetIndex !== null){
            $sheets = $this->getExcelObj()->getSheetNames();
            $endRow = (is_null($maxRows)) ? null : abs(intval($startRow + $maxRows-1));
            $endColumn = (is_null($maxColumns)) ? null : abs(intval($startColumn + $maxColumns-1));
            if ($this->currentSheetIndex !== '*') {
                if ($this->currentSheetIndex < count($sheets)) {
                    $row_i = 0;
                    try{
                        foreach ($this->getExcelObj()->getSheet($this->currentSheetIndex)->getRowIterator($startRow, $endRow) as $row) {
                            $column_i = 0;
                            foreach ($row->getCellIterator(self::columnIndexToAlpha($startColumn), self::columnIndexToAlpha($endColumn)) as $cell) {
                                $output[0]['data'][$row_i][$column_i] = $cell->getCalculatedValue();
                                $column_i++;
                            }
                            $row_i++;
                        }
                    }catch(Exception $e){
                        
                    }
                }
            } else {
                foreach ($sheets as $i => $sheetName) {
                    $_output = $this->getSheetByIndex($i)->toArray($maxRows, $maxColumns, $startRow, $startColumn);
                    $output[$i]['sheet_name'] = $sheetName;
                    $output[$i]['data'] = $_output[0]['data'];
                }
                $this->currentSheetIndex = '*';
            }
        }
        return $output;
    }
}

?>