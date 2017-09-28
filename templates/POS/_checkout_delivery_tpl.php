<?php 
include_once '../includes/driver.php';

$address = $name = $appointment = '';
if(isset($aPatient)) {
	$address = implode(' ', array_filter([$aPatient['street'], $aPatient['city'], $aPatient['state']]));
	$name = $aPatient['firstname'].' '.$aPatient['lastname'];
}
if(isset($_SESSION[CLIENT_ID]['delivery'])) {
	$address = $_SESSION[CLIENT_ID]['delivery']['address'];
	$appointment = $_SESSION[CLIENT_ID]['delivery']['appointment'];
} ?>
<style>
	.error {
		border: 1px solid red;
	}
</style>
<div class="modal fade" id="delivery_modal" tabindex="-1" role="dialog" aria-labelledby="delivery_title">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="_checkout_delivery.php" method="post" class="singleForm">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="delivery_title">Delivery options</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group delivery_patient">
                        <label><span>Patient</span></label>
                        <div class="box-input">
                            <input type="text" class="form-control" id="delivery_patient" name="patient" value="<?=$name;?>" />
                            <div id="delivery_patients"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><span>Address</span></label>
                        <div class="box-input"><input type="text" class="form-control" id="delivery_address" name="address" value="<?=$address;?>" /></div>
                    </div>
                    <div class="form-group">
                        <label><span>Appointment time</span></label>
                        <div class="box-input"><input type="text" class="form-control" id="appointment" name="appointment" value="<?=$appointment;?>" /></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="delivery_options_apply">Apply</button>
                </div>
            </form>
        </div>
    </div>
</div>