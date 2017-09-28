<style>
	.invoice-cal {
		margin-bottom: 20px;
		text-align: center;
	}
	.invoice-cal .filter-select {
		display: block;
		margin: auto;
	}
</style>
<div class="invoice-cal">
    <label>Category</label>
    <div class="select-block-1 input-pa filter-select">
    	<select name="invoice_category">
            <option value="">All</option>;
            <?php foreach($aInvoiceCategories as $k=>$v){ ?>
            	<option value="<?=$v['id'];?>"><?=$v['name'];?></option>
            <?php } ?>
        </select>
    </div>
</div>