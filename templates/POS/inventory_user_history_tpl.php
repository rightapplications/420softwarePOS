<?include '_header_tpl.php'?>

<section class="content">
    <section class="content-header title-page for-desktop">
        <h2>Products added by <a href="edit_employee.php?id=<?=$aUser['id']?>"><?=$aUser['firstname']?> <?=$aUser['lastname']?></a></h2>
    </section>
</section>

<section class="content">
    <section class="search">
        <div class="search-content">
            <div class="search-form">
                <div class="control-button3">
                    <div class="input-submit">
                        <input type="button" class="form-control" value="< Back" onclick="parent.location='<?=isset($_SESSION[CLIENT_ID]['return_page']) ? $_SESSION[CLIENT_ID]['return_page'] : 'inventory.php'?>'"/>
                    </div>
                </div>
            </div>
        </div>
    </section>
</section>

<?if(!empty($aCategories)){?>
    <?php foreach($aCategories as $cat){?>
        <?php if(!empty($cat['goods'])){?>
<section class="content">
    <section class="content-header">
      <h2><?=$cat['name']?></h2>
    </section>
</section>
<section class="content">
    <div class="table-responsive table-3">
        <table>
            <tr>
                <th style="width:50px" class="th-serial-number"></th>
                <th style="width:90px"><span>Date</span></th>
                <th><span>Product Name</span></th>               
            </tr> 
            <?foreach($cat['goods'] as $k=>$good){?>
            <tr>
                <td><span><?=$k+1?></span></td>
                <td><span><?=strftime(DATE_FORMAT, $good['purchase_date'])?></span></td>
                <td><a href="inventory_edit_goods_item.php?cat=<?=$good['cat_id']?>&amp;id=<?=$good['id']?>"><span><?=$good['name']?></span></a></td>
            </tr>
            <?}?>
        </table>
    </div>
</section>
        <?php }?>
    <?php }?>
<?}?>

<?include '_footer_tpl.php'?>