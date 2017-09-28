<script>
$(document).ready(function(){
    $('#selectmenu').change(function(){
        parent.location = $(this).val();
    });
});
</script>
<!-- start content title-page -->
<section class="content reports">
    <section class="content-header title-page">
        <div class="select-block-1 select-title-page">
            <div class="select-1">							
                <select id="selectmenu">
                    <option value="reports.php" <?if($reportName == 'sales'){?>selected="selected"<?}?>>Sales</option>
                    <option value="reports_employee_sales.php" <?if($reportName == 'employee_sales'){?>selected="selected"<?}?>>Employee Sales</option>
                    <option value="reports_top_products.php" <?if($reportName == 'top_products'){?>selected="selected"<?}?>>Top Products</option>
                    <option value="reports_invoices.php" <?if($reportName == 'invoices'){?>selected="selected"<?}?>>Invoices</option>
                    <option value="reports_discounts.php" <?if($reportName == 'discounts'){?>selected="selected"<?}?>>Discounts</option>
                    <option value="reports_comps.php" <?if($reportName == 'comps'){?>selected="selected"<?}?>>Comps</option>
                    <option value="reports_losses.php" <?if($reportName == 'losses'){?>selected="selected"<?}?>>Losses</option>
                    <option value="reports_patients_history.php" <?if($reportName == 'history'){?>selected="selected"<?}?>>Patients Purchase History</option>
                    <option value="reports_visits.php" <?if($reportName == 'visits'){?>selected="selected"<?}?>>Visits</option>
                    <option value="reports_hourly_sales.php" <?if($reportName == 'hourly_sales'){?>selected="selected"<?}?>>Hourly Sales</option>
                    <option value="reports_delivery.php" <?if($reportName == 'delivery'){?>selected="selected"<?}?>>Delivery</option>
                    <option value="reports_credit_cards.php" <?if($reportName == 'cc'){?>selected="selected"<?}?>>Credit Card Transactions</option>
                    <option value="reports_labor.php" <?if($reportName == 'labor'){?>selected="selected"<?}?>>Labor</option>
                    <option value="reports_inventory.php" <?if($reportName == 'inventory'){?>selected="selected"<?}?>>Inventory</option>
                    <option value="inventory_deleting_history.php" <?if($reportName == 'deleting_history'){?>selected="selected"<?}?>>Deleting History</option>
                    <option value="inventory_change_price_history.php" <?if($reportName == 'price_history'){?>selected="selected"<?}?>>Price Changing History</option>
                    <option value="inventory_iou_history.php" <?if($reportName == 'iou_history'){?>selected="selected"<?}?>>IOU History</option>
                    <option value="reports_vendors.php" <?if($reportName == 'vendors'){?>selected="selected"<?}?>>Vendors</option>
                    <option value="reports_marketing.php" <?if($reportName == 'marketing'){?>selected="selected"<?}?>>Marketing Report</option>
                    <?if($_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1){?>
                    <option value="reports_expense.php" <?if($reportName == 'expense'){?>selected="selected"<?}?>>Expenses</option>
                    <?}?>
                </select>							
            </div>
        </div>
    </section>
</section>
<!-- stop content title-page -->