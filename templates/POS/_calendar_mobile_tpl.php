<style>
    .pa-table table, .pa-table table td{overflow:hidden}
    @media (max-width: 767px) {
    .input-pa .calendar-input {
    border-color: #959595;
    background-color: #ebebeb;
    color: #959595;
    font-size: 13px;
    height: 27px;
    }
}
</style>
<section class="content">
    <div class="pa-block">
        <div class="clearfix"></div>
        <div class="table-responsive pa-table">
            <form action="" method="get" id="calFormMobile">

                <div class="clearfix butons-3sh">
                    <a href="#" class="prev_day" title="Previous day" id="prevBtnMobile"><i class="fa fa-angle-double-left" aria-hidden="true"></i></a>
                    <button class="button today" id="todayBtnMobile">Today</button>
                    <?if($_SESSION[CLIENT_ID]['to'] < time()){?>
                    <a href="#" class="next_day" title="Previous day" id="nextBtn"><i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
                    <?}?>
                </div>
                <div class="clearfix"></div>
                
                <div class="tab-cal">
                    <div class="td-cal">
                        <label>Date From</label>
                        <div class="input-pa">
                            <input class="form-control calendar-input" type="text" id="fromMobile" name="from" value="<?= strftime("%m/%d/%Y",$_SESSION[CLIENT_ID]['from'])?>" readonly/>
                        </div>
                    </div>
                    <div class="td-cal">
                        <label>Date To</label>
                        <div class="input-pa">
                            <input class="form-control calendar-input" type="text" id="toMobile" name="to" value="<?= strftime("%m/%d/%Y",$_SESSION[CLIENT_ID]['to'])?>" readonly/>
                        </div>
                    </div>
                </div>
                <?=!empty($additional_filter) ? $additional_filter : '';?>
                
                <div class="butl">
                    <button class="button">Submit</button>                                
                </div>
            </form>
        </div>
    </div>
</section>