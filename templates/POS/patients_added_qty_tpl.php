<?include '_header_tpl.php'?>

<script>
$(document).ready(function(){
    $('#selectmenu').change(function(){
        parent.location = $(this).val();
    });
});
</script>
<section class="content">
    <section class="content-header title-page">
        <div class="select-block-1 select-title-page">
            <div class="select-1">							
                <select id="selectmenu">
                    <option value="patients.php">Patients Lookup</option>
                    <option value="edit_patient.php">Add Patient</option>
                    <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
                    <option value="patients_added_qty.php" selected="selected">Adding Report</option>
                    <?}?>
                </select>							
            </div>
        </div>
    </section>
</section>

<section class="content">
    <p class="p-border-left"><span></span>Patients added:</p>		
</section>

<?include '_calendar_tpl.php'?>

<section class="content">
    <?if(!isset($error)){?>
    <div class="table-responsive table-variant1">
        <table>
            <tr>
                <th>||</th>
                <th><font>Day</font></th>
                <th><font>Number of Patients</font></th>
            </tr>
            <?
            $total = 0;
            foreach($aDateRange as $k=>$date){
                $total+=$date['count'];
            ?>
            <tr>
                <td>||</td>
                <td><?=strftime("%m/%d/%Y", $date['start'])?></td>
                <td><?=$date['count']?></td>
            </tr>
            <?}?>
        </table>
        <?if(count($aDateRange)>1){?>
        <div class="total-ar pull-right">
            <div class="total-ar-button">
                <span>TOTAL:</span>
                <font><?=$total?></font>
            </div>
        </div>
        <?}?>
        <div class="clearfix"></div>
    </div>
    <?}else{?>
    <p class="error"><?=$error?></p>
    <?}?>
</section>

<div id="mobileCalendar"><?include '_calendar_mobile_tpl.php'?></div>

<?include '_footer_tpl.php'?>