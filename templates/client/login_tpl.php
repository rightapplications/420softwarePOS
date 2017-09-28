<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title></title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
  <meta name="format-detection" content="telephone=no" />
  <meta http-equiv="x-rim-auto-match" content="none" />
  <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon" />
  <link rel="icon" href="../favicon.ico" type="image/x-icon" />
  <link rel="icon" href="../favicon.png" type="image/x-icon" />
  <link rel="stylesheet" href="../POS/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../POS/font-awesome-4.6.3/css/font-awesome.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="../POS/dist/css/admin.css">
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <script src="../POS/plugins/jQuery/jquery-2.2.3.min.js"></script>
  <script src="../POS/dist/js/jquery.formstyler.min.js"></script>
	<script type="text/javascript">
		//hljs.initHighlightingOnLoad();
		jQuery(document).ready(function(){
			if (navigator.userAgent.indexOf('Mac') != -1) {
				jQuery("body").addClass("mac");
			} else {
				jQuery("body").addClass("pc");
			}
		});
	   
	</script>
	
   
	  
	<!-- start select -->	
	
	<script>
		(function($) {
		$(function() {
			$('select').styler();
		})
		})(jQuery)
	</script>
	<!-- stop select -->
	
    <!-- start radio checkbox -->
	<script src="../POS/js/jcf.js"></script>
	<script src="../POS/js/jcf.radio.js"></script>
	<script src="../POS/js/jcf.checkbox.js"></script>
	<script>
		$(function() {
			jcf.replaceAll();
		});
	</script>
	<!-- stop radio checkbox -->
	<script type="text/javascript">
function sendData(){
     if($('#email').attr('value').replace(/ +/,'') !='' || $('#password').attr('value').replace(/ +/,'') !=''){
        document.login_form.submit();
    }
}
</script>
<style>
    .signin-container input[type="password"], .signin-container input[type="password"]:focus{background: none;}
</style>
</head>
<body class="">
    


<table class="signin-table">
 <tr>
 	<td>
		<div class="logo-sign-in"><a href="<?=HOST?>" title=""><img src="../POS/images/logo2.png" alt="" /></a></div>
		<div class="signin-container">
			<label>SIGN IN</label><div class="error"><?if(@$error) echo $error?></div>
			<h4>Client's Screen</h4>
                        <form action="#" method="post" name="login_form" class="singleForm">
                            <input type="hidden" name="sent" value="1" />
			<div class="form-group"><!-- +/- class incorrectly-filled class="form-group incorrectly-filled" -->
				<label>Email <font>*</font></label>
				<div class="input-signin"><input type="email" name="email" id="email" class="form-control" /></div>
			</div>
			<div class="form-group"><!-- +/- class incorrectly-filled class="form-group incorrectly-filled" -->
				<label>Password <font>*</font></label>
				<div class="input-signin"><input type="password" name="password" id="password" class="form-control" /></div>
				
			</div>
			<div class="form-group">
				<div class="submit-button"><button class="button" onclick="sendData();">Sign in</button></div>
			</div>
                        </form>
		</div>
	</td>
 </tr>
</table>

<?$cd = getdate()?>
<div class="div-footer">Copyright &copy; <?=$cd['year']?></div>

<script src="../POS/bootstrap/js/bootstrap.min.js"></script>
<script src="../POS/dist/js/app.min.js"></script>
</body>
</html>