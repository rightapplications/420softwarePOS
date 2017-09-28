<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu">
            <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1 or $_SESSION[CLIENT_ID]['user_superclinic']['role'] == 2){?> 
            <li<?if($activeMenu == 'pos'){?> class="active"<?}?>><a href="pos.php"><span><font class="icon_menu_1"></font><font class="link-text">POS</font></span></a></li>
            <?}?>
            <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1 or $_SESSION[CLIENT_ID]['user_superclinic']['role'] == 2){?> 
            <li<?if($activeMenu == 'delivery'){?> class="active"<?}?>><a href="delivery_groups.php"><span><font class="icon_menu_1"></font><font class="link-text">DELIVERY</font></span></a></li>
            <?}?>
            <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 4 or ($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 3 and !empty($_SESSION[CLIENT_ID]['user_superclinic']['add_disc']))){?>  
            <li<?if($activeMenu == 'cashier'){?> class="active"<?}?>><a href="cashier.php"><span><font class="icon_menu_8"></font><font class="link-text">CASHIER</font></span></a></li>
            <?}?>
            <?if(!empty($_SESSION[CLIENT_ID]['user_superclinic']['add_inventory']) or !empty($_SESSION[CLIENT_ID]['user_superclinic']['deactivate_inventory']) or !empty($_SESSION[CLIENT_ID]['user_superclinic']['set_prices']) or !empty($_SESSION[CLIENT_ID]['user_superclinic']['update_price']) or $_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
            <li<?if($activeMenu == 'inventory'){?> class="active"<?}?>><a href="inventory.php"><span><font class="icon_menu_2"></font><font class="link-text">INVENTORY</font></span></a></li>
            <?}?>
            <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] != 1 and !empty($_SESSION[CLIENT_ID]['user_superclinic']['invoices'])){?> 
            <li<?if($activeMenu == 'invoices'){?> class="active"<?}?>><a href="reports_invoices.php"><span><font class="icon_menu_4"></font><font class="link-text">INVOICES</font></span></a></li>
            <?}?>
            <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>            
            <li<?if($activeMenu == 'employees'){?> class="active"<?}?>><a href="employees.php"><span><font class="icon_menu_3"></font><font class="link-text">EMPLOYEES</font></span></a></li>
            <?}?>           
            <li<?if($activeMenu == 'schedule'){?> class="active"<?}?>><a href="employees_schedule.php"><span><font class="icon_menu_3"></font><font class="link-text">SCHEDULE</font></span></a></li>
            <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 3){?>  
            <li<?if(($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 3 and $activeMenu == 'employees') or ($_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1 and $activeMenu == 'shifts')){?> class="active"<?}?>><a href="employees_shifts.php"><span><font class="icon_menu_3"></font><font class="link-text">SHIFTS</font></span></a></li>
            <?}?>
            <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
            <li<?if($activeMenu == 'reports'){?> class="active"<?}?>><a href="reports.php"><span><font class="icon_menu_4"></font><font class="link-text">REPORTS</font></span></a></li>
            <?}?>
            <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] != 1 and !empty($_SESSION[CLIENT_ID]['user_superclinic']['one_day_visits'])){?>
            <li<?if($activeMenu == 'reports'){?> class="active"<?}?>><a href="reports_one_day_visits.php"><span><font class="icon_menu_5"></font><font class="link-text">VISITS</font></span></a></li>
            <?}?>
            <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] != 1 and !empty($_SESSION[CLIENT_ID]['user_superclinic']['patients_history'])){?>
            <li<?if($activeMenu == 'reports-nonmanager'){?> class="active"<?}?>><a href="reports_patients_history.php"><span><font class="icon_menu_5"></font><font class="link-text">PATIENTS HISTORY</font></span></a></li>
            <?}?>
            <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1 or $_SESSION[CLIENT_ID]['user_superclinic']['role'] == 3){?>      
            <li<?if($activeMenu == 'patients'){?> class="active"<?}?>><a href="patients.php"><span><font class="icon_menu_5"></font><font class="link-text">PATIENTS</font></span></a></li>
            <?}?>   
            <?if($_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1 or !empty($_SESSION[CLIENT_ID]['user_superclinic']['add_petty_cash'])){?>
            <li<?if($activeMenu == 'payouts'){?> class="active"<?}?>><a href="payouts.php"><span><font class="icon_menu_6"></font><font class="link-text">PAYOUTS</font></span> </a></li>
            <?}?>
            <li<?if($activeMenu == 'messenger'){?> class="active"<?}?>><a href="messenger.php"><span><font class="icon_menu_6"></font><font class="link-text">MESSAGES <font id="unreadNumber" class="num-unread-messages"></font></font></span> </a></li>
            <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
            <li<?if($activeMenu == 'email'){?> class="active"<?}?>><a href="patients_emails.php"><span><font class="icon_menu_7"></font><font class="link-text">EMAIL</font></span></a></li>
            <?}?>
            <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1 or $_SESSION[CLIENT_ID]['user_superclinic']['role'] == 2){?> 
            <li<?if($activeMenu == 'remote_orders'){?> class="active"<?}?>><a href="remote_orders.php"><span><font class="icon_menu_8"></font><font class="link-text">ONLINE ORDERS</font></span></a></li>
            <?}?>
            <li<?if($activeMenu == 'support'){?> class="active"<?}?>><a href="support.php"><span><font class="icon_menu_7"></font><font class="link-text">SUPPORT <font id="unreadSupport" class="num-unread-messages"></font></font></span> </a></li>
        </ul>
        <div id="mobileInfoMenu">
            <ul class="nav navbar-nav">
              <?php if($_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1){?>
                <li class="theme">
                    <div class="theme-block">
                        <font>Settings:</font>
                        <div class="select-block-1" style="width:170px;">
                            <div class="select-1">
                                <select name="theme" id="settingsSelectorMobile" >
                                    <option value="">-select-</option>
                                    <option value="setPricesPopup">Set Prices</option>
                                    <option value="receiptSettingsPopup">Receipt Settings</option>
                                    <option value="cashDrawerPopup">Cash Drawer</option>
                                    <option value="rewardsSettingsPopup">Rewards Program</option>
                                    <option value="weedmapsSettingsPopup">Weedmaps Settings</option>
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
                                            <select name="theme" id="themeSelectorMobile">
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
                            <font><?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1) echo 'Manager';elseif($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 2) echo 'Sales';else echo 'Security'?>:</font>
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
    </section>
   
    
</aside>

 <script>
        $(document).ready(function(){
            if($('#mobileInfoMenu').css('display') != 'none'){
                var menuHeight = $('.sidebar-menu').height()*1+$('#mobileInfoMenu').height()*1;
            }else{
                var menuHeight = $('.sidebar-menu').height()*1;
            }            
            if($(window).width() <= 2000){
                if($(window).scrollTop() > menuHeight*1+80*1){
                    $('#cart2').css('position', 'fixed');
                    $('#cart2').css('top',0);
                }else{                    
                    $('#cart2').css('position', 'relative');                    
                    $('#cart2').css('top',menuHeight*1);                   
                }
                $(window).scroll(function(){
                    if($(window).scrollTop() > menuHeight*1+80*1){
                        $('#cart2').css('position', 'fixed');
                        $('#cart2').css('top',0);
                    }else{                    
                        $('#cart2').css('position', 'relative');                    
                        $('#cart2').css('top',menuHeight*1);                   
                    }
                });
            }else{
                $('#cart2').css('position', 'relative');                    
                $('#cart2').css('top',menuHeight*1); 
            }
        });
        
    </script>
    <style>
        #cart2{padding: 5px 15px;width:280px;left:0;display:none;z-index: 1000;}
        #cart2 h3{color:#888;}
        #cart2 table tr:nth-child(odd) td{background-color:#eee}
        #cart2 table td{padding:5px; vertical-align: middle} 
        #cart2 .button{margin: 20px auto}
        .cart2amt{text-align: right;}
        .cart2qty{color: #888}
        .cart2total td{font-weight: bold;padding-top:20px!important;background-color: #fff!important}
        .sidebar-open #cart2{
            display:block;
        }
        @media (min-width: 2001px) {
          #cart2{
            display:block;
            float:left;
          }  
        }
    </style>
    <section id="cart2">        
       
    </section>
    <?php if(isset($aNextPatient) and !empty($aNextPatient)){?>
    <div class="hidden" id="nextPatientRewards">Patient: <?=$aNextPatient['firstname']?> <?=$aNextPatient['lastname']?> <?if($aNextPatient['rewards']){?>(Rewards <?=number_format($aNextPatient['rewards'],2,'.',',')?>)<?}?></div>
    <?php }?>
    

<script>
        $(document).ready(function(){
            $('#sidebarCloser').find('a').click(function(){
                $('.sidebar-toggle').click();
                return false;
            });
        });
    </script>
    <style>
        .sidebar-closer{
            position:fixed;
            bottom: 0;
            left: 0;
            width:280px;
            height: 48px;
            text-align:center;
            font-size: 36px;
            padding-bottom:20px;
            border-top:2px solid #ebebeb;
            z-index:1000;
            display:none;
        }
        .sidebar-open .sidebar-closer{
            display:block;
        }
        <?php if(!empty($_SESSION[CLIENT_ID]['cart'])){?>
        .dskChkContainer{display:none}
        <?php }?>
    </style>
    <div id="sidebarCloser" class="sidebar-closer">
        <a href="#"><<</a>
    </div>