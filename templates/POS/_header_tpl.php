<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?=SITE_NAME?> Admin</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
  <meta name="format-detection" content="telephone=no" />
  <meta http-equiv="x-rim-auto-match" content="none" />
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
  <link rel="icon" href="favicon.ico" type="image/x-icon" />
  <link rel="icon" href="favicon.png" type="image/x-icon" />
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="font-awesome-4.6.3/css/font-awesome.css" />
  <link rel="stylesheet" href="dist/css/admin.css">
  <style type="text/css" media="print">
  @page { size: landscape; }
  </style>
  <link rel="stylesheet" href="css/print.css">
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
   <script src="plugins/jQuery/jquery-2.2.3.min.js"></script> 
   <script src="dist/js/jquery.formstyler.min.js"></script> 
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
                $('select:not(.noStyled)').styler();
            }
		})
		})(jQuery)
	</script>
	<!-- stop select -->
	
    <!-- start radio checkbox -->
	<script src="js/jcf.js"></script>
	<script src="js/jcf.radio.js"></script>
	<script src="js/jcf.checkbox.js"></script>
	<script>
		$(function() {
			jcf.replaceAll();
		});
	</script>
	<!-- stop radio checkbox -->

	<!-- || start calendar -->	
	  <link rel="stylesheet" href="css/jquery-ui.css">
	  <script src="js/jquery-ui.js"></script>
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
          $(document).ready(function(){
              $('#settingsSelector').change(function(){
                  $('#'+$(this).val()).modal();
                  $(this).val('');
                  $(this).trigger('refresh');
              });
              $('#settingsSelectorMobile').change(function(){
                  $('#'+$(this).val()).modal();
                  $(this).val('');
                  $(this).trigger('refresh');
                  $('.sidebar-toggle').click();
              });
              
              $('.viewall').each(function(){
                  $(this).click(function(){
                      $('.notmobile').removeClass('for-desktop');
                      $('.viewmobile').removeClass('hidden');
                      $('.viewall').addClass('hidden');
                      return false;
                  });
              });
              $('.viewmobile').each(function(){
                  $(this).click(function(){
                      $('.notmobile').addClass('for-desktop');
                      $('.viewmobile').addClass('hidden');
                      $('.viewall').removeClass('hidden');
                      return false;
                  });
              });
              /*$('.showactions').each(function(){
                   $(this).click(function(){
                      $('.actions').removeClass('for-desktop');
                      $(this).remove();
                      return false;
                   });
              });*/
          });
	  </script>
	<!-- // stop calendar -->
        <script src="js/main.js"></script>
        <style>
            .num-unread-messages{
                color:#f00;
                position:relative;
                top:-1px;
                left:5px;
            }
            <?php if(!$_SESSION[CLIENT_ID]['user_superclinic']['add_disc'] and $_SESSION[CLIENT_ID]['user_superclinic']['role'] != 1){?>
            .dskChkContainer{
                display: none!important;
            }
            .add10DiscountCover{
                display: none!important;
            }
            <?php }?>
        </style>	
</head>
<div class="wrapper <?if(!empty($_COOKIE['theme']) and isset($aThemes[$_COOKIE['theme']])) echo $aThemes[$_COOKIE['theme']]['class']; else echo 'light-green-table'?>">
      <!-- Main Header -->
  <header class="main-header top">
    <nav class="navbar for-mobile" role="navigation">
  	<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button"></a>
    </nav>
    <!-- Logo -->
    <a href="<?=HOST?>" class="logo">
		<img src="images/logo.png" alt="" />
    </a>
  </header>
   
  <?include '_left_sidebar_tpl.php'?>
      
  <div class="content-wrapper"> 
      	<!-- start Main Header -->
	<header class="main-header">
		<nav class="navbar" role="navigation">		  
		  <h1><?=$sectionName?></h1>
		  <!-- Navbar Right Menu -->
		  <div class="navbar-custom-menu">
			<ul class="nav navbar-nav" id="infoMenu">
                            <?php if($_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1){?>
                            <li class="theme">
                                <div class="theme-block">
                                    <font>Settings:</font>
                                    <div class="select-block-1" style="width:170px;">
                                        <div class="select-1">
                                            <select name="theme" id="settingsSelector" >
                                                <option value="">-select-</option>
                                                <option value="setPricesPopup">Set Prices</option>
                                                <option value="receiptSettingsPopup">Receipt Settings</option>
                                                <option value="cashDrawerPopup">Cash Drawer</option>
                                                <option value="rewardsSettingsPopup">Rewards Program</option>
                                                <option value="weedmapsSettingsPopup">Weedmaps Settings</option>
                                                <option value="taxSettingsPopup">Tax Settings</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <?php }?>
                            <li class="theme">
                                  <div class="theme-block">
                                          <font>Theme:</font>
                                          <div class="select-block-1">
                                                  <div class="select-1">							
                                                          <select name="theme" id="themeSelector">
                                                              <?foreach($aThemes as $k=>$v){?>
                                                              <option value="<?=$k?>" <?if(empty($_COOKIE['theme']) or $_COOKIE['theme'] == $k) echo "selected"?>><?=$v['name']?></option>
                                                              <?}?>
                                                          </select>							
                                                  </div>
                                          </div>
                                  </div>
                            </li>
                            <li class="manager">
                                  <span>
                                          <font><?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1) echo 'Manager';elseif($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 2) echo 'Bud Tender';elseif($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 3) echo 'Security';elseif($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 4) echo 'Cashier';?>:</font>
                                          <?=$_SESSION[CLIENT_ID]['user_superclinic']['lastname']?>
                                  </span> 
                            </li>
                            <li class="sing-out">
                                  <a href="login.php">
                                          <span><img src="images/icon_sign_out.png" alt="" /><font>Sign out</font></span>
                                  </a>
                            </li>
			</ul>
		  </div>
	  </nav>
	</header>
	<div class="clearfix"></div>
	<!-- stop Main Header -->
        
        <?php if($_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1){
            $rewards_amount_needed = settings::get('rewards_amount_needed');            
            $rewards_receive = settings::get('rewards_receive');
            $receipt_mode = settings::get('receipt_mode');
            $receipt_label_text = settings::get('receipt_label_text');
            $receipt_name = settings::get('receipt_name');
            $receipt_address = settings::get('receipt_address');
            $receipt_phone = settings::get('receipt_phone');
            $weedmaps_apikey = settings::get('weedmaps_apikey');
            $tax_amount = settings::get('tax_amount');
            $tax_mode = settings::get('tax_mode');
            ?>
        <div class="modal fade" id="receiptSettingsPopup" tabindex="-1" role="dialog" aria-labelledby="receiptLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="_receipt_settings.php" method="post" class="singleForm">
                        <input type="hidden" name="sent_receipt_settings" value="1" />
                        <input type="hidden" name="return" value="<?=$_SERVER['REQUEST_URI']?>" />
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="receiptLabel">Receipt Settings</h4>
                        </div>
                        <div class="modal-body">
                            
                            <section class="content-header title-page for-desktop">
                                <h2>Mode</h2>
                            </section>
                            <div class="col">
                                <div class="form-group">
                                    <label><span>Print Receipt</span></label> <input type="radio" name="receipt_mode" value="1"<?if($receipt_mode == 1 or empty($receipt_mode)) echo ' checked'?>/>
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <label><span>Print Rx Labels</span></label> <input type="radio" name="receipt_mode" value="2"<?if($receipt_mode == 2) echo ' checked'?>/>
                                </div>
                            </div>
                            
                            
                            <section class="content-header title-page for-desktop">
                                <h2>Receipt Header</h2>
                            </section>
                            <div class="col">
                                <div class="form-group">
                                    <label><span>Name</span></label>
                                    <div class="box-input"><input type="text" class="form-control" name="receipt_name" value="<?=$receipt_name?>" /></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label><span>Address</span></label>
                                    <div class="box-input"><input type="text" class="form-control" name="receipt_address" value="<?=$receipt_address?>" /></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label><span>Phone Number</span></label>
                                    <div class="box-input"><input type="text" class="form-control" name="receipt_phone" value="<?=$receipt_phone?>" /></div>
                                </div>
                            </div>
                            <section class="content-header title-page for-desktop">
                                <h2>Rx Label Text</h2>
                            </section>
                            <div class="col">
                                <div class="form-group">
                                    <div class="box-input"><textarea class="form-control" name="receipt_label_text"><?=$receipt_label_text?></textarea></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        
        <script>
            $(document).ready(function(){
                $('#CDPSubmit').click(function(){
                    var passField = $(this).parent().prev().find('input[name="cashdrawer_password"]').val();
                    var confirmField = $(this).parent().prev().find('input[name="cashdrawer_password_confirm"]').val();
                    if(passField){
                        if(passField == confirmField){
                            $('#CDPForm').submit();
                        }else{
                            $('#CDPErr').text("Passwords mismatch");
                        }
                    }else{
                        $('#CDPErr').text("Passwords is empty");
                    }
                    return false; 
                });
            });
        </script>
        <div class="modal fade" id="cashDrawerPopup" tabindex="-1" role="dialog" aria-labelledby="cashdrawerLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="_set_cashdrawer_password.php" method="post" class="singleForm" id="CDPForm">
                        <input type="hidden" name="return" value="<?=$_SERVER['REQUEST_URI']?>" />
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="cashdrawerLabel">Cash Drawer Password</h4>
                        </div>
                        <div class="modal-body">                            
                            <div class="col">
                                <div class="form-group">
                                    <label><span>Enter Password</span></label>
                                    <div class="box-input"><input type="password" class="form-control" name="cashdrawer_password" value="" /></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label><span>Confirm Password</span></label>
                                    <div class="box-input"><input type="password" class="form-control" name="cashdrawer_password_confirm" value="" /></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <span id="CDPErr" style="color:#f00"></span>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="CDPSubmit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="setPricesPopup" tabindex="-1" role="dialog" aria-labelledby="pricesLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="pricesLabel">Set Prices</h4>
                    </div>
                    <div class="modal-body">
                        <?php
                        $aCategoryList = $oInventory->get_categories();
                        foreach($aCategoryList as $ctg){
                            if($ctg['measure_type'] == 1){
                                $lnk = 'inventory_set_prices.php';
                            }else{
                                $lnk = 'inventory_set_qty_prices.php';
                            }
                            ?>
                        <div class="col" style="margin: 10px;font-size:14px;text-transform: uppercase">
                            <strong><a href="<?=$lnk?>"><?=$ctg['name']?></a></strong>
                        </div>
                        <?php }?>                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        
        
        <div class="modal fade" id="rewardsSettingsPopup" tabindex="-1" role="dialog" aria-labelledby="rewardsLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="_rewards_settings.php" method="post" class="singleForm">
                        <input type="hidden" name="sent_rewards_settings" value="1" />
                        <input type="hidden" name="return" value="<?=$_SERVER['REQUEST_URI']?>" />
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="rewardsLabel">Rewards Program Settings</h4>
                        </div>
                        <div class="modal-body">
                            <div class="col">
                                <div class="form-group">
                                    <label><span>Amount needed to Spent ($)</span></label>
                                    <div class="box-input"><input type="text" class="form-control" name="rewards_amount_needed" value="<?=$rewards_amount_needed?>" /></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label><span>Receive ($)</span></label>
                                    <div class="box-input"><input type="text" class="form-control" name="rewards_receive" value="<?=$rewards_receive?>" /></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="weedmapsSettingsPopup" tabindex="-1" role="dialog" aria-labelledby="weedmapsLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="_weedmaps_settings.php" method="post" class="singleForm">
                        <input type="hidden" name="sent_weedmaps_settings" value="1" />
                        <input type="hidden" name="return" value="<?=$_SERVER['REQUEST_URI']?>" />
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="weedmapsLabel">Weedmaps Settings</h4>
                        </div>
                        <div class="modal-body">
                            <div class="col">
                                <div class="form-group">
                                    <label><span>API Key</span></label>
                                    <div class="box-input"><input type="text" class="form-control" name="weedmaps_apikey" value="<?=$weedmaps_apikey?>" /></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>                
            </div>
        </div>
        
        <div class="modal fade" id="taxSettingsPopup" tabindex="-1" role="dialog" aria-labelledby="receiptLabelTax">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="_tax_settings.php" method="post" class="singleForm">
                        <input type="hidden" name="sent_tax_settings" value="1" />
                        <input type="hidden" name="return" value="<?=$_SERVER['REQUEST_URI']?>" />
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="receiptLabelTax">Tax Settings</h4>
                        </div>
                        <div class="modal-body">  
                            <div class="col">
                                <div class="form-group">
                                    <label><span>Amount</span></label>
                                    <div class="box-input">
                                        <input type="text" class="form-control" name="tax_amount" value="<?=$tax_amount?>" style="width:50%;float:left;margin-right:20px"/>
                                        <strong style="position:relative;top:5px">%</strong> <div class="clearfix"></div>                                       
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col">
                                <div class="form-group">
                                    <label><span>Including in your sales</span></label> <input type="radio" name="tax_mode" value="0"<?if($tax_mode == 0 or empty($tax_mode)) echo ' checked'?>/>
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <label><span>Add to your sales</span></label> <input type="radio" name="tax_mode" value="1"<?if($tax_mode == 1) echo ' checked'?>/>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php }?>

