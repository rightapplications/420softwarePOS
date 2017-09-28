<?include '_header_tpl.php'?>

<!-- start content title-page -->
<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Inventory History</h2>
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

<?if($aCategories){?>
    <?foreach($aCategories as $k=>$category){?>
        <?if(!empty($category['products'])){?>
<section class="content">
    <section class="content-header">
      <h2><?=$category['name']?></h2>
    </section>
</section>
<section class="content">
    <div class="table-responsive table-4">
        <table>
            <tr>
                <th class="th-serial-number"></th>
                <th style="width:90px">
                    Purchase Date
                </th> 
                <th>
                    Added By          
                </th>
                <th>
                    Name          
                </th>                
                <th>
                    Vendor 
                </th>                             
            </tr>
            <?php foreach($category['products'] as $k=>$item){?>
            <tr>                
                <td><span><?=$k+1?></span></td>
                <td><span><?=strftime(DATE_FORMAT, $item['purchase_date'])?></span></td>
                <td><a href="edit_employee.php?id=<?=$item['added_by']?>"><span><?=$item['addedby']?></span></a></td>
                <td><a href="inventory_edit_goods_item.php?cat=<?=$category['id']?>&id=<?=$item['id']?>"><span><?=$item['name']?></span></a></td>                
                <td><span><?=$item['vendorname']?></span></td>                
            </tr>
            <?php }?>
        </table>
    </div>
</section>
        <?}?>
    <?}?>
<?}?>

<div id="mobileCalendar"><?include '_calendar_mobile_tpl.php'?></div>

<?include '_footer_tpl.php'?>