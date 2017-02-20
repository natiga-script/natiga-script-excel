<?php if(!defined('TEMP_PATH')){die('Forbidden');}
$title = !empty($_temp_vars['title'])?$_temp_vars['title']:'النتيجة';
$temp_url = !empty($_temp_vars['temp_url'])?$_temp_vars['temp_url']:'templates/assets/';
$_temp_vars['csrf_token'] = (!empty($_temp_vars['csrf_token']))?$_temp_vars['csrf_token']:'';
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title><?= $title;?></title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf_token" content="<?= $_temp_vars['csrf_token'];?>" />
        <link rel="shortcut icon" href="<?=$temp_url;?>images/favicon.ico" type="image/x-icon" />
        <link rel="apple-touch-icon" href="<?=$temp_url;?>images/apple-touch-icon.png" />
        <link rel="apple-touch-icon" sizes="57x57" href="<?=$temp_url;?>images/apple-touch-icon-57x57.png" />
        <link rel="apple-touch-icon" sizes="72x72" href="<?=$temp_url;?>images/apple-touch-icon-72x72.png" />
        <link rel="apple-touch-icon" sizes="76x76" href="<?=$temp_url;?>images/apple-touch-icon-76x76.png" />
        <link rel="apple-touch-icon" sizes="114x114" href="<?=$temp_url;?>images/apple-touch-icon-114x114.png" />
        <link rel="apple-touch-icon" sizes="120x120" href="<?=$temp_url;?>images/apple-touch-icon-120x120.png" />
        <link rel="apple-touch-icon" sizes="144x144" href="<?=$temp_url;?>images/apple-touch-icon-144x144.png" />
        <link rel="apple-touch-icon" sizes="152x152" href="<?=$temp_url;?>images/apple-touch-icon-152x152.png" />
        <link rel="apple-touch-icon" sizes="180x180" href="<?=$temp_url;?>images/apple-touch-icon-180x180.png" />
		<!--[if lte IE 8]><script src="<?= $temp_url;?>js/ie/html5shiv.js"></script><![endif]-->
		<link rel="stylesheet" href="<?= $temp_url;?>css/main.min.css" />
		<!--[if lte IE 9]><link rel="stylesheet" href="<?= $temp_url;?>css/ie9.min.css" /><![endif]-->
		<!--[if lte IE 8]><link rel="stylesheet" href="<?= $temp_url;?>css/ie8.min.css" /><![endif]-->
        <script type="text/javascript">
        function escapeHTML(unsafe) {
            unsafe = (unsafe != null)?unsafe.toString():'';
            return unsafe
                 .replace(/&/g, "&amp;")
                 .replace(/</g, "&lt;")
                 .replace(/>/g, "&gt;")
                 .replace(/"/g, "&quot;")
                 .replace(/'/g, "&#039;");
         }
        function get_alert(message,type="error")
        {
            return `<div class='alert ${escapeHTML(type)}'>${message}</div>`;
        }
        XMLHttpRequest.prototype.send_bak = XMLHttpRequest.prototype.send;
        XMLHttpRequest.prototype.send = function(data) {
            csrf_token = ((meta = document.querySelector('meta[name=csrf_token]')) !== null)? meta.getAttribute('content'):'';
            this.setRequestHeader('X-CSRF-TOKEN', csrf_token);
            this.send_bak(data);
        };
        </script>
	</head>
	<body class="landing">
		<div id="page-wrapper">

			<!-- Header -->
				<header id="header">
					<h1 id="logo"><a href="./"><?= $title;?></a></h1>
					<nav id="nav">
						<ul>
							<li><a href="./">الرئيسية</a></li>
							<li><a href="#natiga" class="button special scrolly">اعرف نتيجتك</a></li>
						</ul>
					</nav>
				</header>