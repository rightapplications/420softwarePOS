<?include '_header_tpl.php'?>

<?include '_reports_list_tpl.php'?>

<?include '_calendar_tpl.php'?>

<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Labor</h2>
    </section>
</section>

<?if(!empty($aEmployees)){?>
<section class="content">
    <div class="table-responsive table-2">
        <table>
            <tr>
                <th class="th-serial-number"></th>
                <th><?sortableHeader('Employee Name', 'reports_labor.php', 'name', $ord, true, $ordby)?></th>
                <th><?sortableHeader('Hours Worked', 'reports_labor.php', 'worktime', $ord, false, $ordby)?></th>
                <th><?sortableHeader('Labor Cost', 'reports_labor.php', 'salary', $ord, false, $ordby)?></th>
                <th><?sortableHeader('Hourly Pay', 'reports_labor.php', 'hourlyPay', $ord, false, $ordby)?></th>
                <th><?sortableHeader('Gross Sales', 'reports_labor.php', 'grossSales', $ord, false, $ordby)?></th>
                <th><?sortableHeader('Net Sales', 'reports_labor.php', 'netSales', $ord, false, $ordby)?></th>
            </tr>
            <?
            $totalHours = $totalLaborCost = $totalGross = $totalNet = 0;
            foreach($aEmployees as $k=>$empl){
                $totalHours+=$empl['worktime'];
                $totalLaborCost+=$empl['salary'];
                $totalGross+=$empl['grossSales'];
                $totalNet=$empl['netSales'];
                        ?>
            <tr>
                <td><span><?=$k+1?></span></td>
                <td><span><?=$empl['name']?></span></td>
                <td><span><?=$empl['worktimeHoursMins']?></span></td>
                <td><span>$<?=number_format($empl['salary'],2,'.',',')?></span></td>
                <td><span>$<?=number_format($empl['hourlyPay'],2,'.',',')?></span></td>
                <td><span<?php if($empl['grossSales'] < 0) echo " class='red'"?>>$<?=number_format($empl['grossSales'],2,'.',',')?></span></td>
                <td><span<?php if($empl['netSales'] < 0) echo " class='red'"?>>$<?=number_format($empl['netSales'],2,'.',',')?></span></td>
            </tr>
            <?}?>
            <tr>
                <td></td>
                <td><span><strong>TOTAL</strong></td>
                <td><span><strong><?=secToHoursMin($totalHours, true)?></strong></span></td>
                <td><span><strong>$<?=number_format($totalLaborCost,2,'.',',')?></strong></span></td>
                <td>&nbsp;</td>
                <td><span<?php if($totalGross < 0) echo " class='red'"?>><strong>$<?=number_format($totalGross,2,'.',',')?></strong></span></td>
                <td><span<?php if($totalNet < 0) echo " class='red'"?>><strong>$<?=number_format($totalNet,2,'.',',')?></strong></span></td>
            </tr>
        </table>
    </div>
</section>
<?}?>

<div id="mobileCalendar"><?include '_calendar_mobile_tpl.php'?></div>

<?include '_footer_tpl.php'?>
