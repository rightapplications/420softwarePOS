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
.table-4 td span {
    padding: 0 5px;
    overflow: hidden;
    line-height: initial;
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
<?include '_reports_list_tpl.php'?>

<?include '_calendar_tpl.php'?>
<style>
    .table-responsive{margin-bottom:20px;}
</style>

<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Employee Sales</h2>
    </section>
</section>

<section class="content"> 
    <section class="search">
        <div class="search-content">
            <div class="search-form">
                <form action="reports_employee_sales.php" method="get">
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

<section class="content">
    <?if(!empty($aEmployees)){?>
        <?foreach($aEmployees as $empl){?>
    <div class="data-recent">
        <span class="name"><?=$empl['firstname']?> <?=$empl['lastname']?></span><br /> 
        <span class="name">Total Sales: $<?=number_format($empl['total_sales'],2,'.',',')?></span><br />
        <span class="name">Transactions: <a href="reports_patients_served.php?user=<?=$empl['user_id']?>"><?=$empl['patients_served']?></a></span>            
    </div>
    <div class="table-responsive table-4">
        <?if(!empty($empl['sold_items'])){?>
        <table>
            <tr>
                <th></th>
                <th><?sortableHeader('Product Name', 'reports_employee_sales.php?search='.(isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''), 'goods_item_name', $ord, false, $ordby)?></th>
                <th><?sortableHeader('<font class="for-desktop">Quantity Sold</font><font class="for-mobile">Sold</font>', 'reports_employee_sales.php?search='.(isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''), 'q', $ord, true, $ordby)?></th>                
                <th><?sortableHeader('<font class="for-desktop">Amount</font><font class="for-mobile">Amt</font>', 'reports_employee_sales.php?search='.(isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''), 'p', $ord, false, $ordby)?></th>
                <th><?sortableHeader('Date/Time', 'reports_employee_sales.php?search='.(isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''), 'date', $ord, false, $ordby)?></th>
            </tr>
            <?
            $k=1;
            foreach($empl['sold_items'] as $item){?>
            <tr>
                <td><span><?=$k?></span></td>
                <td><a href="reports_product_details.php?id=<?=$item['goods_item_id']?>"><span><?=$item['goods_item_name']?></span></a></td>
                <td><span><?=$item['q']?><font class="for-desktop"> <?=$item['modifier_name']?></font></span></td>           
                <td><span>$<?=number_format($item['p'],2,'.',',')?></span></td>
                <td><span class="for-desktop"><?=strftime(DATE_FORMAT." %I:%M%p", $item['date'])?></span><span class="for-mobile"><?=strftime(DATE_FORMAT."<br />%I:%M%p", $item['date'])?></span></td>
            </tr>
            <?$k++;}?>
        </table>        
        <?}?>
    </div>
        <?}?>
    <div class="clearfix"></div>
    <?}?>
</section>

<div id="mobileCalendar"><?include '_calendar_mobile_tpl.php'?></div>

<?include '_footer_tpl.php'?>