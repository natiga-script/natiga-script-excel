<?php

/**
 * @author Abood Nour
 * @copyright 2017
 */

//Start Session
session_start();

//Setting default content-type and character set
header('Content-type: text/html; charset=utf8');

//Defining include path and template path
define('INC_PATH', dirname(__file__) . "/includes");
define('TEMP_PATH', dirname(__file__) . "/templates");

//Initializing variables and setting default values
$_temp_vars = array();
$_CONFIG = array();
require_once(INC_PATH.'/site_config.php');
$_CONFIG['is_production_env'] = isset($_CONFIG['is_production_env'])?$_CONFIG['is_production_env']:true;
ini_set('memory_limit',$_CONFIG['memory_limit']);

//Handle errors within the script
ini_set('display_errors', !$_CONFIG['is_production_env']);
$_errors_to_display = ($_CONFIG['is_production_env'])?0:E_ALL; //Show all errors if we are not in production environment
error_reporting($_errors_to_display);
register_shutdown_function(function(){
        global $_CONFIG;
        $error = error_get_last();
        //if it's a fatal error that can't be handled then die gracefully :)
        if(null !== $error && $error['type'] === E_ERROR)
        {
                $err_msg = 'حدث خطأ غير متوقع. <br/> إذا كنت تحاول رفع ملف قد يكون الملف ضخم لا يمكن معالجته<br/>إذا كان غير ذلك من فضلك تواصل مع المطور';
                $err_msg .= (!$_CONFIG['is_production_env'])?('<br/> تفاصيل الخطأ: '.$error['message']):'';
                die_error($err_msg);
        }
    });

//template vars
$_temp_vars['title'] = $_CONFIG['site_title'];
$_temp_vars['temp_url'] = $_CONFIG['template_assets_url'];

//default response
$_result = array('status' => 'success', 'message' => '', 'data' => array());

//including core libs
require_once (INC_PATH . '/class.security.php');
require_once (INC_PATH . '/functions.php');

//Generate CSRF token and save its value
$_temp_vars['csrf_token'] = Natiga_Security::generate_csrf_token();

//Validate POST requests for CSRF attacks
if (strtoupper($_SERVER['REQUEST_METHOD']) === "POST") {
    //get csrf token value from POST body or request headers
    $csrf_token = (!empty($_POST['csrf_token']))?$_POST['csrf_token']:((!empty($_SERVER['HTTP_X_CSRF_TOKEN']))?$_SERVER['HTTP_X_CSRF_TOKEN']:'');
    
    if(empty($csrf_token) || !Natiga_Security::validate_csrf_token($csrf_token)){
        if(is_ajax_request()){
            header('Content-Type: application/json');
            die(json_encode(array('status'=>'error','message'=>'Invalid CSRF Token')));
        }else{
            $_temp_vars['error_details'] = 'إما أن يكون طلبك قديم أو هناك محاولة لتزييف طلبك.. <br/> قم بإعادة تحميل الصفحة وحاول مرّة أخرى';
            $page_content = 'error.php';
            require_once(TEMP_PATH.'/main.php');
            exit;
        }
    }
}

?>