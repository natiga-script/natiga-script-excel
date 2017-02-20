<?php

/**
 * @author Abood Nour
 * @copyright 2017
 */
require ('global.php');
require_once (INC_PATH . '/class.natiga.fetcher.php');
if (!is_script_installed())
{
    die_error('يبدو أنك لم تقم بتثبيت السكربت بعد <br/>أو أنه تم مسح بعض الملفات الهامة.. من فضلك قم بتثبيته أولا<br/><br/><a class="button" href="./installer.php">بدء التثبيت</a>');
}

if (empty($PDO))
{
    require_once (INC_PATH . '/config.php');
}

if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'search')
{

    //Set default response to error to handle unexpected failures
    $_result = array(
        'status' => 'error',
        'message' => 'حدث خطأ أثناء محاولة تنفيذ طلبك.. <br/>برجاء المحاولة مرّة أخرى',
        'data' => array());

    if (isset($_REQUEST['stdInfo']))
    {
        //If user entered a name of less than 3 chars return error
        if (!is_numeric($_REQUEST['stdInfo']) && mb_strlen(trim($_REQUEST['stdInfo'])) < 3)
        { //We changed our code writing style in this condition for better readability of the code
            $_result['message'] = 'اسم الطالب الذي تبحث عنه قصير جدًا.. يجب أن يكون على الأقل 3 حروف';

        } else
        {
            $grade = (!empty($_REQUEST['grade'])) ? trim($_REQUEST['grade']) : '';
            $search_config = array(
                'allow_partial_names' => $_CONFIG['allow_partial_names'],
                'allow_partial_grades' => $_CONFIG['allow_partial_grades'],
                'allow_search_all_grades' => $_CONFIG['allow_search_all_grades'],
                'search_by' => $_CONFIG['search_by']);

            $natiga_instance = new Natiga_Fetcher($_REQUEST['stdInfo'], $grade, $search_config);
            $result = $natiga_instance->get_result();
            //var_dump($result->get_statement()->fetch());
            if ($result && ($data = $result->fetch(PDO::FETCH_ASSOC)) && !empty($data))
            {
                //Checks for existence and readbility of headers file, exists in is_script_installed() function
                $headers = json_decode(file_get_contents(INC_PATH . '/' . TB_HEADER_FILENAME), true);

                unset($data['stdID']); //remove student ID from result
                $_temp_vars['student_result'] = array('headers' => $headers, 'data' => $data);

                $_result['status'] = 'success';
                $_result['message'] = 'تم العثور على بيانات الطالب بنجاح';
                $_result['data'] = parse_as_template(TEMP_PATH . '/content_result.php');

            } else
            {
                $_result['message'] = 'لم نستطع العثور على طالب بالبيانات التي أدخلتها.. <br/> من فضلك تأكد من إدخال البيانات بشكل صحيح';
            }
        }
    } else
    {
        $_result['message'] = 'لم تقم بتقديم بيانات كافية عن الطالب. من فضلك أدخل الاسم أو رقم الجلوس';
    }
}

$_temp_vars['grades'] = $PDO->query('SELECT DISTINCT `grade` from `' . DB_PREFIX . 'sheet_schema`')->fetchAll(PDO::FETCH_COLUMN);
$_temp_vars['search_by'] = array('num'=>false,'name'=>false);
if($_CONFIG['search_by'] & Natiga_Fetcher::SEARCH_BY_NUM){
    $_temp_vars['search_by']['num'] = true;
}
if($_CONFIG['search_by'] & Natiga_Fetcher::SEARCH_BY_NAME){
    $_temp_vars['search_by']['name'] = true;
}


if (is_ajax_request())
{
    header('Content-Type: application/json; charset=utf8');
    die(json_encode($_result));
} else
{
    /*if ($_result['status'] === 'success')
    {
        $page_content = 'content_home.php';
    } else
    {
        $_temp_vars['error_details'] = $_result['message'];
        $page_content = 'error.php';
    }*/
    $page_content = 'content_home.php';
    include_once (TEMP_PATH . '/main.php');
}

?>