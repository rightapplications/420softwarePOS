<?include '_header_tpl.php'?>

<?include '_reports_list_tpl.php'?>

<?include '_calendar_tpl.php'?>

<section class="content">
    <h3>Remove Data</h3>
    <div class="table-sl1"> 
        <table>
            <tr>
                <td>Orders Number</td>
                <td><?=$numOrders?></td>
            </tr>
            <tr>
                <td>Orders Amount</td>
                <td>$<?=number_format($amtOrders,2,'.',',')?></td>
            </tr>           
        </table>
    </div>
    
    <div class="dataGrid">
        <?if(isset($_GET['df']) and !empty($_GET['df']) and isset($_GET['dt']) and !empty($_GET['dt']) and (!isset($_GET['delete_token']) or empty($_GET['delete_token']))){?>
        <p class="warning">Are you sure you want to delete selected period's sales?</p>
        <input class="button" type="button" value="YES" style="background-color: #f00" onclick="parent.location='reports_remove_data.php?df=<?=$_SESSION[CLIENT_ID]['from']?>&dt=<?=$_SESSION[CLIENT_ID]['to']?>&delete_token=<?=$delete_token?>'">
        <input class="button" type="button" value="NO" onclick="parent.location='reports.php'">
        <?}elseif(isset($_GET['df']) and !empty($_GET['df']) and isset($_GET['dt']) and !empty($_GET['dt']) and isset($_GET['delete_token']) and !empty($_GET['delete_token'])){?>
        <?if($res == 'ok'){?>
        <p class="result">Your sales have been deleted.</p>
        <?}?>
        <input class="button" type="button" value="<< Back" onclick="parent.location='reports.php'">
        <?}else{?>
        <input class="button" type="button" value="Remove" style="background-color: #f00" onclick="parent.location='reports_remove_data.php?df=<?=$_SESSION[CLIENT_ID]['from']?>&dt=<?=$_SESSION[CLIENT_ID]['to']?>'">        
        <input class="button" type="button" value="Cancel" onclick="parent.location='reports.php'">
        <?}?>
    </div>

</section>

<div id="mobileCalendar"><?include '_calendar_mobile_tpl.php'?></div>

<?include '_footer_tpl.php'?>