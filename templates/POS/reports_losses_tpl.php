<?include '_header_tpl.php'?>

<?include '_reports_list_tpl.php'?>

<?include '_calendar_tpl.php'?>

<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Losses</h2>
    </section>
</section>

<section class="content">
    <div class="checkou-block">
        <div class="checkou-button">
            <?if(!isset($_GET['show_all'])){?>
            <button class="button" onclick="parent.location='reports_losses.php?show_all=1'">Show All</button>
            <?}else{?>
            <button class="button" onclick="parent.location='reports_losses.php'">Show By Categories</button>
            <?}?>
        </div>
    </div>	  
</section>

<?if(!empty($aLosses)){?>
<section class="content">
    <div class="table-responsive table-4">
        <table>
            <tr>
                <th class="th-serial-number"></th>
                <th><?sortableHeader('Date', 'reports_losses.php', 'loss_date', $ord, false, $ordby)?></th>
                <th><?sortableHeader('Product Name', 'reports_losses.php', 'goods_name', $ord, true, $ordby)?></th>
                <th><?sortableHeader('Purchase Date', 'reports_losses.php', 'starting_date', $ord, false, $ordby)?></th>
                <th><?sortableHeader('Starting Amount', 'reports_losses.php', 'starting_value', $ord, false, $ordby)?></th>
                <th><?sortableHeader('Lost Qty', 'reports_losses.php', 'loss_value', $ord, false, $ordby)?></th>
                <th><?sortableHeader('Losses', 'reports_losses.php', 'loss_amt', $ord, false, $ordby)?></th>
                <th></th>
            </tr>
            <?php 
                $totalAmt = 0;
                foreach($aLosses as $k=>$loss){
                    $totalAmt+= $loss['loss_amt'];
                ?>
            <tr>
                <td><span><?=$k+1?></span></td>
                <td><span><?=strftime("%m/%d/%Y",$loss['loss_date'])?></span></td>
                <td><span><?=$loss['goods_name']?></span></td>
                <td><span><?=strftime("%m/%d/%Y",$loss['starting_date'])?></span></td>
                <td><span><?=$loss['starting_value']?> <?=strtolower($loss['modifier_name'])?><?=($loss['modifier_name'] === 'Gram' and $loss['starting_value'] > 1) ? 's' : ''?></span></td>
                <td><span class="red">-<?=$loss['loss_value']?> <?=strtolower($loss['modifier_name'])?><?=($loss['modifier_name'] === 'Gram' and $loss['loss_value'] > 1) ? 's' : ''?></span></td>
                <td><span class="red">-$<?=number_format($loss['loss_amt'],2,'.',',')?></span></td>
                <td>                    
                    <div class="cont-t3">
                    <a href="reports_losses.php?delete=<?=$loss['id']?>" title="delete" onclick="return confirm('Are you sure you want to delete this record?')">
                    <font><i class="fa fa-times"></i></font><font>delete</font>
                    </a>
                    </div>
                </td>
            </tr>
            <?php }?>
            <tr>
                <td></td>
                <td></td>
                <td><strong><span>TOTAL</span></strong></td>
                <td></td>
                <td></td>
                <td></td>
                <td><strong><span class="red">-$<?=number_format($totalAmt,2,'.',',')?></span></strong></td>
                <td></td>
            </tr>
        </table>
    </div>
</section>
<?}?>

<?php if(!empty($aCategories)){ ?>
<section class="content">
    <?php foreach($aCategories as $catItem){?>
        <?php if(!empty($catItem['losses'])){?>
    <div class="data-recent">
        <span class="name"><?=$catItem['category_name']?></span>
    </div>
    <div class="table-responsive table-4">
        <table>
            <tr>
                <th class="th-serial-number"></th>
                <th><?sortableHeader('Date', 'reports_losses.php', 'loss_date', $ord, false, $ordby)?></th>
                <th><?sortableHeader('Product Name', 'reports_losses.php', 'goods_name', $ord, true, $ordby)?></th>
                <th><?sortableHeader('Purchase Date', 'reports_losses.php', 'starting_date', $ord, false, $ordby)?></th>
                <th><?sortableHeader('Starting Amount', 'reports_losses.php', 'starting_value', $ord, false, $ordby)?></th>
                <th><?sortableHeader('Lost Qty', 'reports_losses.php', 'loss_value', $ord, false, $ordby)?></th>
                <th><?sortableHeader('Losses', 'reports_losses.php', 'loss_amt', $ord, false, $ordby)?></th>
                <th></th>
            </tr>
             <?php 
                $totalAmt = $totalQty = 0;
                foreach($catItem['losses'] as $k=>$loss){
                    $totalAmt+= $loss['loss_amt'];
                    $totalQty+= $loss['loss_value'];
                ?>
            <tr>
                <td><span><?=$k+1?></span></td>
                <td><span><?=strftime("%m/%d/%Y",$loss['loss_date'])?></span></td>
                <td><span><?=$loss['goods_name']?></span></td>
                <td><span><?=strftime("%m/%d/%Y",$loss['starting_date'])?></span></td>
                <td><span><?=$loss['starting_value']?> <?=strtolower($loss['modifier_name'])?><?=($loss['modifier_name'] === 'Gram' and $loss['starting_value'] > 1) ? 's' : ''?></span></td>
                <td><span class="red">-<?=$loss['loss_value']?> <?=strtolower($loss['modifier_name'])?><?=($loss['modifier_name'] === 'Gram' and $loss['loss_value'] > 1) ? 's' : ''?></span></td>
                <td><span class="red">-$<?=number_format($loss['loss_amt'],2,'.',',')?></span></td>
                <td>                    
                    <div class="cont-t3">
                    <a href="reports_losses.php?delete=<?=$loss['id']?>" title="delete" onclick="return confirm('Are you sure you want to delete this record?')">
                    <font><i class="fa fa-times"></i></font><font>delete</font>
                    </a>
                    </div>
                </td>
            </tr>
                <?php }?>
            <tr>
                <td></td>
                <td></td>
                <td><strong><span>TOTAL</span></strong></td>
                <td></td>
                <td></td>
                <td><strong><span class="red">-<?=$totalQty?></span></strong></td>
                <td><strong><span class="red">-$<?=number_format($totalAmt,2,'.',',')?></span></strong></td>
                <td></td>
            </tr>
        </table>
    </div>
    <br />
        <?php }?>
    <?php }?>
</section>
<?php }?>

<div id="mobileCalendar"><?include '_calendar_mobile_tpl.php'?></div>

<?include '_footer_tpl.php'?>
