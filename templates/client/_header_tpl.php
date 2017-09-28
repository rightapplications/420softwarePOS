<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?=SITE_NAME?> Client's Screen</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
  <meta name="format-detection" content="telephone=no" />
  <meta http-equiv="x-rim-auto-match" content="none" />
  <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon" />
  <link rel="icon" href="../favicon.ico" type="image/x-icon" />
  <link rel="icon" href="../favicon.png" type="image/x-icon" />
  <link rel="stylesheet" href="<?=HOST?>POS/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?=HOST?>POS/font-awesome-4.6.3/css/font-awesome.css" />

  <style type="text/css" media="print">
  @page { size: landscape; }
  </style>
  <link rel="stylesheet" href="<?=HOST?>POS/css/print.css">
  <link rel="stylesheet" href="<?=HOST?>client/css/style.css">
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
   <script src="<?=HOST?>POS/plugins/jQuery/jquery-2.2.3.min.js"></script> 
   <script src="<?=HOST?>POS/dist/js/jquery.formstyler.min.js"></script> 
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
            if (!(/(iPad|iPhone|iPod)/g.test(navigator.userAgent))) {
                $('select').styler();
            }
		})
		})(jQuery)
	</script>
	<!-- stop select -->
	
    <!-- start radio checkbox -->
	<script src="<?=HOST?>POS/js/jcf.js"></script>
	<script src="<?=HOST?>POS/js/jcf.radio.js"></script>
	<script src="<?=HOST?>POS/js/jcf.checkbox.js"></script>
	<script>
		$(function() {
			jcf.replaceAll();
		});
	</script>
	<!-- stop radio checkbox -->

	<!-- || start calendar -->	
	  <link rel="stylesheet" href="<?=HOST?>POS/css/jquery-ui.css">
	  <script src="<?=HOST?>POS/js/jquery-ui.js"></script>
	  <script>
	  $(function() {
		$( "#from, #fromMobile" ).datepicker({
		  defaultDate: "",
		  changeMonth: true,
		  numberOfMonths: 1,
		  onClose: function( selectedDate ) {
			$( "#to, #toMobile" ).datepicker( "option", "minDate", selectedDate );
		  }
		});
		$( "#to, #toMobile" ).datepicker({
		  defaultDate: "",
		  changeMonth: true,
		  numberOfMonths: 1,
		  onClose: function( selectedDate ) {
			$( "#from, #fromMobile" ).datepicker( "option", "maxDate", selectedDate );
		  }
		});
	  });
	  </script>
	<!-- // stop calendar -->
        <style>
            .num-unread-messages{
                color:#f00;
                position:relative;
                top:-1px;
                left:5px;
            }
        </style>
        <script src="<?=HOST?>client/js/client.js"></script>
</head>
<body>