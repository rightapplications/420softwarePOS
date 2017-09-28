<script>
    $(document).ready(function(){
        $('#todayBtn, #todayBtnMobile').click(function(){                                
            var startDayObj = new Date();
            var endDayObj = new Date();               
            setDay(startDayObj, endDayObj);
            return false;
        });
        $('#prevBtn, #prevBtnMobile').click(function(){
            var sCurrDay = '<?= strftime("%d %b %Y 00:00:00",$_SESSION[CLIENT_ID]['from'])?>';
            var eCurrDay = '<?= strftime("%d %b %Y 00:00:00",$_SESSION[CLIENT_ID]['to'])?>';                
            var actualStartDate = new Date(sCurrDay);
            var actualEndDate = new Date(eCurrDay);
            var oneDay = 24*60*60*1000;
            var diffDays = Math.round(Math.abs((actualStartDate.getTime() - actualEndDate.getTime())/(oneDay)))+1;               
            var sPrevDay = new Date(actualStartDate.getFullYear(), actualStartDate.getMonth(), actualStartDate.getDate()-diffDays);
            var ePrevDay = new Date(actualEndDate.getFullYear(), actualEndDate.getMonth(), actualEndDate.getDate()-diffDays);
            setDay(sPrevDay, ePrevDay);
            return false;
        });
        $('#nextBtn, #nextBtnMobile').click(function(){
            var sCurrDay = '<?= strftime("%d %b %Y 00:00:00",$_SESSION[CLIENT_ID]['from'])?>';
            var eCurrDay = '<?= strftime("%d %b %Y 00:00:00",$_SESSION[CLIENT_ID]['to'])?>';
            var actualStartDate = new Date(sCurrDay);
            var actualEndDate = new Date(eCurrDay);
            var oneDay = 24*60*60*1000;
            var diffDays = Math.round(Math.abs((actualStartDate.getTime() - actualEndDate.getTime())/(oneDay)))+1;
            var sNextDay = new Date(actualStartDate.getFullYear(), actualStartDate.getMonth(), actualStartDate.getDate()+diffDays);
            var eNextDay = new Date(actualEndDate.getFullYear(), actualEndDate.getMonth(), actualEndDate.getDate()+diffDays);
            setDay(sNextDay, eNextDay);
            return false;
        });

        function setDay(startDayObj, endDayObj){                
            var smonth = startDayObj.getMonth()+1;
            if(smonth < 10){
                smonth = '0'+smonth;
            }
            var sday = startDayObj.getDate();
            if(sday < 10){
                sday = '0'+sday;
            }                
            var syear = startDayObj.getFullYear();
            var startDayString = smonth+'/'+sday+'/'+syear;                
            $('#calFormM, #calFormMobile').find('input[name="from"]').val(startDayString);

            var emonth = endDayObj.getMonth()+1;
            if(emonth < 10){
                emonth = '0'+emonth;
            }
            var eday = endDayObj.getDate();
            if(eday < 10){
                eday = '0'+eday;
            }                
            var eyear = endDayObj.getFullYear();
            var endDayString = emonth+'/'+eday+'/'+eyear;                
            $('#calFormM, #calFormMobile').find('input[name="to"]').val(endDayString);

            $('#calFormM, #calFormMobile').submit();
        }
    });
</script>
<div id="desktopCalendar">
<section class="content">
    <div class="pa-block">
        <div class="clearfix"></div>
        <div class="table-responsive pa-table">
            <form action="" method="get" id="calForm">
            <table>
                <tr>
                    <td>
                        <label>Date From</label>
                        <div class="input-pa">
                                <input class="form-control calendar-input" type="text" id="from" name="from" value="<?= strftime("%m/%d/%Y",$_SESSION[CLIENT_ID]['from'])?>" readonly/>
                        </div>
                    </td>
                    <td>
                        <label>Date To</label>
                        <div class="input-pa">
                                <input class="form-control calendar-input" type="text" id="to" name="to" value="<?= strftime("%m/%d/%Y",$_SESSION[CLIENT_ID]['to'])?>" readonly/>
                        </div>
                    </td>
                    <?=!empty($additional_filter) ? $additional_filter : '';?>
                    <td>
                        <label>&nbsp;</label>
                        <div class="butl">
                                <button class="button">Submit</button>
                                <a href="#" class="prev_day" title="Previous day" id="prevBtn"><i class="fa fa-angle-double-left" aria-hidden="true"></i></a>
                                <button class="button today" id="todayBtn">Today</button>
                                <?if($_SESSION[CLIENT_ID]['to'] < time()){?>
                                <a href="#" class="next_day" title="Next day" id="nextBtn"><i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
                                <?}?>
                        </div>
                    </td>
                </tr>
            </table>
            </form>
        </div>
    </div>
</section>
</div>