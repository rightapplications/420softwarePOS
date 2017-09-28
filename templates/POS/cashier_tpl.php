<?include '_header_tpl.php'?>
<style>
.title-page {
    padding: 25px 0 42px 0;
    margin: 0 0 20px 0;
    min-height: 62px;
    height: 62px;
    z-index: 10;
}
.title-page .form-control {
    width: 150px;
    position: relative;
    top: -3px;
    margin-left:10px;
}
.blocks-f2-repeat{width:170px;}
.f2-file{height:150px; text-align: center}
.blocks-f2-repeat p.f-block {
    padding: 10px 10px 7px 10px;
    margin: 0;
    font-size: 12px;
    text-transform: uppercase;
    font-weight: 700;
    line-height: normal;
    border-top: 1px solid #cfcfcf;
    border-bottom: none;
}
.table-2{
    margin-bottom: 20px;
}
</style>
<script type='text/javascript' src='js/StarWebPrintBuilder.js'></script>
<script type='text/javascript' src='js/StarWebPrintTrader.js'></script>
<script>
var printerURL = '<?=PRINTER_URL?>/api/print/';   
</script>
<script type="text/javascript" src="js/cashier.js"></script>
<?php if($allow_open_cashdrawer){?>
<script type='text/javascript'>
$(document).ready(function(){
    sendOpenCashRegister();
});
</script>
<?php }?>
<section class="content">
    <section class="content-header title-page">
      <h2>Orders <input type="button" value="Open Reg" class="form-control btn ps-btn" onclick="$('#cdpModal').modal();" style="color:#fff;background-color: #34a136;border-color:#008702"/></h2>
    </section>
</section>



    
    <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mylModalLabelCDP" id="cdpModal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabelCDP">Enter Password</h4>
          </div>
          <div class="modal-body">
              <form action="" method="post">
                <div class="form-group">
                  <div class="box-input">
                  <input type="password" name="cd_pass" class="form-control" style=""/><br />
                  <input type="submit" value="Submit" class="btn btn-primary" style=""/>
                  <div class="clearfix"></div>
                  </div>
                </div>
              </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>      



<section class="content">
    <div id="orders"></div>
</section>

<?include '_recent_transactions_tpl.php'?>

<?include '_footer_tpl.php'?>