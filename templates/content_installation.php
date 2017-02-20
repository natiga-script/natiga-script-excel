<?php if(!defined('TEMP_PATH')){die('Forbidden');}
$temp_url = !empty($_temp_vars['temp_url'])?$_temp_vars['temp_url']:'templates/assets/';
$_temp_vars['extra_footer'] = sprintf('<script type="text/javascript" src="%sjs/install_func.min.js"></script>',$temp_url); 
?>
	<!-- Banner -->
				<section id="banner">
					<div class="content">
						<header>
							<h2>مرحبًا بك!</h2>
							<p>برجاء اتباع الخطوات الآتية لتثبيت سكربت النتيجة <br /> 
                            بشكل صحيح.</p>
						</header>
						<span class="image"><img src="<?= $temp_url;?>images/installation_package.jpg" alt="" /></span>
					</div>
					<a href="#upload_file" class="goto-next scrolly">Next</a>
				</section>
<!-- One -->
				<section id="upload_file" class="spotlight style1 bottom">
					<span class="image fit main"><img src="<?=$temp_url;?>images/code_bg.jpg" alt="" /></span>
					<div class="content">
						<div class="container">
							<div class="row">
								<div class="4u 12u$(medium)">
									<header>
										<h2> قم برفع ملف الـExcel</h2>
										<p>اختر ملف الـExcel الذي يحتوي على كافة بيانات الطلاب ودرجاتهم</p>
									</header>
								</div>
								<div class="8u$ 12u$(medium)" style="text-align: center;">
                                    <div id="upload_result" <?= (empty($_POST['action']) || $_POST['action'] !== 'file_upload')?'style="display: none;"':'';?>>
                                    <?= (!empty($_POST['action']) && $_POST['action']=='file_upload' && isset($_result))?get_alert($_result['message'],$_result['status']):'';?>
                                    </div>
                                    <form action="" id="file_upload" style="text-align: center;">
                                        <input type="file" name="excel_file" id="excel_file" style="width: 0.1px;height: 0.1px;	opacity: 0;	overflow: hidden;	position: absolute;	z-index: -1;" />
                                        <br />
                                        <label for="excel_file" class="button" id="upload_btn">رفع الملف</label>
                                    </form>
                                    <h4 >ملحوظة: قد تأخد عملية رفع ومعالجة الملف بعض الوقت إذا كان حجم الملف كبير</h4>
								</div>
							</div>
						</div>
					</div>
					<a href="#sheet_config" class="goto-next scrolly">Next</a>
				</section>
<!----------- two ------->
<form action="" method="post" data-success-callback="save_data_callback" style="position: relative;">
                <section id="sheet_config" class="wrapper style3" style="position: relative;min-height: 100vh;padding: 3em;">
                    <!--<span  class="fit image main"><img src="<?=$temp_url;?>images/pic02.jpg" alt="" /></span>-->
                    <div class="overlay">
                        <img class="circle" src="<?=$temp_url;?>images/help_icon.jpg" alt="Hint" style="width: 5em;" /><br /><br />
                        <p>من فضلك، قم برفع ملف أولا</p>
                    </div>
                    <div class="container">
                    <div class="row">
                        <div class="6u 12u$(medium)">
                        	<header>
    							<h2>ورقة العمل التي تحتوي على البيانات: </h2>
    							<p><select id="excel_sheet" name="excel_sheet"></select></p>
    						</header>
                            <div id="excel_sheet_content" class="table-wrapper">
                                <table class="alt">
                                    <tbody>
                                        <tr>
                                            <td style="width: 30%;">عمود رقم الجلوس: </td>
                                            <td><select class="sheet_column" name="seat_no_column"></select></td>
                                        </tr>
                                        <tr>
                                            <td>عمود الاسم: </td>
                                            <td><select class="sheet_column" name="name_column"></select></td>
                                        </tr>
                                        <tr>
                                            <td>عمود الصف الدراسي: </td>
                                            <td><select class="sheet_column" name="grade_column"></select></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="6u 12u$(medium)" style="text-align: center;">
                            <h3>الأعمدة التي لا تحتوي على درجات: </h3>
                            <p>يتم عرض هذه الأعمدة كفواصل كما هو موضح  <a href="<?=$temp_url;?>images/sep_cell_example.jpg" target="_blank">بالصورة </a>
                            <br />
                            يمكنك اختيار أكتر من عمود باستمرار الضغط على زر كنترول 
                            <span class="inline-btn" style="margin-top: 1em;">Ctrl</span>
                             أثناء التحديد
                            </p>
                            <!--<p style="text-align: center;"><a href="<?=$temp_url;?>images/sep_cell_example.jpg" target="_blank"><img src="<?=$temp_url;?>images/sep_cell_example.jpg" class="circle" style="width:10em;" /></a></p>-->
                            <p><select class="sheet_column" name="sep_columns[]" multiple="" style="min-height: 13.8em;"></select></p>
                        </div>
                    </div>
					</div>
                    <a href="#db_config" style="z-index: 101;" class="goto-next scrolly">Next</a>
                </section>
<!------- Three ----------->                
                <section id="db_config" class="spotlight style3 right">
                    <span  class="fit image main"><img src="<?=$temp_url;?>images/db_bg.jpg" alt="" /></span>
                    <div class="content" style="padding: 2.5em 3em;">
                    <div class="overlay">
                        <img class="circle" src="<?=$temp_url;?>images/help_icon.jpg" alt="Hint" style="width: 5em;" /><br /><br />
                        <p>من فضلك، قم برفع ملف أولا</p>
                    </div>
                    <?= (!empty($_POST['action']) && $_POST['action']=='save_data' && isset($_result))?get_alert($_result['message'],$_result['status']):'';?>
						<header>
							<h2>بيانات الاتصال بسيرفر قاعدة البيانات</h2>
						</header>
						<div class="table-wrapper">
                            <table class="alt">
                                <tbody>
                                    <tr>
                                        <td style="width: 30%;"><label for="hostname">سيرفر قاعدة البيانات: </label></td>
                                        <td><input type="text" name="hostname" placeholder="سيرفر قاعدة البيانات" /></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 30%;"><label for="username">اسم المستخدم: </label></td>
                                        <td><input type="text" name="username" placeholder="اسم المستخدم" /></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 30%;"><label for="password">كلمة المرور: </label></td>
                                        <td><input type="password" name="password" placeholder="كلمة المرور" /></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 30%;"><label for="dbname">اسم قاعدة البيانات: </label></td>
                                        <td><input type="text" name="dbname" placeholder="اسم قاعدة البيانات" /></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 30%;"><label for="prefix">البادئة (اختياري): </label></td>
                                        <td><input type="text" name="prefix" placeholder="بادئة لإضافتها لاسم الجدول" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <input type="hidden" name="action" value="save_data" />
                        <input type="hidden" name="csrf_token" value="<?= $_temp_vars['csrf_token'];?>" />
                        <input type="submit" class="button special" value="تثبيت "  style="float: left;" />
                     
					</div>
                    <a href="#finish" style="display: none;" class="goto-next scrolly">Next</a>
                </section>
                </form>   
<!------------- FOUR ----------------->
<section id="finish" class="wrapper style1 special fade-up" style="padding: 1.5em;display: none;">
					<div class="container">
						<header class="major">
							<img src="<?=$temp_url;?>images/done.png" class="circle" alt="finish!!"/>
                            <h2>تهانينا!!</h2>
                            <p>تمت عملية التثبيت بنجاح :)</p>
						</header>
                        <a href="./" class="button">الصفحة الرئيسيّة</a>
					</div>
				</section>