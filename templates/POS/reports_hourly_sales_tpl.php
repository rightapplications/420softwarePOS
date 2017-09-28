<? include '_header_tpl.php' ?>

<?include '_reports_list_tpl.php'?>

<style>
.pa-select-table .select-block-1 {
    width: auto;
    margin-right: 10px;
}
@media (max-width: 767px) {
    .pa-select-table .select-block-1 {
    color: #9e9e9e;
    font-size: 14px;
}
    .pa-select-table .input-pa .calendar-input {
    border-color: #959595;
    background-color: #ebebeb;
    color: #959595;
    font-size: 13px;
    height: 27px;
    width:auto;
    }
    .dt{
        text-align: center;
    }
     .dt .input-pa{
       margin:auto;
    }
    .ws{
        float:left;
        width:60px;
    }
     .we{
        float:left;
        width:60px;
    }
     .sm{
        float:left;
        width:60px;
    }
}
    .pa-select-table .reports .jq-selectbox.jqselect{
        z-index: 101!important;
    }
    .ws .jq-selectbox.jqselect{
        z-index: 9!important;
    }
    
    .we .jq-selectbox.jqselect{
        z-index: 8!important;
    }
    .button{font-size:14px;line-height: 18px;}
</style>

<section class="content">
    <div class="pa-block">
        <div class="clearfix"></div>
        <div class="pa-table pa-select-table for-desktop">
            <form action="" method="get" id="calForm">
            <table>
                <tr>
                    <td>
                        <label>Date</label>
                        <div class="input-pa">
                                <input class="form-control calendar-input" type="text" id="from" name="from" value="<?= strftime("%m/%d/%Y",$_SESSION[CLIENT_ID]['from'])?>" readonly/>
                        </div>
                    </td>
                    <td>
                        <label>Work Start</label>
                        <div class="select-1">
                            <div class="select-block-1">
                                <div class="select-1">  
                                    <select name="workstart">
                                        <?for($h=0; $h<= 23; $h++){?>
                                        <option value="<?=$h?>" <?if($h == $workStartH) echo "selected"?>><?=time24to12($h)?></option>
                                        <?}?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <label>Work End</label>
                        <div class="select-1">
                            <div class="select-block-1">
                                <div class="select-1">  
                                   <select name="workend">
                                        <?for($h=0; $h<= 23; $h++){?>
                                        <option value="<?=$h?>" <?if($h == $workEndH) echo "selected"?>><?=time24to12($h)?></option>
                                        <?}?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <label>&nbsp;</label>
                        <div class="">
                            <input type="submit" value="Submit" class="button"/>
                        </div>
                    </td>
                </tr>
            </table>
            </form>
        </div>
        <div class="pa-table pa-select-table for-mobile">
            <div class="table-responsive pa-table">
                <form action="" method="get" id="calForm2">
                    <div class="tab-cal dt">
                        <label>Date</label>
                        <div class="input-pa">
                            <input class="form-control calendar-input" type="text" id="fromMobile" name="from" value="<?= strftime("%m/%d/%Y",$_SESSION[CLIENT_ID]['from'])?>" readonly/>
                        </div>
                    </div>
                    
                    <div class="tab-cal ws">
                        <label>Work Start</label>
                        <div class="select-1">
                            <div class="select-block-1">
                                <div class="select-1">  
                                    <select name="workstart">
                                        <?for($h=0; $h<= 23; $h++){?>
                                        <option value="<?=$h?>" <?if($h == $workStartH) echo "selected"?>><?=time24to12($h)?></option>
                                        <?}?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-cal we">
                        <label>Work End</label>
                        <div class="select-1">
                            <div class="select-block-1">
                                <div class="select-1">  
                                   <select name="workend">
                                        <?for($h=0; $h<= 23; $h++){?>
                                        <option value="<?=$h?>" <?if($h == $workEndH) echo "selected"?>><?=time24to12($h)?></option>
                                        <?}?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-cal sm">
                        <label>&nbsp;</label>
                        <div class="">
                            <input type="submit" value="Submit" class="button" style="height:30px;"/>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
        
    </div>
</section>

<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Hourly Sales</h2>
    </section>
</section>

<section class="content">
    <div class="table-responsive table-4 reports-table">
        <table>
            <tr>
                <th></th>
                <th><font>Period</font></th>
                <?foreach($aDays as $d=>$dname){?>
                <th><font><?=$dname?></font></th>
                <?}?>
                <th><font>TOTAL</font></th>
            </tr>
            <?
            $total = 0;
            $k=1;
            foreach ($aHours as $i => $hour) {
                ?>
            <tr>
                <td><span><?=$k?></span></td>
                <td><span><?= $hour['startFormatted'] ?> - <?= $hour['endFormatted'] ?></span></td>
                <?
                    $hourTotal = 0;
                    foreach ($aWeekDays as $d => $dname) {
                        if (isset($dayToal[$dname['day']])) {
                            $dayToal[$dname['day']]+=$dname['hours'][$i]['sales'];
                        } else {
                            $dayToal[$dname['day']] = $dname['hours'][$i]['sales'];
                        }
                        $hourTotal+=$dname['hours'][$i]['sales'];
                        $total+=$dname['hours'][$i]['sales'];
                        ?>
                    <td>
                        <? if ($dname['hours'][$i]['sales'] > 0) { ?>
                            <span>$<?= number_format($dname['hours'][$i]['sales'], 2, '.', ',') ?></span>
                        <? } else { ?>
                            <span style="color:#ccc">$0.00</span>
                    <? } ?>
                    </td>
                <? } ?>
                    <td><span><strong>$<?= number_format($hourTotal, 2, '.', ',') ?></strong></span></td>
            </tr>   
            <?$k++; }?>
            <tr>
                <td></td>
                <td><span><strong>TOTAL</strong></span></td>
                <?foreach($aDays as $d=>$dname){?>
                <td><span><strong>$<?= number_format($dayToal[$dname], 2, '.', ',') ?></strong></span></td>
                <?}?>
                <td><span><strong>$<?= number_format($total, 2, '.', ',') ?></strong></span></td>
            </tr>
        </table>
    </div>
</section>

<? include '_footer_tpl.php' ?>