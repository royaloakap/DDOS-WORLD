<?php
ob_start();
include 'controls/database.php';
if (!($user -> LoggedIn()))
{
	header('location: login.php');
	die();
}
if (!($user -> notBanned($odb)))
{
	header('location: login.php');
	die();
}
$page = "Terms Of Service";
include ("head.php");
?>
<body>
		<?php include "header.php"; ?>
	
		<div class="container-fluid content">
		<div class="row">
				
			<?php include "side.php"; ?>
			<!-- end: Main Menu -->
						
			<!-- start: Content -->
			<div class="col-md-10 col-sm-11 main ">
			
			
			
			<div class="row">
				
				<div class="col-md-12">
					<div class="panel panel-default">
						
						<div class="panel-body padding-horizontal">
							<ol style="list-style-type:decimal">
			<h2>
	Web Site Terms and Conditions of Use
</h2>

<h3>
	1. Terms
</h3>

<p>
	By accessing this web site, you are agreeing to be bound by these 
	web site Terms and Conditions of Use, all applicable laws and regulations, 
	and agree that you are responsible for compliance with any applicable local 
	laws. If you do not agree with any of these terms, you are prohibited from 
	using or accessing this site. The materials contained in this web site are 
	protected by applicable copyright and trade mark law.
</p>

<h3>
	2. Use License
</h3>

<ol type="a">
	<li>
		Permission is granted to temporarily download one copy of the materials 
		(information or software) on FruitStresser web site for personal, 
		non-commercial transitory viewing only. This is the grant of a license, 
		not a transfer of title, and under this license you may not:
		
		<ol type="i">
			<li>modify or copy the materials;</li>
			<li>use the materials for any commercial purpose, or for any public display (commercial or non-commercial);</li>
			<li>attempt to decompile or reverse engineer any software contained on FruitStresser web site;</li>
			<li>remove any copyright or other proprietary notations from the materials; or</li>
			<li>transfer the materials to another person or "mirror" the materials on any other server.</li>
		</ol>
	</li>
	<li>
		This license shall automatically terminate if you violate any of these restrictions and may be terminated by FruitStresser at any time. Upon terminating your viewing of these materials or upon the termination of this license, you must destroy any downloaded materials in your possession whether in electronic or printed format.
	</li>
</ol>

<h3>
	3. Disclaimer
</h3>

<ol type="a">
	<li>
		The materials on FruitStresser web site are provided "as is". FruitStresser makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties, including without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights. Further, FruitStresser does not warrant or make any representations concerning the accuracy, likely results, or reliability of the use of the materials on its Internet web site or otherwise relating to such materials or on any sites linked to this site.
	</li>
</ol>

<h3>
	4. Limitations
</h3>

<p>
	In no event shall FruitStresser or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption,) arising out of the use or inability to use the materials on FruitStresser Internet site, even if FruitStresser or a FruitStresser authorized representative has been notified orally or in writing of the possibility of such damage. Because some jurisdictions do not allow limitations on implied warranties, or limitations of liability for consequential or incidental damages, these limitations may not apply to you.
</p>
			
<h3>
	5. Revisions and Errata
</h3>

<p>
	The materials appearing on FruitStresser web site could include technical, typographical, or photographic errors. FruitStresser does not warrant that any of the materials on its web site are accurate, complete, or current. FruitStresser may make changes to the materials contained on its web site at any time without notice. FruitStresser does not, however, make any commitment to update the materials.
</p>

<h3>
	6. Links
</h3>

<p>
	FruitStresser has not reviewed all of the sites linked to its Internet web site and is not responsible for the contents of any such linked site. The inclusion of any link does not imply endorsement by FruitStresser of the site. Use of any such linked web site is at the user's own risk.
</p>

<h3>
	7. Site Terms of Use Modifications
</h3>

<p>
	FruitStresser may revise these terms of use for its web site at any time without notice. By using this web site you are agreeing to be bound by the then current version of these Terms and Conditions of Use.
</p>

<h3>
	8. Governing Law
</h3>

<p>
	Any claim relating to FruitStresser web site shall be governed by the laws of the State of Kansas without regard to its conflict of law provisions.
</p>

<p>
	General Terms and Conditions applicable to Use of a Web Site.
</p>



<h2>
	Privacy Policy
</h2>

<p>
	Your privacy is very important to us. Accordingly, we have developed this Policy in order for you to understand how we collect, use, communicate and disclose and make use of personal information. The following outlines our privacy policy.
</p>

<ul>
	<li>
		Before or at the time of collecting personal information, we will identify the purposes for which information is being collected.
	</li>
	<li>
		We will collect and use of personal information solely with the objective of fulfilling those purposes specified by us and for other compatible purposes, unless we obtain the consent of the individual concerned or as required by law.		
	</li>
	<li>
		We will only retain personal information as long as necessary for the fulfillment of those purposes. 
	</li>
	<li>
		We will collect personal information by lawful and fair means and, where appropriate, with the knowledge or consent of the individual concerned. 
	</li>
	<li>
		Personal data should be relevant to the purposes for which it is to be used, and, to the extent necessary for those purposes, should be accurate, complete, and up-to-date. 
	</li>
	<li>
		We will protect personal information by reasonable security safeguards against loss or theft, as well as unauthorized access, disclosure, copying, use or modification.
	</li>
	<li>
		We will make readily available to customers information about our policies and practices relating to the management of personal information. 
	</li>
</ul>

<p>
	We are committed to conducting our business in accordance with these principles in order to ensure that the confidentiality of personal information is protected and maintained. 
</p>		

<h2>Final Thoughts And Information </h2>
The above TOS is legally binding. In addition, the following is legally binding.
<ul>
	<li> You may not charge back any payment for any reason. </lI>
	<li> You may not claim that you were hacked. We track IPs and any information will be submitted to PayPal upon a dispute. </li>
	<li> We offer refunds at our discretion. We can refuse anyone a refund. </li>
	<li> We offer a virtual service. This is not covered under the PayPal Buyer Protection. </li>
	<li> Attempts to deface, 'hack' or otherwise harm the webpage results in your account being suspended. </li>
	<li> We can suspend anyone for any reason and not provide the reason. </li>
	<li> If we suspend you, we retain the right to keep the payment you sent. </li>
	<li> We do not have to answer any support ticket or support request. </li>
	<li> We do not have to provide support for our service. </li>
	<li> We are in no way responsible for anyone or anything that you target with this stresser. </li>
	<li> We make the assumption you are granted LEGAL AND WRITTEN consent to target a website or another person. </li>
	<li> Essentially we are able to deny anyone service for any reason, not respond to support for whatever reason, and you need consent to 'hit someone off'. 
</ul>

In addition, these are the terms of service that can be found on the registration page. By signing up you agree to them.
By buying an account you agree to every term of service. <br>
    Do not share your accounts.</br>
    Do not abuse our system/service.</br>
    Do not re-sell your account.</br>
    We are obliged to and will provide any information requested by law enforcement.</br>
    You agree that you will use our system for legal and educational purposes only.</br>
    Do not attempt to exploit FruitStresser.</br>
    All sales are final.</br>
    Charging back will result in the termination of your account, and a scam report opened.</br>
    Chargebacks may cause a release of your information.</br>
    We are not held responsible for any of your actions.</br>
    We may change our terms of service, at any time with little notice.</br>
    We may ban you at any time for what ever reason.</br>
    By registering and using our service, you agree to our Terms of Service.

Thank you.
		</ol>
	</div>
</article>
</br>
</div>
						</div>
					</div>	
				</div><!--/col-->	
			</div><!--/row-->
		</div>
	</div>
	<div class="clearfix"></div>
	
	<?php include "footer.php"; ?>
		
	<!-- start: JavaScript-->
	<!--[if !IE]>-->

			<script src="assets/js/jquery-2.1.0.min.js"></script>

	<!--<![endif]-->

	<!--[if IE]>
	
		<script src="assets/js/jquery-1.11.0.min.js"></script>
	
	<![endif]-->

	<!--[if !IE]>-->

		<script type="text/javascript">
			window.jQuery || document.write("<script src='assets/js/jquery-2.1.0.min.js'>"+"<"+"/script>");
		</script>

	<!--<![endif]-->

	<!--[if IE]>
	
		<script type="text/javascript">
	 	window.jQuery || document.write("<script src='assets/js/jquery-1.11.0.min.js'>"+"<"+"/script>");
		</script>
		
	<![endif]-->
	<script src="assets/js/jquery-migrate-1.2.1.min.js"></script>
	<script src="assets/js/bootstrap.min.js"></script>	
	
	
	<!-- page scripts -->
	<script src="assets/js/jquery-ui.min.js"></script>
	<script src="assets/js/jquery.ui.touch-punch.min.js"></script>
	<script src="assets/js/jquery.sparkline.min.js"></script>
	<script src="assets/js/fullcalendar.min.js"></script>
	<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="assets/js/excanvas.min.js"></script><![endif]-->
	<script src="assets/js/jquery.flot.min.js"></script>
	<script src="assets/js/jquery.flot.pie.min.js"></script>
	<script src="assets/js/jquery.flot.stack.min.js"></script>
	<script src="assets/js/jquery.flot.resize.min.js"></script>
	<script src="assets/js/jquery.flot.time.min.js"></script>
	<script src="assets/js/jquery.flot.spline.min.js"></script>
	<script src="assets/js/jquery.autosize.min.js"></script>
	<script src="assets/js/jquery.placeholder.min.js"></script>
	<script src="assets/js/moment.min.js"></script>
	<script src="assets/js/daterangepicker.min.js"></script>
	<script src="assets/js/jquery.easy-pie-chart.min.js"></script>
	<script src="assets/js/jquery.dataTables.min.js"></script>
	<script src="assets/js/dataTables.bootstrap.min.js"></script>
	<script src="assets/js/raphael.min.js"></script>
	<script src="assets/js/morris.min.js"></script>
	<script src="assets/js/jquery-jvectormap-1.2.2.min.js"></script>
	<script src="assets/js/uncompressed/jquery-jvectormap-world-mill-en.js"></script>
	<script src="assets/js/uncompressed/gdp-data.js"></script>
	<script src="assets/js/gauge.min.js"></script>
	
	<!-- theme scripts -->
	<script src="assets/js/custom.min.js"></script>
	<script src="assets/js/core.min.js"></script>
	
	<!-- inline scripts related to this page -->
	<script src="assets/js/pages/index.js"></script>
	
	<!-- end: JavaScript-->
	
</body>
</html>