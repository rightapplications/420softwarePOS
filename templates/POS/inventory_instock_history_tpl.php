<?include '_header_tpl.php'?>

<!-- start content title-page -->
<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Purchase History</h2>
    </section>
</section>
<!-- stop content title-page -->

<section class="content"> 
    <section class="search">
        <div class="search-content">
            <div class="search-form">
                <div class="control-button3">
                    <div class="input-submit">
                            <input type="button" class="form-control" value="<< Back" onclick="parent.location='inventory.php'"/>
                    </div>
                </div>
            </div>
        </div>
    </section>
</section>

<?include '_calendar_tpl.php'?>

<script>
$(document).ready(function(){
    $('#selectmenu').change(function(){
        parent.location = $(this).val();
    });
});
</script>
<section class="content">
    <section class="content-header title-page">
        <div class="select-block-1 select-title-page">
            <div class="select-1">							
                <select id="selectmenu">
                    <option value="inventory_instock_history.php?c=all" <?if(empty($selectedCategory)) echo 'selected="selected"'?>>All Categories</option>
                    <?php foreach($aInStockCategories as $c){?>
                    <option value="inventory_instock_history.php?c=<?=$c['category']?>" <?if(trim($c['category']) == trim($selectedCategory)) echo 'selected="selected"'?>><?=$c['category']?></option>
                    <?php }?>                   
                </select>							
            </div>
        </div>
    </section>
</section>


<section class="content">
<?php if(!isset($error)){?>
    <?php if(!empty($aDays)){?>
    <div class="table-responsive table-4">
        
        <?php if(empty($selectedCategory)){?>
        
            <?php foreach($aDays as $day){?>
            <table>
                <tr>
                    <th>&nbsp;</th>
                    <?php foreach($day['instock'] as $stock){?>
                    <th colspan="2" style="text-align:center"><?=$stock['category']?></th>
                    <?php }?>
                    <th colspan="2" style="text-align:center">TOTAL</th>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <?php foreach($day['instock'] as $stock){?>
                    <td style="text-align:center"><strong>Purchase</strong></td>
                    <td style="text-align:center"><strong>Sale</strong></td>
                    <?php }?>
                    <td style="text-align:center"><strong>Purchase</strong></td>
                    <td style="text-align:center"><strong>Sale</strong></td>
                </tr>
                <tr>
                    <td><span><?=$day['date']?></span></td>
                    <?php 
                    $totalPurchase = $totalSale = 0;
                    foreach($day['instock'] as $stock){
                        $totalPurchase+=$stock['amount_purchase'];
                        $totalSale+=$stock['amount_sale'];
                        ?>
                    <td style="text-align:center"><span>$<?=number_format($stock['amount_purchase'],2,'.',',')?></span></td>
                    <td style="text-align:center"><span>$<?=number_format($stock['amount_sale'],2,'.',',')?></span></td>
                    <?php }?>
                    <td style="text-align:center"><span>$<?=number_format($totalPurchase,2,'.',',')?></span></td>
                    <td style="text-align:center"><span>$<?=number_format($totalSale,2,'.',',')?></span></td>
                </tr>
            </table>
            <br />
            <?php }?>
        
        <?php }else{?>
            
            <table>
                <tr>
                    <th>&nbsp;</th>
                    <th style="text-align:center; width:40%"><strong>Purchase</strong></th>
                    <th style="text-align:center; width:40%"><strong>Sale</strong></th>
                </tr>
                <?php foreach($aDays as $day){?>
                <tr>
                    <td><span><?=$day['date']?></span></td>
                    <?php 
                    $totalPurchase = $totalSale = 0;
                    foreach($day['instock'] as $stock){
                        $totalPurchase+=$stock['amount_purchase'];
                        $totalSale+=$stock['amount_sale'];
                        ?>
                    <td style="text-align:center"><span>$<?=number_format($stock['amount_purchase'],2,'.',',')?></span></td>
                    <td style="text-align:center"><span>$<?=number_format($stock['amount_sale'],2,'.',',')?></span></td>
                    <?php }?>
                </tr>
                <?php }?>
            </table>        
        
        <?php }?>
        
    </div>
    <?php }else{?>
    <p class="error">History for this period is not available</p>
    <?php }?>
<?php }else{?>
    <p class="error"><?=$error?></p>
<?php }?>
</section>


<div id="mobileCalendar"><?include '_calendar_mobile_tpl.php'?></div>

<?include '_footer_tpl.php'?>