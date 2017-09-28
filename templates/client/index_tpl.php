<?include '_header_tpl.php'?>

<section id="invitation">
    <div class="invitation">
        <h2>Welcome to <?=SITE_NAME?></h2>
    </div>
</section>

<div class="yourorder-page" id="order" style="display:none;">
    <div class="yourorder-content">

        <div class="yourorder-header">
            <span>YOUR ORDER</span>
        </div>
        <!-- stop yourorder-header -->
        <div class="yourorder-filling"  id="orderDetails"></div>
        <!-- start yourorder-header -->        
    </div> 
    <!-- start yourorder-footer -->
    <div class="yourorder-footer"></div>
    <!-- stop yourorder-footer --> 
</div>

<?include '_footer_tpl.php'?>