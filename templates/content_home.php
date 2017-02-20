<?php if(!defined('TEMP_PATH')){die('Forbidden');}
$temp_url = !empty($_temp_vars['temp_url'])?$_temp_vars['temp_url']:'templates/assets/';

$successful_query = ($_result['status']=='success' && !empty($_result['data']));
$search_request = (!empty($_REQUEST['action']) && $_REQUEST['action']=='search' && !empty($_result['message']));

$search_by_key = ' الاسم أو رقم الجلوس ';
$search_by_example = 'أحمد محمد عبد العزيز ، أو 79526';
if(!empty($_temp_vars['search_by']) && is_array($_temp_vars['search_by'])){
    if($_temp_vars['search_by']['num'] == true && $_temp_vars['search_by']['name'] == false){
        $search_by_key = 'رقم الجلوس';
        $search_by_example = '79526';
    }else if($_temp_vars['search_by']['num'] == false && $_temp_vars['search_by']['name'] == true){
        $search_by_key = 'الاسم';
        $search_by_example = 'أحمد محمد عبد العزيز ';
    }
}
?>
	<!-- Banner -->
				<section id="banner">
					<div class="content">
						<header>
							<h2>مرحبًا بك!</h2>
							<p>الآن يمكنك معرفة نتيجتك<br /> بشكل سهل وبسيط.</p>
						</header>
						<span class="image"><img src="<?= $temp_url;?>images/paper.png" alt="" /></span>
					</div>
					<a href="#natiga" class="goto-next scrolly">Next</a>
				</section>
<!-- One -->
				<section id="natiga" class="spotlight style1 right">
					<span class="image fit main"><img src="<?=$temp_url;?>images/grad_wallpaper2.jpg" alt="" /></span>
					<div class="content">
                    <form action="./index.php" id="result_form" method="GET" data-success-callback="natiga_callback">
									<header>
										<h2>بيانات الطالب</h2>
										<p>قم بإدخال <?=$search_by_key;?> وصفّك الدراسي</p>
									</header>
                                        <div id="alert_zone" <?= (!$search_request)? 'style="display: none;"':'';?>><?=($search_request)?get_alert($_result['message'],$_result['status']):'';?></div>
                                        <?php if($search_request && !$successful_query){ ?>
                                        <script type="text/javascript">
                                            document.querySelector('#banner a.goto-next').click();
                                        </script>
                                        <?php };?>
                                        <div class="table-wrapper">
                                                <table class="alt">
                                                    <tr>
                                                        <td style="width: 30%;"><label for="stdInfo"><?= str_replace('أو','/',$search_by_key);?></label></td>
                                                        <td><input type="text" name="stdInfo" placeholder="مثال: <?= $search_by_example;?>" /></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 30%;"><label for="grade">الصف الدراسي: </label></td>
                                                        <td><select name="grade">
                                                        <?= ($_CONFIG['allow_search_all_grades'])?'<option value="">أي صف</option>':'';?>
                                                        <?php if(!empty($_temp_vars['grades']) && is_array($_temp_vars['grades'])){
                                                            foreach($_temp_vars['grades'] as $grade){
                                                                echo sprintf('<option value="%s">%s</option>',Natiga_Security::escapeAttribute($grade),Natiga_Security::escapeHTML($grade));
                                                            }
                                                        }?>
                                                        </select></td>
                                                    </tr>
                                                </table>
                                                <input type="hidden" name="action" value="search" />
                                                <input type="submit" class="button special" style="float: left;" value="بحث" />
    							</div>
                            </form>
					</div>
					<a href="#student_result" <?= (!$successful_query)?'style="display: none;"':'';?> class="goto-next scrolly">Next</a>
				</section>
                <!---------- Student Result Section ------------>
                <section id="student_result" class="wrapper style1 fade-down" <?= (!$successful_query)?'style="display: none;"':'';?>>
                <?= ($successful_query)?$_result['data']:'';?>
                </section>
