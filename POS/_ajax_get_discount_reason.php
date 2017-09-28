<?php
include_once '../includes/common.php';
if(checkAccess(array('1'), '')){
    $order_id = intval($_GET['order_id']);
    $reason = $oOrder->get_discount_reason($order_id);
    ?>
<div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel">Discount reason</h4>
                          </div>
                          <div class="modal-body">
                          <?=$reason?>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                          </div>
    <?php
}
