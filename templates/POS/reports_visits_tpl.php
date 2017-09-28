<?include '_header_tpl.php'?>
<style>
@media (max-width: 767px) {
    .daily{
        padding: 5px 0 5px 0;
        margin: 0 0 0px 0;
        min-height: 45px;
        height: 45px;
        z-index: 10;
    }
}
</style>
<?include '_reports_list_tpl.php'?>

<?include '_calendar_tpl.php'?>

<section class="content for-desktop">
    <section class="content-header title-page ">
      <h2>Patients Visits</h2>
    </section>
</section>

<script>
$(document).ready(function(){
    $('#selectview').change(function(){
        parent.location = $(this).val();
    });
});
</script>
<section class="content">
    <section class="content-header title-page daily">
        <div class="select-block-1 select-title-page">
            <div class="select-1">							
                <select id="selectview">
                    <option value="reports_visits.php?mode=1" <?if($mode === 1) echo 'selected="selected"'?>>Weekly</option>                    
                    <option value="reports_visits.php?mode=2" <?if($mode === 2) echo 'selected="selected"'?>>Daily</option>                  
                </select>							
            </div>
        </div>
    </section>
</section>

<section class="content">
    <div class="table-responsive table-4 reports-table">
        <table>
            <?php if($mode === 1){?>
            <tr>
                <th></th>
                <th><font>Week</font></th>
                <?foreach($aDays as $d=>$dname){?>
                <th><font><?=$dname?></font></th>
                <?}?>
                <th><font>TOTAL</font></th>
            </tr>
            <?
            $total = 0;
            foreach($aWeekIntervals as $k=>$week){?>
            <tr>
                <td><span><?=$k+1?></span></td>
                <td>
                    <span><?=strftime("%m/%d/%Y",$week['start'])?> - <?=strftime("%m/%d/%Y",$week['end'])?></span>
                </td>
                <?$weekTotal = 0;
                foreach($week['days'] as $d=>$day){
                    if(isset($dayToal[$day['day']])){
                        $dayToal[$day['day']]+=$day['orderNum'];
                    }else{
                        $dayToal[$day['day']]=$day['orderNum'];
                    }
                    $weekTotal+=$day['orderNum'];
                    $total+=$day['orderNum'];
                ?>
                <td><span><?=$day['orderNum']?></span></td>
                <?}?>
                <td><span><strong><?=$weekTotal?></strong></span></td>
            </tr>
            <?}?>
            <?if(count($aWeekIntervals)>1){?>
            <tr>
                <td></td>
                <td><span><strong>TOTAL</strong></span></td>
                <?foreach($aDays as $d=>$dname){?>
                <td><span><strong><?=$dayToal[$dname]?></strong></span></td>
                <?}?>
                <td><span><strong><?=$total?></strong></span></td>
            </tr>
            <?}?>
            <?php }else{?>
            <tr>
                <th></th>
                <th><font>Day</font></th>
                <th>Visits</th>
            </tr>
            <?
            $total = 0;
            foreach($aDaysInterval as $k=>$day){
                $total+=$day['orderNum'];
                ?>
            <tr>
                <td><span><?=$k+1?></span></td>
                <td><span><?=strftime("%m/%d/%Y %A",$day['start'])?></span></td>
                <td><span><?=$day['orderNum']?></span></td>
            </tr>
            <?}?>
            <tr>
                <td></td>
                <td><span><strong>TOTAL</strong></span></td>
                <td><span><strong><?=$total?></strong></span></td>
            </tr>
            <?php }?>
        </table>
    </div>
</section>

<div id="mobileCalendar"><?include '_calendar_mobile_tpl.php'?></div>

<?include '_footer_tpl.php'?>

