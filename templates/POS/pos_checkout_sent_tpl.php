<?include '_header_tpl.php'?>

<!-- start content -->
<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Checkout</h2>
    </section>
</section>
<!-- stop content -->

<section class="content">
    <div class="checkou-block">
        <div class="checkou-content">
        <br />
        <p>Order <strong>#<?=$orderNum?></strong> has been sent to the cashier</p>
        <br />
        <button class="button" onclick="parent.location='pos.php'">New Order</button>
        </div>
    </div>
</section>

<?include '_footer_tpl.php'?>