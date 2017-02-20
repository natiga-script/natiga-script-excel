<?php

/**
 * @author Abood Nour
 * @copyright 2017
 */


require_once ('global.php');
//Set page title
$_temp_vars['title'] = 'تثبيت سكربت النتيجة ';

//config.php.bak template content just in case it got deleted
$config_bak_base64 =
    'PD9waHANCi8vZGF0YWJhc2UgaG9zdG5hbWUNCmRlZmluZSgnREJfSE9TVCcsJ3t7ZGJfaG9zdG5hbWV9fScpOw0KLy9kYXRhYmFzZSB1c2VyDQpkZWZpbmUoJ0RCX1VTRVInLCd7e2RiX3VzZXJuYW1lfX0nKTsNCi8vZGF0YWJhc2UgdXNlciBwYXNzd29yZA0KZGVmaW5lKCdEQl9QQVNTJywne3tkYl9wYXNzd29yZH19Jyk7DQovL2RhdGFiYXNlIG5hbWUNCmRlZmluZSgnREJfTkFNRScsJ3t7ZGJfbmFtZX19Jyk7DQovL2RhdGFiYXNlIHRhYmxlIG5hbWVzIHByZWZpeA0KZGVmaW5lKCdEQl9QUkVGSVgnLCd7e2RiX3ByZWZpeH19Jyk7DQovL1RhYmxlIEhlYWRlciBTdG9yYWdlIEZpbGUNCmRlZmluZSgnVEJfSEVBREVSX0ZJTEVOQU1FJywnaGVhZGVycy5qc29uJyk7DQoNCmlmKCFpc3NldCgkUERPKSl7DQogICAgdHJ5ew0KICAgICAgICAkUERPID0gbmV3IFBETyhzcHJpbnRmKCdteXNxbDpob3N0PSVzO2RibmFtZT0lcztjaGFyc2V0PXV0Zjg7JyxEQl9IT1NULERCX05BTUUpLERCX1VTRVIsREJfUEFTUyk7DQogICAgfWNhdGNoKFxQRE9FeGNlcHRpb24gJGV4KXsNCiAgICAgICAgZGllKCdVbmFibGUgdG8gY29ubmVjdCB0byBkYXRhYmFzZScpOw0KICAgIH0NCn0NCj8+';

//if page is requested over POST, check if we have something to do
if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST')
{
    if (!empty($_POST['action']))
    {
        //Include PHP Excel Reader to parse uploaded file
        require_once (INC_PATH . '/class.reader.php');

        //Set default response to error to handle unexpected failures
        $_result = array(
            'status' => 'error',
            'message' => 'حدث خطأ أثناء محاولة تنفيذ طلبك.. <br/>برجاء المحاولة مرّة أخرى',
            'data' => array());

        //Excel File upload handling
        if ($_POST['action'] === 'file_upload')
        {
            //Check that user uploaded a file
            if (isset($_FILES) && !empty($_FILES['excel_file']))
            {
                //Check that we only have one file that has been uploaded without errors
                if (count($_FILES['excel_file']['error']) === 1 && $_FILES['excel_file']['error'] === UPLOAD_ERR_OK && is_uploaded_file($_FILES['excel_file']['tmp_name']))
                {
                    //Validate uploaded file extension and mime type
                    $file = $_FILES['excel_file'];
                    $ext = strtolower(@array_pop(explode('.', $file['name'])));
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimetype = finfo_file($finfo, $file['tmp_name']);
                    if (in_array($ext, $_CONFIG['allowed_file_ext'], true) && in_array($mimetype, $_CONFIG['allowed_file_mime'], true))
                    {
                        $filename = $file['tmp_name'];
                        //Try to read uploaded excel file
                            try{
                                $reader = new Natiga_Reader($filename);
                                if ($reader->hasNoErrors())
                                {
                                    //Retrieve only one row from each sheet to let user select proper values without consuming much memory
                                    if (($file_content = $reader->getAllSheets()->toArray(1)) !== Natiga_Reader::$empty_sheet_array)
                                    {
                                        //Change response status and message to show success
                                        $_result['data'] = $file_content;
                                        $_result['status'] = 'success';
                                        $_result['message'] = 'تم قراءة الملف بنجاح.. قم باختيار البيانات أدناه';
                                        $reader->closeSession();
                                        //Rename uploaded file but keep it in temp, so we can read it later without asking user to reupload
                                        $tmp_name = tempnam(sys_get_temp_dir(), 'natiga_file_');
                                        if (move_uploaded_file($file['tmp_name'], $tmp_name))
                                        {
                                            //if user has previously uploaded a file, remove old ones
                                            if (!empty($_SESSION['last_successful_filename']) && is_file($_SESSION['last_successful_filename']))
                                            {
                                                unlink($_SESSION['last_successful_filename']); //remove old one if exists
                                            }
                                            //Save temp file name in session
                                            $_SESSION['last_successful_filename'] = $tmp_name;
                                        }
                                    } else
                                    {
                                        $_result['message'] = 'الملف الذي قمت برفعه لا يحتوي على أيّة  بيانات.. برجاء التأكد من وضع البيانات في الملف';
                                    }
                                } else
                                {
                                    $_result['message'] = "حدث خطأ أثناء محاولة قراءة ملف الإكسل!!";
                                }
                        }catch(Exception $e){
                            $_result['message'] = "حدث خطأ أثناء محاولة قراءة ملف الإكسل!!<br/> قد يكون الملف تالف أو ضخم.. حاول مرّة أخرى أو جرّب ملف أخر";
                        }
                    } else
                    {
                        $_result['message'] = 'الملف الذي تحاول رفعه غير مسموح به.. من فضلك قم برفع ملفات Excel فقط';
                    }
                }
            } else
            {
                $_result['message'] = 'لم نتلق أيّ ملفات في طلبك.. برجاء رفع الملف مرة أخرى';
            }

            //Saving data into database
        } elseif ($_POST['action'] === 'save_data')
        {
            //Check that user has selected column values and entered dbs credentials
            if (isset($_POST['excel_sheet']) && isset($_POST['seat_no_column']) && isset($_POST['name_column']) && isset($_POST['grade_column']) && !empty($_POST['hostname']) && !empty($_POST['username']) && !
                empty($_POST['password']) && !empty($_POST['dbname']))
            {
                //Check if we still have access to uploaded excel file
                if (!empty($_SESSION['last_successful_filename']) && is_file($_SESSION['last_successful_filename']))
                {
                    $filename = $_SESSION['last_successful_filename'];
                    
                    //We moved attempt to connect to DB server before reading file to save resources in case of large excel files
                    try{
                        //Attempt to connect to DB
                        $hostname = Natiga_Security::sanitize_PDO_DSN($_POST['hostname']);
                        $dbname = Natiga_Security::sanitize_PDO_DSN($_POST['dbname']);
                        $prefix = (!empty($_POST['prefix']) && trim(Natiga_Security::alphanum($_POST['prefix'])) != '') ? trim(Natiga_Security::alphanum($_POST['prefix'])) : 'natiga_';
                        $PDO = new PDO(sprintf('mysql:host=%s;dbname=%s;charset=utf8', $hostname, $dbname), $_POST['username'], $_POST['password']);
                    }catch(PDOException $ex){
                            $_result['message'] = 'فشل الاتصال بقاعدة البيانات.. من فضلك تأكد من البيانات التي قمت بإدخالها';
                    }
                    
                    //If db connection is established
                    if(isset($PDO) && $PDO instanceof PDO){
                        //Try to read excel file
                        $reader = new Natiga_Reader($filename);
                        if (is_object($reader->getExcelObj()))
                        {
                            //Make sure that user has supplied Integer value for sheet index
                            $sheetIndex = Natiga_Security::toInt($_POST['excel_sheet']);
                            //Retrieve this sheet data and populate it in an array
                            if (($file_content = $reader->getSheetByIndex($sheetIndex)->toArray()) !== Natiga_Reader::$empty_sheet_array)
                            {
                                //Validate that user indices are within range
                                $columnIndices = array();
                                $file_data = (isset($file_content[0]['data'])) ? $file_content[0]['data'] : null;
                                $reader->closeSession();
                                $columnIndices['seat_no'] = Natiga_Security::toInt($_POST['seat_no_column']);
                                $columnIndices['name'] = Natiga_Security::toInt($_POST['name_column']);
                                $columnIndices['grade'] = Natiga_Security::toInt($_POST['grade_column']);
                                if (!is_null($file_data) && count($file_data) > 0 && $columnIndices['seat_no'] < count($file_data[0]) && $columnIndices['name'] < count($file_data[0]) && $columnIndices['grade'] < count($file_data[0]))
                                {
                                    if (count(array_unique($columnIndices)) === 3)
                                    {
                                        $columnIndices['sep_columns'] = (!empty($_POST['sep_columns']) && is_array($_POST['sep_columns'])) ? array_map(array('Natiga_Security', 'toInt'), $_POST['sep_columns']) : array();
                                        try
                                        {
                                            //Create/Update config file
                                            $fh = fopen(INC_PATH . '/config.php', 'w');
                                            $template = file_exists(INC_PATH . '/config.php.bak') ? file_get_contents(INC_PATH . '/config.php.bak') : base64_decode($config_bak_base64);
                                            $replacements = array(
                                                '{{db_hostname}}' => addslashes($hostname),
                                                '{{db_username}}' => addslashes($_POST['username']),
                                                '{{db_password}}' => addslashes($_POST['password']),
                                                '{{db_name}}' => $dbname,
                                                '{{db_prefix}}' => $prefix);
                                            fwrite($fh, str_ireplace(array_keys($replacements), array_values($replacements), $template));
                                            fclose($fh);
    
    
                                            //create tables and prepare DB
                                            define('CALLED_FROM_INSTALL', true);
                                            require_once (INC_PATH . '/db.install.php');
                                        }
                                        catch (exception $ex)
                                        {
                                            if ($ex instanceof \PDOException)
                                            {
                                                $_result['message'] = 'حدث خطأ أثناء إعداد قاعدة البيانات. <br/>بيانات الخطأ: '.$PDO->errorInfo();
                                            } else
                                            {
                                                $_result['message'] = 'حدث خطأ أثناء تثبيت السكربت.. من فضلك قم بالتواصل مع المطور';
                                            }
                                        }
                                    } else
                                    {
                                        $_result['message'] = 'لقد قمت باختيار نفس العمود لأكثر من حقل.. <br/> من فضلك أعد المحاولة';
                                    }
                                } else
                                {
                                    $_result['message'] = 'لا توجد بيانات كافية في الملف.. <br/> إما أنك لم تقم بإضافة بيانات كافية أو أنك اخترت أعمدة غير موجودة';
                                }
                            }
                        } else
                        {
                            $_result['message'] = "حدث خطأ أثناء محاولة قراءة ملف الإكسل!!";
                        }
                    }else{
                        $_result['message'] = 'فشل الاتصال بقاعدة البيانات.. من فضلك تأكد من البيانات التي قمت بإدخالها';
                    }
                } else
                {
                    $_result['message'] = 'لم نستطع العثور على الملف الذي قمت برفعه.. من فضلك قم برفع الملف مرة أخرى';
                }
            } else
            {
                $_result['message'] = 'لقد تركت أحد الحقول فارغة.. <br/> من فضلك تأكد من ملء كافة الحقول وحاول مرة أخرى';
            }
        }
    }

}

if (is_ajax_request())
{
    header('Content-Type: application/json; charset=utf8');
    die(json_encode($_result));
} else
{
    if ($_result['status'] === 'success')
    {
        $page_content = 'content_installation.php';
    } else
    {
        $_temp_vars['error_details'] = $_result['message'];
        $page_content = 'error.php';
    }
    include_once (TEMP_PATH . '/main.php');
}

?>
