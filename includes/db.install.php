<?php

/**
 * @author Abood Nour
 * @copyright 2017
 */

if(!defined('CALLED_FROM_INSTALL')){
    die('Forbidden!');
}

if(!file_exists(INC_PATH.'/config.php')){
    die('Missed some steps during installation');
}
require_once(INC_PATH.'/config.php');

$max_rows_per_query = 100;

if(isset($file_data)){
        $rows_count = count($file_data); 
        $columns_count = ($rows_count>0)?count($file_data[0]):0;
        if($rows_count>0 && $columns_count>3){
            
            $subFieldNames = array();
            for($i=0;$i<$columns_count-3;$i++){
                $subFieldNames[] = sprintf('`sub%d`',($i+1));
            }
            $tableSQL = 'CREATE TABLE IF NOT EXISTS `'.DB_PREFIX.'sheet_schema` ('
                        .'`stdID` INT NOT NULL AUTO_INCREMENT ,'
                        .'`num` INT NOT NULL ,'
                        .'`name` VARCHAR(250) NOT NULL ,'
                        .'`grade` VARCHAR(250) NOT NULL ,';
            foreach($subFieldNames as $sub){
                $tableSQL .= sprintf('%s DOUBLE NULL DEFAULT "0",',$sub);
            }
            $tableSQL .= 'PRIMARY KEY (`stdID`)) DEFAULT CHARSET=utf8';
            if($PDO->exec($tableSQL) !== false){
                $dataQuery = sprintf('INSERT INTO `'.DB_PREFIX.'sheet_schema` (`num`,`name`,`grade`,%s) VALUES ',implode(',',$subFieldNames));
                $rowQuery = array();
                $allVars = array();
                foreach($file_data as $i=>$row){
                    //Prepare and Sort row data
                    $seatNo = ($i === 0)?$row[$columnIndices['seat_no']]:intval($row[$columnIndices['seat_no']]); //if it's the 1st row, set value to header value and don't cast to integer
                    //prepare replacement array by adding seat number, name and grade
                    $rowVars = array($seatNo,trim(strval($row[$columnIndices['name']])),trim(strval($row[$columnIndices['grade']])));
                    //remove them from the array and leave only subjects
                    unset($row[$columnIndices['seat_no']],$row[$columnIndices['name']],$row[$columnIndices['grade']]);
                    
                    
                    if($i === 0){
                        //store 1st row as a header and dont store it in db
                        $fh = fopen(INC_PATH.'/'.TB_HEADER_FILENAME,'w');
                        fwrite($fh,json_encode(array_merge($rowVars,$row)));
                        fclose($fh);
                        continue;
                    }
                    
                    //Loop through subjects values and ensure right data is in place
                    //P.S. I used to use array_map but it turned out that Foreach is much faster
                    foreach($row as $columnIndex => $cellValue){
                        //Check if it's a separator (not a real subject), set cell value to MAGIC NUMBER :D 
                        //otherwise just cast value to the right data type 
                        $rowVars[] = (in_array($columnIndex,$columnIndices['sep_columns'],true))?$_CONFIG['magic_number']:((is_null($cellValue))?null:doubleval($cellValue)); 
                    }
                    
                    //Add this row variables to query variable placeholder
                    $allVars = array_merge($allVars,$rowVars);
                    
                    //Parameterized Query String
                    $rowQuery[] = sprintf('(%s)',substr(str_repeat(' ?,',$columns_count),0,-1));
                    
                    if(($i !== 0 && ($i % $max_rows_per_query) === 0) || ($i === (count($file_data)-1) ) ){
                        $query = $dataQuery.implode(',',$rowQuery);
                        try{
                            $q = $PDO->prepare($query);
                            if($q->execute($allVars)){
                                $_result['status'] = 'success';
                                $_result['message'] = 'تم إضافة البيانات بنجاح';
                            }else{
                                $_result['message'] = 'لم يتم إضافة كافة البيانات.. تأكد من إدخال البيانات بشكل صحيح';
                            }
                        }catch(Exception $e){
                            $_result['message'] = 'لم يتم إضافة كافة البيانات.. تأكد من إدخال البيانات بشكل صحيح';
                        }
                        $rowQuery = array(); //reset data
                        $allVars = array(); //reset data
                    }
                }
                
                /*if(count($rowQuery)>0){
                    $query = $dataQuery.implode(',',$rowQuery);
                    $q = $PDO->prepare($query);
                    if($q->execute($allVars)){
                        $_result['status'] = 'success';
                        $_result['message'] = 'تم إضافة كافة البيانات بنجاح';
                    }else{
                        $_result['message'] = 'لم يتم إضافة كافة البيانات.. تأكد من إدخال البيانات بشكل صحيح';
                    }
                    
                }*/
            }else{
                var_dump($PDO->errorInfo());
                $_result['message'] = 'لم نستطع إنشاء الجدول.. من فضلك تأكد من صلاحيات المستخدم أو اختر بادئة مختلفة';
            }
        }else{
            $_result['message'] = 'لا توجد بيانات كافية. من فضلك قم بإدخال على الأقل صف واحد و 4 أعمدة';
        }
}

?>