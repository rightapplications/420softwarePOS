<?include '_header_tpl.php'?>

<?include '_reports_list_tpl.php'?>

<!-- start content title-page -->
<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Deleting History</h2>
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

<?if(!empty($aItems)){?>
<section class="content">
    <div class="table-responsive table-4">
        <table>
            <tr>
                <th class="th-serial-number"></th>
                <th><?sortableHeader('Date', 'inventory_deleting_history.php', 'date', $ord, true, $ordby)?></th>
                <th><?sortableHeader('Employee Name', 'inventory_deleting_history.php', 'user_name', $ord, false, $ordby)?></th>
                <th><?sortableHeader('Product Name', 'inventory_deleting_history.php', 'name', $ord, false, $ordby)?></th>
                <th><?sortableHeader('Category', 'inventory_deleting_history.php', 'category', $ord, false, $ordby)?></th>                
            </tr>
            <?foreach($aItems as $k=>$item){?>
            <tr>
                <td><span><?=$k+1?></span></td>
                <td><span><?=strftime(DATE_FORMAT." %I:%M%p", $item['date'])?></span></td>
                <td><span><?=$item['user_name']?></span></td>
                <td><span><?=$item['name']?></span></td>
                <td><span><?=$item['category']?></span></td>                
            </tr>
            <?}?>
        </table>
    </div>
</section>
<?}?>

<div id="mobileCalendar"><?include '_calendar_mobile_tpl.php'?></div>

<?include '_footer_tpl.php'?>
