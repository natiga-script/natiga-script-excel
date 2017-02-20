<?php
if(!defined('TEMP_PATH')){die('Forbidden');}
include('header.php');

if(isset($page_content) && is_file(TEMP_PATH.'/'.$page_content) && is_readable(TEMP_PATH.'/'.$page_content))
{
    include(TEMP_PATH.'/'.$page_content);
}

include('footer.php');

?>