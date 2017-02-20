	<!-- Banner -->
				<section id="banner">
					<div class="content">
						<header>
							<h2>خطأ!!!</h2>
							<p>حدث خطأ أثناء محاولتنا لتنفيذ طلبك. <br />
                            برجاء المحاولة مرّة أخرى</p>
                            <?php if(!empty($_temp_vars['error_details'])){?>
                               <p><span style="color: #cfcfcf;">بيانات الخطأ:</span> <span style="color: #e44c65;"><?= $_temp_vars['error_details'];?></span></p>
                            <?php }?>
                            
						</header>
						<span class="image"><img src="<?= $temp_url;?>images/error.jpg" alt="" /></span>
					</div>
				</section>