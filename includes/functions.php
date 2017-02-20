<?php

/**
 * @author Abood Nour
 * @copyright 2017
 */

function is_ajax_request(){
    return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) ==='xmlhttprequest')?true:false;
}

function is_script_installed(){
    global $PDO;
    if(file_exists(INC_PATH.'/config.php')){ //Check presence of configuration file
        require_once(INC_PATH.'/config.php');
        if(is_file(INC_PATH.'/'.TB_HEADER_FILENAME) && is_readable(INC_PATH.'/'.TB_HEADER_FILENAME)){ //Check presence of header.json file
            if(is_object($PDO) && $PDO instanceof PDO){
                if($PDO->query(sprintf('SHOW TABLES LIKE "%ssheet_schema"',DB_PREFIX))->fetchColumn()){ //Check presence of sheet_schema table in DB 
                    return true;
                }
            }
        }
    }
    return false;
}

function die_error($error_message){
    if(is_ajax_request()){
        die(json_encode(array('status'=>'error','message'=>$error_message,'data'=>'')));
    }else{
        $_temp_vars['error_details'] = $error_message;
        $page_content = 'error.php';
        include_once(TEMP_PATH.'/main.php');
    }
    exit();
}

function get_alert($message = '',$type='error'){
    return sprintf('<div class="alert %s">%s</div>',Natiga_Security::escapeAttribute($type),$message);
}

function parse_as_template($file,$vars = array(),$use_global_vars = true){
    if(!empty($vars) && is_array($vars)){
        extract($vars);
    }
    
    if($use_global_vars === true){
        extract($GLOBALS, EXTR_SKIP);
    }
    
    $__template_result_holder__ = '';
    if(is_file($file) && is_readable($file)){
        try{
            ob_start(); //prevent output and save it in buffer
            include($file);
            $__template_result_holder__ = ob_get_clean();
            ob_end_clean();
        }catch(Exception $e){
            $__template_result_holder__ = '';
        }
    }
    return $__template_result_holder__;
}

?>