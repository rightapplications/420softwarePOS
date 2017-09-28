<section class="content">
    <div class="block-bordtop"></div>
    <div class="clearfix"></div>
    <h4>Changes history</h4>
	<?php foreach($history as $record) {?>
		<div class="block-bordtop"></div>
		<div>
			<p><b>Date:</b> <?=date('m/d/Y', $record['date']);?></p>
			<p><b>Changed by:</b> <?=$record['user_name'];?></p>
			<?php if($record['old_price'] != $record['new_price']){?><p><b>Price changed:</b> from <?=$record['old_price'];?> to <?=$record['new_price'];?></p><?php } ?>
			<?php if($record['stock_added']){?><p><b>Stock added:</b> <?=$record['stock_added'];?></p><?php } ?>
		</div>
	<?php } ?>
</section>