<?include '_header_tpl.php'?>

<?include '_reports_list_tpl.php'?>

<?include '_calendar_tpl.php'?>

<link rel="stylesheet" href="css/orders_history.css">

<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Delivery</h2>
    </section>
</section>

<!-- start -->
<section class="content">
    <div class="table-sl1">
        <table>
            <tr>
                    <td>Total Deliveries</td>
                    <td><?=$count?></td>
            </tr>
            <tr>
                    <td>Gross Sales</td>
                    <td>$<?=number_format($gross,2,'.',',')?></td>
            </tr>
            <tr>
                    <td>Net Sales</td>
                    <td>$<?=number_format($net,2,'.',',')?></td>
            </tr>
        </table>
    </div>
    <?php if($count){ ?>
		<button type="button" class="load_history">Orders history</button>
		<div class="orders_history"></div>
		<script type="text/template" class="order_template">
			<div class="order">
				<a class="id"></a>
				<div class="date"></div>
				<div class="patient"></div>
				<div class="address"></div>
				<div class="driver"></div>
				<div class="items"></div>
				<div class="total"></div>
			</div>
		</script>
		<script type="text/javascript">history_range = <?=json_encode(['from' => $_SESSION[CLIENT_ID]['from'], 'to' => $_SESSION[CLIENT_ID]['to']]);?>;</script>
		<script type="text/javascript" src="js/orders_history.js"></script>
	<?php } ?>
</section>
<!-- stop -->
<div id="mobileCalendar"><?include '_calendar_mobile_tpl.php'?></div>

<?include '_footer_tpl.php'?>