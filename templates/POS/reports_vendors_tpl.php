<?include '_header_tpl.php'?>

<?include '_reports_list_tpl.php'?>

<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Vendors</h2>
    </section>
</section>

<section class="content">
    <button class="button" onclick="parent.location='reports_edit_vendor.php'">Add New</button>
    <?php if(isset($_SESSION[CLIENT_ID]['return'])){?>
    <button class="button" onclick="parent.location='<?=$_SESSION[CLIENT_ID]['return']?>'">< Back</button>
    <?php }?>
</section>

<section class="content">
    <div class="table-responsive table-3 employees-list">
        <table>
            <tr>
                <th class="th-serial-number"></th>
                <th>Name</th>
                <th width="100" class="centered">Phone</th>
                <th width="200" class="centered">Email</th>
                <th width="200" class="centered">Contact Person</th>
                <th width="80" class="centered">&nbsp;</th>
            </tr>
            <?if(!empty($aVendors)){?> 
                <?foreach($aVendors as $k=>$vnd){?>
            <tr>
                <td class="td-serial-number"><?=$k+1?></td>
                <td>
                    <div class="cont-t3"><a href="reports_edit_vendor.php?id=<?=$vnd['id']?>"><span><?=$vnd['name']?></span></a></div>
                </td>
                <td>
                    <div class="cont-t3"><span><?=$vnd['phone']?></span></div>
                </td>
                <td>
                    <div class="cont-t3"><a href="mailto:<?=$vnd['email']?>"><span><?=$vnd['email']?></span></a></div>
                </td>
                <td>
                   <div class="cont-t3"><span><?=$vnd['contact_person']?></span></div>
                </td>
                <td class="td-delite">
                    <a href="reports_vendors.php?delete=<?=$vnd['id']?>" title="delete" onclick="return confirm('Are you sure you want to delete vendor \'<?=  htmlspecialchars(str_replace('\'', '`', $vnd['name']))?>\'?')">
                        <span><font><i class="fa fa-times"></i></font><font>delete</font></span>
                    </a>
                </td>
            </tr>
                <?}?>
            <?}?>
        </table>
    </div>
</section>

<?include '_footer_tpl.php'?>