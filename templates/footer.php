<?php if(!defined('TEMP_PATH')){die('Forbidden');}
$temp_url = !empty($_temp_vars['temp_url'])?$_temp_vars['temp_url']:'templates/assets/';
?>
	<!-- Footer -->
				<footer id="footer">
					<ul class="icons">
                        <li><a href="https://www.instagram.com/abood.nour/" target="_blank" class="icon alt fa-github"><span class="label">Instagram</span></a></li>
						<li><a href="https://twitter.com/aboodnour" target="_blank" class="icon alt fa-twitter"><span class="label">Twitter</span></a></li>
						<li><a href="https://fb.me/abood.nour" target="_blank" class="icon alt fa-facebook"><span class="label">Facebook</span></a></li>
						<li><a href="https://www.linkedin.com/in/aboodnour" target="_blank" class="icon alt fa-linkedin"><span class="label">LinkedIn</span></a></li>
						<li><a href="https://www.instagram.com/abood.nour/" target="_blank" class="icon alt fa-instagram"><span class="label">Instagram</span></a></li>
					</ul>
					<ul class="copyright">
						<li>تم التطوير بواسطة <a href="https://twitter.com/aboodnour" target="_blank">Abood Nour</a></li>
                        <li>قالب التصميم مقدم من &nbsp;<a href="http://html5up.net" target="_blank">HTML5 UP</a></li>
					</ul>
				</footer>

		</div>

		<!-- Scripts -->
			<script src="<?= $temp_url;?>js/jquery.min.js"></script>
			<!--<script src="<?= $temp_url;?>js/jquery.dropotron.min.js"></script> [Enable it if you need to add dropdown menus in nav bar]--> 
			<!--[if lte IE 8]><script src="<?= $temp_url;?>js/ie/respond.min.js"></script><![endif]-->
			<script src="<?= $temp_url;?>js/all.min.js"></script>
            <script src="<?= $temp_url;?>js/main.js"></script>
            <?=(isset($_temp_vars['extra_footer']))?$_temp_vars['extra_footer']:'';?>
            <script>
              (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
              (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
              m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
              })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
            
              ga('create', 'UA-92183371-1', 'auto');
              ga('send', 'pageview');
            
            </script>
            
	</body>
</html>