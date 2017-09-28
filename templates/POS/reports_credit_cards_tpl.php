<?include '_header_tpl.php'?>

<?include '_reports_list_tpl.php'?>

<?include '_calendar_tpl.php'?>

<link rel="stylesheet" href="css/orders_history.css">

<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Credit Card Transactions</h2>
    </section>
</section>

<!-- start -->
<section class="content">
    <div class="table-sl1">
        <table>
            <tr>
                    <td>Total</td>
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
</section>
<!-- stop -->
<div id="mobileCalendar"><?include '_calendar_mobile_tpl.php'?></div>

<?include '_footer_tpl.php'?>