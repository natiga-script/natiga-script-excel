<?php

/**
 * @author Abood Nour
 * @copyright 2017
 */

$_CONFIG = isset($_CONFIG)?$_CONFIG:array();
$_CONFIG['allow_partial_names'] = true;
$_CONFIG['allow_partial_grades'] = false;
$_CONFIG['allow_search_all_grades'] = false;
$_CONFIG['search_by'] = 6; // 2=> only seat_no, 4=> only name. 6 => both
$_CONFIG['site_title'] = 'نتيجة المدرسة';
$_CONFIG['template_assets_url'] = 'http://natiga-script.cf/templates/assets/';



$_CONFIG['is_production_env'] = true;

$_CONFIG['allowed_file_ext'] = array('xls','xlsx','xlsm','xltx','xltm','xlt','csv');
$_CONFIG['allowed_file_mime'] = array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/excel','application/x-excel','application/vnd.ms-excel','application/x-msexcel','text/plain','text/csv');

$_CONFIG['memory_limit'] = '1024m'; //you can set it to "-1" for unlimited memory usage

$_CONFIG['magic_number'] = 0.0123456789; //When subject score is equal to this magic number, only subject name will be shown and score will be hidden
?>