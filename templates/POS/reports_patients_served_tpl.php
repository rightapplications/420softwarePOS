<?include '_header_tpl.php'?>
<style>
    .leftCol{
        float:left;
        width: 800px;
        margin-right:40px;
    }
    .rightCol{
        float:left;
        width: 400px;
    }
</style>

<?include '_reports_list_tpl.php'?>

<?include '_calendar_tpl.php'?>

<script type="text/javascript">
    $(document).ready(function(){       
        $('.transactionsSelected').click(function(){
            var amt = 0;
            $('.transactionsSelected').each(function(){
                if($(this).is(':checked')){
                    amt+=$(this).next().val()*1;
                }
            });
            $('#totalAmt').text(amt.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));   
            $('#totalAmt2').text(amt.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')); 
            getDailySummary();
        });
        
        $('#selectfirst').click(function(){           
            var num = $('#selectnum').val();
            var selectors = $('.transactionsSelected');
            if(num > selectors.length){
                num = selectors.length;
            }             
            if($(this).prop('checked') == true){
                var status = true;                
            }else{
                var status = false;
            }
            $.get('_ajax_set_selected_transaction.php?numTransaction='+num+'&selected='+(status ? 1 : 0));
            for(i=0; i<num; i++){                
                selectors.eq(i).prop('checked', status);
            }
            var amt = 0;
            $('.transactionsSelected').each(function(){
                if($(this).is(':checked')){
                    amt+=$(this).next().val()*1;
                }
            });
            $('#totalAmt').text(amt.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
            $('#totalAmt2').text(amt.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
            getDailySummary();
        });
        
        <?php if(!empty($_SESSION[CLIENT_ID]['transactionsSelected'])){?>
        $('#selectfirst').click();
        <?php }?>
        
        jQuery("input.number_only").keyup(function(event){
            checkNumberFields(this, event);
        }).keypress(function(event){
            checkNumberFields(this, event);
        }).change(function(event){
            checkNumberFields(this, event);
        });
    });
    
    function getDailySummary(){
        var Days = [];
        var Days2 = [];
        var Qtys = [];
        var Amts = [];
        var Rows = '';
        var num = 1;
        var qty;
        var amt;
        var totalQty = 0;
        var totalAmt = 0;
        var i=0;
        $('#dailySummaryTable').html('');        
        
        $('.transactionsSelected').each(function(){//calculation
            if($(this).is(':checked')){
                var currDate = $(this).parent().find('.dateContainer').text().split(" ")[0];
                if($.inArray(currDate, Days) < 0){
                    Days.push(currDate);
                    qty=1;
                    Qtys.push(qty);
                    amt=$(this).next().val()*1;
                    Amts.push(amt);
                    i++;                    
                }else{
                    qty++;
                    amt+=$(this).next().val()*1;
                    Qtys[i-1] = qty;
                    Amts[i-1] = amt;
                }
                totalQty++;
                totalAmt+=$(this).next().val()*1;
            }
        });
        $('.transactionsSelected').each(function(){//row creation
            if($(this).is(':checked')){
                var currDate = $(this).parent().find('.dateContainer').text().split(" ")[0];                                
                if($.inArray(currDate, Days2) < 0){
                    Days2.push(currDate);
                    Rows+='<tr><td>'+num+'</td><td>'+currDate+'</td><td>'+Qtys[num-1]+'</td><td>$'+Amts[num-1].toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')+'</td></tr>';
                    num++;
                }                
            }
        }); 
        Rows+='<tr><td></td><td>TOTAL</td><td>'+totalQty+'</td><td>$'+totalAmt.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')+'</td></tr>';  
        $('#dailySummaryTable').append(Rows);
    }
    
    function checkNumberFields(e, k){
        var str = jQuery(e).val();
        var new_str = s = "";
        for(var i=0; i < str.length; i++){
                s = str.substr(i,1);
                if((s!=" " && isNaN(s) == false) || s=='.'){
                        new_str += s;
                }
        }
        jQuery(e).val(new_str);
    }
</script>
<style>
    .table-responsive{margin-bottom:20px;}
    .sortHeader{
        margin-bottom:9px!important;
    }
    .sortHeader a{
        padding:5px;
    }
    .sortHeader a.active{
        border:1px #51c553 solid;        
    }
    .buttonsBlock button{
        margin: 10px;
    }
    @media (max-width: 1025px) {
        .sortHeader a{display:block;}
        .sortHeader .sortBtn{display:block;margin: 10px 0}
    }
</style>

<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Patients Served by:</h2>
    </section>
</section>

<section class="content"> 
    <section class="search">
        <div class="search-content">
            <div class="search-form">
                <form action="reports_patients_served.php" method="get">
                    <div class="control-button1">
                    <div class="lupa"><i class="fa fa-search"></i></div>
                    <div class="input-search">
                        <input type="text" class="form-control" name="search" placeholder="Type here to search" value="<?=isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''?>"/>
                    </div>
                    </div>
                    <div class="control-button2">
                    <div class="input-submit">
                        <input type="submit" class="form-control" value="Search" />
                    </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</section>

<section class="content">
    <section class="content-header">
        <h2 class="sortHeader">
            <span class="sortBtn"><span class="for-mobile">Patients Served by </span><?=$aUser['firstname']?> <?=$aUser['lastname']?></span>
            <span>| TOTAL SALES: <b>$<?=number_format($aSales['gross'],2,'.',',')?></b></span>|
            Sort by:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
            <span class="sortBtn"><a href="reports_patients_served.php?user=<?=$aUser['id']?>&search=<?=(isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '')?>" <?if($activeSort === 'patients') echo 'class="active"'?>>&nbsp;Patients&nbsp;</a> &nbsp;&nbsp;&nbsp;&nbsp;</span>
            <span class="sortBtn">
                <a href="reports_patients_served.php?user=<?=$aUser['id']?>&search=<?=(isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '')?>&sort=amount&ord=<?if(!isset($_GET['ord']) or $_GET['ord'] === 'ASC') echo 'DESC';else echo 'ASC'?>" <?if($activeSort === 'amount') echo 'class="active"'?>>
                    <?if(isset($_GET['ord']) and $activeSort === 'amount'){?> 
                    <i class="for-mobile"><img src="images/icon_select_<?=$_GET['ord'] === 'ASC' ? 'up' : 'down'?>m.png" alt="" /></i>
                    <i class="for-desktop"><img src="images/icon_select_<?=$_GET['ord'] === 'ASC' ? 'up' : 'down'?>.png" alt="" /></i>
                    <?}else{?>
                    <i class="for-mobile"><img src="images/icon_select_disabledm.png" alt="" /></i>
                    <i class="for-desktop"><img src="images/icon_select_disabled.png" alt="" /></i>
                    <?}?>
                    Amount
               </a>
            </span>
            <span>
                <a href="reports_patients_served.php?user=<?=$aUser['id']?>&search=<?=(isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '')?>&sort=date&ord=<?if(!isset($_GET['ord']) or $_GET['ord'] === 'ASC') echo 'DESC';else echo 'ASC'?>" <?if($activeSort === 'date') echo 'class="active"'?>>
                    <?if(isset($_GET['ord']) and $activeSort === 'date'){?> 
                    <i class="for-mobile"><img src="images/icon_select_<?=$_GET['ord'] === 'ASC' ? 'up' : 'down'?>m.png" alt="" /></i>
                    <i class="for-desktop"><img src="images/icon_select_<?=$_GET['ord'] === 'ASC' ? 'up' : 'down'?>.png" alt="" /></i>
                    <?}else{?>
                    <i class="for-mobile"><img src="images/icon_select_disabledm.png" alt="" /></i>
                    <i class="for-desktop"><img src="images/icon_select_disabled.png" alt="" /></i>
                    <?}?>
                    Date
               </a>
            </span>
        </h2>
    </section>
</section>

<section class="content">
    <p>Select <input type="text" class="number_only" style="width:50px" value="<?=isset($_SESSION[CLIENT_ID]['transactionsSelectedNum']) ? $_SESSION[CLIENT_ID]['transactionsSelectedNum'] : ''?>" id="selectnum"/> first transactions <input type="checkbox" id="selectfirst"/> &nbsp;&nbsp;&nbsp;Selected amount: $<font id="totalAmt2">0.00</font></p>
        
    <form action="" method="post" name="transaction_form" id="transaction_form">
        <input type="hidden" name="sent_transactions_action" value="0" id="sent_transactions_action">
    <?if(!empty($aPatients)){?>
        <div class="leftCol">
        <?foreach($aPatients as $patient){?>
        <div class="block-bordtop">
            <p class="p-border-left"><span></span><?if(!empty($patient['client_id'])){?><a href="edit_patient.php?id=<?=$patient['client_id']?>"><?=$patient['firstname']?> <?=$patient['lastname']?></a><?}else{?><?if(!empty($patient['firstname']) or !empty($patient['lastname'])){?>
                        <?=$patient['firstname']?> <?=$patient['lastname']?>
                            <?}else{?>
                        Unknown Patient
                            <?}?><?}?></p>        
        </div> 
            <?if(!empty($patient['orders'])){?>      
                <?foreach($patient['orders'] as $aOrder){?>
        <p>
            <input type="checkbox" name="selectedTransactions[]" value="<?=intval($aOrder['id'])?>" class="transactionsSelected jcf-ignore"/>
            <input type="hidden" name="transAmt[]" value="<?=round($aOrder['total']-$aOrder['paid_rewards'], 2)?>" />
            <span class="dateContainer"><?=strftime(DATE_FORMAT." %I:%M%p", $aOrder['date'])?></span>&nbsp;&nbsp;&nbsp;&nbsp; 
            <?if($activeSort == 'amount'){?> <b><?=$aOrder['client_firstname']?> <?=$aOrder['client_lastname']?></b>&nbsp;&nbsp;&nbsp;&nbsp;<?}?>
            <?if(!empty($aOrder['delivery'])){?>Delivery<?}?>
        </p>
        
        <?if(!empty($aOrder['items'])){?>
        <div class="table-responsive table-2">
            <table>
                <tr>
                    <th>&nbsp;</th>
                    <th><font>Product Name</font></th>
                    <th><font>QTY</font></th>
                    <th><font>Discount</font></th>
                    <th><font>Amount</font></th>
                </tr>
                <?
                $total = 0;
                $totalDsc = 0;
                foreach($aOrder['items'] as $k=>$item){
                    $total+= $item['price']*$item['qty'];
                    $totalDsc+= $item['d'];
                ?>
                <tr>
                    <td><?=$k+1?></td>
                    <td><?=$item['goods_item_name']?></td>
                    <td><?=$item['qty']?> <?=(@$item['alt'] === 'default' or @$item['alt'] === 'other') ? @$item['modifier_name'] : @$aAlternativeWeights[$item['alt']]['name']?></td>
                    <td>
                        <div <?if($item['d'] <= 0){?>class="hr"<?}?>> 
                        <?if($item['d'] > 0){?>
                            $<?=number_format($item['d'],2,'.',',')?>
                        <?}?>
                        </div>
                    </td>
                    <td>$<?=number_format($item['price']*$item['qty'],2,'.',',')?></td>
                </tr>
                <?}?>
                <?php if($aOrder['tax_mode'] > 0 and $aOrder['tax'] > 0){
                    $total+=$aOrder['tax'];
                    ?>
                <tr>
                    <td>&nbsp;</td>
                    <td colspan="2" align="left"><strong>TAX:</strong></td>
                    <td>&nbsp;</td>
                    <td>$<?=number_format(round($aOrder['tax'], 2),2,'.',',')?></td>
                </tr>
                <?php }?>
                <tr>
                    <td>&nbsp;</td>
                    <td colspan="2" align="left"><strong>TOTAL:</strong></td>
                    <td>
                        <?if($totalDsc > 0){?>
                        <strong><a href="#dsc<?=$aOrder['id']?>" data-toggle="modal" data-target="#dsc<?=$aOrder['id']?>">$<?=number_format(round($totalDsc, 2),2,'.',',')?></a></strong>
                        <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="dsc<?=$aOrder['id']?>">
                          <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Discount reason</h4>
                              </div>
                              <div class="modal-body">
                              <?=$aOrder['discount_reason']?>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                              </div>
                            </div>
                          </div>
                        </div>                   
                        <?}?>
                    </td>
                    <td><strong>$<?=number_format(round($total, 2),2,'.',',')?> <?if($aOrder['paid_rewards']){?>(Rewards: $<?=number_format($aOrder['paid_rewards'],2,'.',',')?>)<?}?></strong></td>
                </tr>
            </table>
        </div>
        <?}?>
                <?}?>
            <?}?>
        <?}?>
        </div>
        <div class="rightCol" id="dailySummary">
            <h3>Daily Summary</h3>
            <div class="table-responsive table-2">
                <table>
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th><font>Date</font></th>
                            <th><font>Transactions</font></th>
                            <th><font>Amount</font></th>
                        </tr>
                    </thead>
                    <tbody id="dailySummaryTable">
                        
                    </tbody>                    
                </table>
            </div>
        </div>
        <div class="clearfix"></div>
    <?}?>
    </form>
    <div class="block-bordtop">
            <p class="p-border-left">Selected amount: $<font id="totalAmt">0.00</font></p>        
    </div>
</section>



<section class="content buttonsBlock">
    <button class="button" onclick="$('#deleteModal').modal();" style="background-color:#f00">Delete selected</button>
    <button class="button" onclick="$('#sent_transactions_action').val('export');$('#transaction_form').submit();" />Export selected</button>
    <?if(isset($_SESSION[CLIENT_ID]['back_from_details'])){?>
    <a class="btn" href="<?=$_SESSION[CLIENT_ID]['back_from_details']?>"><< Back</a>
    <?}?>
</section>

<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mylModalLabelDel" id="deleteModal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabelDel">Select Action</h4>
            </div>
            <div class="modal-body" style="text-align: center">

                <button class="button" onclick="if(confirm('Are you sure you want to delete selected transactions?')){$('#sent_transactions_action').val('delete');$('#transaction_form').submit();}return false;" style="background-color:#f00;width:220px;margin: 10px auto">Delete From Everything</button>

                <button class="button" onclick="if(confirm('Are you sure you want to delete selected transactions and return back the orders items to inventory?')){$('#sent_transactions_action').val('return');$('#transaction_form').submit();}return false;" style="background-color:#f00;width:220px;margin: 10px auto">Return back to inventory</button>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>      

<div id="mobileCalendar"><?include '_calendar_mobile_tpl.php'?></div>

<?include '_footer_tpl.php'?>