<style>
	.filter-select {
		display: block;
	}
</style>
<td>
    <label>Category</label>
    <div class="select-block-1 input-pa filter-select">
    	<select name="invoice_category">
            <option value="">All</option>;
            <?php $selected_category = intval($_GET['invoice_category']);
            foreach($aInvoiceCategories as $k=>$v){ ?>
            	<option <?=$selected_category == $k ? 'selected="selected"' : ''?>value="<?=$v['id'];?>"><?=$v['name'];?></option>
            <?php } ?>
        </select>
    </div>
</td>