<?include '_header_tpl.php'?>
<style>
.search-form .form-control {
    height: 43px;
    padding: 8px 14px;
    margin:0 auto;
}
.search-form{width:100%}
.search-form .input-submit {
    width: 80px;
}
.search-form .input-submit .form-control {
    min-width: 80px;
    font-size: 16px;
    padding: 9px 16px;
}
.search-form .lupa {
    cursor:pointer;
}
.search, .searchContent{margin-bottom:0px}
.searchLabel{
    display:block;
    padding:0 5px;
    font-size:10px;
    color:#aaa;  
    text-transform: uppercase;
}
@media (max-width: 767px) {
.table-3 td span {
    padding: 0 5px;
}
}
</style>
<script>
$(document).ready(function(){
    $('.lupa').click(function(){
        $('#searchInvBtn').click();
    });
});
</script>
<?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
<?include '_reports_list_tpl.php'?>
<?}else{?>
<br />
<?}?>

<?include '_calendar_tpl.php'?>


<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Patients Purchase History</h2>
    </section>
</section>

<section class="content"> 
    <section class="search">
        <div class="search-content">
            <div class="search-form">
                <form action="reports_patients_history.php" method="get">
                    <div class="control-button1">
                    <div class="lupa"><i class="fa fa-search"></i><span class="searchLabel for-mobile">search</span></div>
                    <div class="input-search" style="width:91%">
                        <input type="text" class="form-control" name="search" placeholder="Type here to search" value="<?=isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''?>"/>
                    </div>
                    </div>
                    <div class="control-button2">
                    <div class="input-submit">
                        <input type="submit" class="form-control for-desktop" value="Search" id="searchInvBtn"/>
                    </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</section>

<?if(!empty($aHistory)){?>
<section class="content">
    <div class="table-responsive table-3">
        <table>
            <tr>
                <th class="th-serial-number"></th>
                <th><?sortableHeader('Patients Name', 'reports_patients_history.php', 'firstname', $ord, false, $ordby)?></th>
                <th><?sortableHeader('Visits', 'reports_patients_history.php', 'numOrders', $ord, false, $ordby)?></th>
                <th><?sortableHeader('Latest Order', 'reports_patients_history.php', 'latestOrderDate', $ord, true, $ordby)?></th>
            </tr>
            <?php
            foreach($aHistory as $k=>$patient){?>
            <tr>
                <td><span><?=$k+1?></span></td>
                <td><a href="reports_patient_history.php?id=<?=$patient['id']?>"><span><?=$patient['firstname']?> <?=$patient['lastname']?></span></a></td>
                <td><span><?=$patient['numOrders']?></td>
                <td><span><?=strftime(DATE_FORMAT." %I:%M%p", $patient['latestOrderDate'])?></td>
            </tr>
            <?php }?>
        </table>
    </div>
</section>
<?}?>


<div id="mobileCalendar"><?include '_calendar_mobile_tpl.php'?></div>

<?include '_footer_tpl.php'?>