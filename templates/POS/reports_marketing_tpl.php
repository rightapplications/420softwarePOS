<?include '_header_tpl.php'?>

<?include '_reports_list_tpl.php'?>

<?include '_calendar_tpl.php'?>

<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Maketing Report</h2>
    </section>
</section>


<section class="content">
    <div class="table-responsive table-3 employees-list">
        <table>
            <tr>
                <th class="th-serial-number"></th>
                <th>Name</th>
                <th width="200" class="centered"><?sortableHeader('Number of Patients', 'reports_marketing.php', 'quantity', $ord, true, $ordby)?></th>
                <th width="200" class="centered">%</th>
            </tr>
            <?if(!empty($aSources)){?> 
                <?foreach($aSources as $k=>$v){?>
            <tr>
                <td class="td-serial-number"><?=$k+1?></td>
                <td>
                    <div class="cont-t3"><span><?=@$aMarketingSources[$v['source']]?></span></div>
                <td>
                   <div class="cont-t3"><span><?=$v['quantity']?></span></div>
                </td>
                <td>
                   <div class="cont-t3"><span><?=$v['percent']?></span></div>
                </td>
            </tr>
                <?}?>
            <?}?>
        </table>
    </div>
</section>

 <div id="mobileCalendar"><?include '_calendar_mobile_tpl.php'?></div>

<?include '_footer_tpl.php'?>