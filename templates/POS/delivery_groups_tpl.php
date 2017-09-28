<?php include '_header_tpl.php';
$status_labels = [
	GROUP_STATUS_NEW => 'new',
	GROUP_STATUS_READY => 'ready',
	GROUP_STATUS_LOADED => 'delivering',
	GROUP_STATUS_DELIVERED => 'delivered',
]; ?>

<link rel="stylesheet" href="css/delivery_groups.css">
<div class="content dev_groups_page">
	<div class="col">
		<form action="" method="post" class="delivery_base">
			<div class="td_min">
				<label for="delivery_base">Delivery base:</label>
			</div>
			<div class="td_input">
				<input type="text" name="delivery_base" value="<?=$home?>" id="delivery_base"/>
			</div>
			<div class="td_min">
				<button type="submit">Update</button>
			</div>
		</form>
		<?php if(!empty($error)) { ?>
			<div class="error">$error</div>
		<?php } ?>
		<div id="delivery_map"></div>
		<button type="button" class="add_group">Add group</button>
		<div class="groups"></div>
	</div>
	<div class="orders col"></div>
</div>

<?php /*these are html templates that are used in javascript to create lists, since lists are dynamically added/removed/changed it is better to create them with js*/ ?>
<script type="text/template" id="order_template">
	<div class="order">
		<div class="group-sel"><div class="select"></div></div>
		<div class="id"></div>
		<div class="date"></div>
		<div class="patient"></div>
		<div class="address"></div>
		<div class="appoint"></div>
		<div class="items"></div>
		<div class="total"></div>
		<div class="directions"></div>
	</div>
</script>
<script type="text/template" id="group_template">
	<div class="group">
		<div class="orders"></div>
		<div class="group_approve" title="allow this group to be taken by driver"></div>
		<div class="group_cancel" title="remove this group and ungroup orders inside"></div>
		<div class="group_payed" title="mark this group as payed"></div>
		<div class="group_status"></div>
		<div class="group_driver"><select class="driver_change"></select></div>
		<div class="group_directions"></div>
	</div>
</script>

<script type="text/javascript">
	var home = {address: "<?=$home?>", coords: <?=$coords ? $coords : 'false';?>};
	var drivers = <?=json_encode($drivers);?>;
	var orders = <?=json_encode(array_values($orders));?>;
	var groups = <?=json_encode(array_values($groups));?>;
	var colors = <?=json_encode($color_codes);?>;
	var last_color = <?=$color;?>;
	var status_labels = <?=json_encode($status_labels);?>;
</script>
<script type="text/javascript" src="js/delivery_groups.js"></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC0qrvayv1BSCN4t1yGTtItLumHi9f6N6U&callback=init_map"></script>

<?include '_footer_tpl.php'?>