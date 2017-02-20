<?php

/**
 * @author Abood Nour
 * @copyright 2017
 */

require_once ('global.php');

$_result['message'] = false;

if(!empty($_GET['code'])){
    switch(abs(intval($_GET['code']))){
        case 404:
            $_result['message'] = 'لم نستطع العثور على الصفحة التي تحاول الوصول إليها<br/>من فضلك تأكد من اتباع رابط صحيح';
            break;
        case 403:
            $_result['message'] = 'يبدو أنك لا تمتلك صلاحيات كافية للوصول إلى هذه الصفحة';
            break;
        case 500:
            $_result['message'] = 'حدث خطأ أثناء معالجة طلبك ونحن نقوم بإصلاحه الآن';
            break;
        case 401:
            $_result['message'] = 'تحتاج تصريح للوصول إلى هذه الصفحة';
            break;
        case 400:
            $_result['message'] = 'هناك شيئ خاطئ في طلبك.. <br/> قم بإعادة المحاولة مرة أخرى';
            break;
        default:
            $_result['message'] = false;
    }
}
if($_result['message'] !== false){
    $_temp_vars['error_details'] = $_result['message'];
}
$page_content = 'error.php';

include_once (TEMP_PATH . '/main.php');

?>