<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" id="addModal">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Change Price/Amount</h4>
			</div>
			<div class="modal-body">
				<form action="_ajax_add_stock.php" method="post" id="addForm">
					<div class="form-group">
						<label>Purchase price:</label>
						<input type="text" name="price" value="" class="form-control"/>
						<div class="help-block"></div>
					</div>
					<div class="form-group">
						<label>Add stock:</label>
						<input type="text" name="stock" value="" class="form-control"/>
						<div class="help-block"></div>
					</div>
					<div class="error"></div>
					<p><input type="button" class="btn btn-primary" id="addBtn" value="Submit"/></p>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<script src="js/inventory_stock.js"></script>