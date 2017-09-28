<?include '_header_tpl.php'?>
<?include '_reports_list_tpl.php'?>

<?include '_calendar_tpl.php'?>
<style>
    .pettycash{
        display:block;
        float:none;
        margin: 10px 0;
        width:420px;
    }
    .pettycash table{width:100%}
    .dataGrid{margin:10px;}
    
    @media (max-width: 767px) {
    .pettycash {
        display: block;
        float: none;
        margin: 10px 0;
        width: 100%;
    }
    .pettycash table td {
        text-transform: uppercase;
        padding: 15px 5px 11px 5px;
        font-size: 14px;
    }
}
a{text-decoration: underline}
</style>
<script>
$(document).ready(function(){
    $('#chartSelect').change(function(){
        $('.mobilechart').css('height','0');
        $('.mobilechart').css('visibility','hidden');        
        $($(this).val()).css('height','100%');
        $($(this).val()).css('visibility','visible');
        return false;
    });
});
$(window).load(function(){
    setTimeout(function(){
        $('.mobilechart').css('height','0');
        $('.mobilechart').css('visibility','hidden');
        $('.mobilechart').eq(0).css('height','100%');
        $('.mobilechart').eq(0).css('visibility','visible');
    }, 500);
    
});
</script>
<section class="content">     
    <div class="table-sl1" style="margin-bottom:20px;<?php if(!empty($aChartByType) or !empty($aChartByVendors)){?>float:left;margin-right:20px;<?}?>">        
        <table style="">
            <tr>
                <td>Gross Sales</td>
                <td>$<?=number_format($gross,2,'.',',')?></td>
            </tr>
            <?if($rewards){?>
            <tr>
                <td>Rewards</td>
                <td>$<?=number_format($rewards,2,'.',',')?></td>
            </tr>
            <?}?>
            <tr>
                <td>Net Sales</td>
                <td<?php if($net < 0) echo " class='red'"?>>$<?=number_format($net,2,'.',',')?></td>
            </tr>            
            <tr>
                <td>Visits</td>
                <td><?if($visits > 0){?><a href="reports_patients_served.php"><?=$visits?></a><?}else{?>0<?}?></td>
            </tr>
            <tr>
                <td>Discounts</td>
                <td><?if($discount > 0){?><a href="reports_discounts.php">$<?=number_format($discount,2,'.',',')?></a><?}else{?>$0.00<?}?></td>
            </tr>
            <?if($expense > 0){?>
            <tr>
                <td>Expenses</td>
                <td>$<?=number_format($expense,2,'.',',')?></td>
            </tr>
            <?}?>
            <tr>
                <td><a href="reports_top_products.php"><?if($numDaysReported > 1){?>Sales<?}else{?>Sales of the day<?}?></a></td>
                <td></td>
            </tr>
        </table>
    </div>

    <div id="mobileCalendar"><?include '_calendar_mobile_tpl.php'?></div>
    
<?php if(!empty($aCategories)){?>
<?php if(!empty($aChartByType) or !empty($aChartByVendors)){?> 
    
    <script src="js/RGraph/libraries/RGraph.common.core.js"></script>
    <script src="js/RGraph/libraries/RGraph.common.dynamic.js"></script>
    <script src="js/RGraph/libraries/RGraph.common.tooltips.js"></script>
    <script src="js/RGraph/libraries/RGraph.pie.js"></script>
    <style>
    .RGraph_tooltip {
        font-size: 16pt !important;
        font-weight: bold;
        text-align: center;
        padding: 15px;
    }
</style>
<section class="tab-block-1 for-desktop" style="float:left;max-width:100%">
<div class="tab-block">
    <ul class="tab-default clearfix" role="tablist">
        <?php foreach($aCategories as $k=>$cat){?>
        <li role="presentation" <?php if($k==0) echo 'class="active"'?>><a href="#tab-<?=$cat['id']?>" aria-controls="tab-<?=$cat['id']?>" role="tab" data-toggle="tab"><?=$cat['name']?></a></li>
        <?php }?>
        <li role="presentation"><a href="#tab-total" aria-controls="tab-total" role="tab" data-toggle="tab">All</a></li>
    </ul>
    <div class="tab-content">
        <?php foreach($aCategories as $k=>$cat){?>
        <div role="tabpanel" class="tab-pane<?php if($k==0) echo ' active'?>" id="tab-<?=$cat['id']?>">
            <div class="table-responsive">
    <?php if($cat['measure_type'] == 1){?>        
                
            <?php
//get type chart
$aChartByType = $oOrder->getSalesByTypes($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to'], $cat['id']);
if(!empty($aChartByType)){
    //chart types
    $chartTotal = 0;
    foreach($aChartByType as $type){
        $chartTotal+=$type['amt'];
    }
    $aChartData = $aChartLabels = $aChartColors = $aChartTooltips = array();
    foreach($aChartByType as $type){
        $aChartData[] = $type['amt'];
        $sChartData = implode(',', $aChartData);
        
        $aChartLabels[] = "'".$aMedsCategories[$type['meds_type']]['name'].'\n$'.number_format($type['amt'],2,'.',',')."'";
        $sChartLabels = implode(',', $aChartLabels);
        
        $aChartColors[] = "'".$aMedsCategories[$type['meds_type']]['color']."'";
        $sChartColors = implode(',', $aChartColors);
        if($chartTotal){
            $percent = round($type['amt']/$chartTotal*100, 2);
        }else{
            $percent = 0;
        }
        $aChartTooltips[] = "'".$aMedsCategories[$type['meds_type']]['name'].' '.number_format($percent,2,'.',',')."%'";
        $sChartTooltips = implode(',', $aChartTooltips);
        
    }
?>
    <canvas id="sales<?=$cat['id']?>" width="600" height="350" style="margin-top:-50px;">[No canvas support]</canvas>
    <script>
        $(document).ready(function(){
            var pie = new RGraph.Pie({
            id: 'sales<?=$cat['id']?>',
            data: [<?=$sChartData?>],
            options: {
                gutterLeft: 0,
                gutterRight: 0,
                linewidth: 0,
                strokestyle: 'rgba(0,0,0,0)',
                tooltips: [<?=$sChartTooltips?>],
                labels: [<?=$sChartLabels?>],
                colors: [<?=$sChartColors?>],
                variant: 'pie3d',
                radius: 80,
                labelsSticksList: true,
                labelsSticksColors: [<?=$sChartColors?>],
                shadowOffsety: 5,
                shadowColor: '#aaa',
                
                textAccessible: false
            }
        }).draw();
        });
</script>
<?}?>

    <?php }else{?>


            <?php
//get vendor chart
$aChartByVendors = $oOrder->getSalesByVendors($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to'], $cat['id']);
if(!empty($aChartByVendors)){
    //chart vendors
    $chartVendorsTotal = 0;
    foreach($aChartByVendors as $vendor){
        $chartVendorsTotal+=$vendor['amt'];
    }
    $aChartVendorsColors = $aVendorsColors;
    $sChartVendorsColors = implode(',', $aChartVendorsColors);
    $aChartVendorsData = $aChartVendorsLabels = $aChartVendorsTooltips = array();
    foreach($aChartByVendors as $vendor){
        $aChartVendorsData[] = $vendor['amt'];
        $sChartVendorsData = implode(',', $aChartVendorsData);
        
        $aChartVendorsLabels[] = "'".$vendor['name']."'";
        $sChartVendorsLabels = implode(',', $aChartVendorsLabels);
        
        
        if($chartVendorsTotal){
            $percent = @round($vendor['amt']/$chartVendorsTotal*100, 2);
        }else{
            $percent = 0;
        }
        $aChartVendorsTooltips[] = "'".$vendor['name'].' '.number_format($percent,2,'.',',')."%'";
        $sChartVendorsTooltips = implode(',', $aChartVendorsTooltips);
        
    }
?>
<canvas id="vendorsSales<?=$cat['id']?>" width="800" height="350" style="margin-left:-50px;">[No canvas support]</canvas>    
<script>
        $(document).ready(function(){
            var pie = new RGraph.Pie({
            id: 'vendorsSales<?=$cat['id']?>',
            data: [<?=$sChartVendorsData?>],
            options: {
                gutterLeft: 0,
                gutterRight: 0,
                linewidth: 0,
                strokestyle: 'rgba(0,0,0,0)',
                tooltips: [<?=$sChartVendorsTooltips?>],
                labels: [<?=$sChartVendorsLabels?>],
                colors: [<?=$sChartVendorsColors?>],
                variant: 'pie3d',
                radius: 80,
                labelsSticksList: true,
                labelsSticksColors: [<?=$sChartVendorsColors?>],
                shadowOffsety: 5,
                shadowColor: '#aaa',
                
                textAccessible: false
            }
        }).draw();
        });
</script> 
<?}?>  

    <?php }?>

            </div>
        </div>
        <?php }?>
        
        <div role="tabpanel" class="tab-pane" id="tab-total">
        <?php
        //chart by categories
        if(!empty($aChartByCategories)){
            $chartCategoriesTotal = 0;
            foreach($aChartByCategories as $c){
                $chartCategoriesTotal+=$c['amt'];
            }
            $aChartCategoriesColors = $aVendorsColors;
            $sChartCategoriesColors = implode(',', $aChartCategoriesColors);
            $aChartCategoriesData = $aChartCategoriesLabels = $aChartCategoriesTooltips = array();
            foreach($aChartByCategories as $c){
                $aChartCategoriesData[] = $c['amt'];
                $sChartCategoriesData = implode(',', $aChartCategoriesData);
                
                $aChartCategoriesLabels[] = "'".$c['name'].'\n$'.number_format($c['amt'],2,'.',',')."'";
                $sChartCategoriesLabels = implode(',', $aChartCategoriesLabels);
                
                if($chartCategoriesTotal){
                    $percent = round($c['amt']/$chartCategoriesTotal*100, 2);
                }else{
                    $percent = 0;
                }
                $aChartCategoriesTooltips[] = "'".$c['name'].' '.number_format($percent,2,'.',',')."%'";
                $sChartCategoriesTooltips = implode(',', $aChartCategoriesTooltips);
                
            }?>
            <canvas id="categoriesSales" width="800" height="350" style="margin-left:-50px;">[No canvas support]</canvas> 
            <script>
                $(document).ready(function(){
                    var pie = new RGraph.Pie({
                    id: 'categoriesSales',
                    data: [<?=$sChartCategoriesData?>],
                    options: {
                        gutterLeft: 0,
                        gutterRight: 0,
                        linewidth: 0,
                        strokestyle: 'rgba(0,0,0,0)',
                        tooltips: [<?=$sChartCategoriesTooltips?>],
                        labels: [<?=$sChartCategoriesLabels?>],
                        colors: [<?=$sChartCategoriesColors?>],
                        variant: 'pie3d',
                        radius: 80,
                        labelsSticksList: true,
                        labelsSticksColors: [<?=$sChartCategoriesColors?>],
                        shadowOffsety: 5,
                        shadowColor: '#aaa',

                        textAccessible: false
                    }
                }).draw();
                });
        </script>
        <?php }?>
        </div>
        
        
    </div>
</div>
</section>

<section class="tab-block-1 for-mobile">
    
    <link rel="stylesheet" type="text/css" href="css/chart.css">
    <script src="js/d3.v3.min.js" charset="utf-8"></script>
    <script src="js/c3.js"></script>
    
    <?php foreach($aCategories as $k=>$cat){?>
    <div class="mobilechart" id="chartblock<?=$cat['id']?>">
        <?php if($cat['measure_type'] == 1){?>
        <?$aChartByType = $oOrder->getSalesByTypes($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to'], $cat['id']);?>
        <div class="chart chart<?=$cat['id']?>"></div>
        <div class="legend legend<?=$cat['id']?>">
            <?php if(!empty($aChartByType)) foreach($aChartByType as $type){?>
                <div><span class="color_code"></span><span><?=@$aMedsCategories[$type['meds_type']]['name']?><br/>$<?=number_format($type['amt'],2,'.',',')?></span></div>
            <?php }?>
        </div>
        <script>
		var chart = c3.generate({
			bindto: d3.select('.chart<?=$cat['id']?>'),
			data: {
				columns: [
                                    <?php if(!empty($aChartByType)) foreach($aChartByType as $type){?>
                                         ["<?=@$aMedsCategories[$type['meds_type']]['name']?>", <?=$type['amt']?>],            
                                    <?php }?>
				],
				colors: {
                                    <?php if(!empty($aChartByType)) foreach($aChartByType as $type){?>
					'<?=@$aMedsCategories[$type['meds_type']]['name']?>': '<?=@$aMedsCategories[$type['meds_type']]['color']?>',
                                    <?php }?>
				},
				type : 'donut',
				order: null // set null to disable sort of data. desc is the default.
			},
			donut: {
				width: 40,
				label: {
					show: false
				}
			},
			legend: {
				show: false
			},
			tooltip: {
				show: false
			},
			size: {
                            height: 200
                        },
		});
		d3.select('.legend<?=$cat['id']?>').selectAll('div').data([<?php if(!empty($aChartByType)) foreach($aChartByType as $type){?>'<?=@$aMedsCategories[$type['meds_type']]['name']?>',<?php }?>])
		.each(function (id) {
			var $this = d3.select(this);
			$this.attr('data-id', function (id) { return id; })
			.select('.color_code').style('background-color', chart.color(id))
		})
		
	</script>
        <?}else{?>
            <?$aChartByVendors = $oOrder->getSalesByVendors($_SESSION[CLIENT_ID]['from'], $_SESSION[CLIENT_ID]['to'], $cat['id']);?>
        <div class="chart chart<?=$cat['id']?>"></div>
        <div class="legend legend<?=$cat['id']?>">
            <?php if(!empty($aChartByVendors)) foreach($aChartByVendors as $vendor){?>
                <div><span class="color_code"></span><span><?=@$vendor['name']?><br/>$<?=number_format($vendor['amt'],2,'.',',')?></span></div>
            <?php }?>
        </div>
        <script>
		var chart = c3.generate({
			bindto: d3.select('.chart<?=$cat['id']?>'),
			data: {
				columns: [
                                    <?php if(!empty($aChartByVendors)) foreach($aChartByVendors as $vendor){?>
                                         ["<?=@$vendor['name']?>", <?=$vendor['amt']?>],            
                                    <?php }?>
				],
				colors: {
                                    <?php if(!empty($aChartVendorsColors)) foreach($aChartVendorsColors as $k=>$color){?>
					'color<?=$k?>': <?=@$color?>,
                                    <?php }?>
				},
				type : 'donut',
				order: null // set null to disable sort of data. desc is the default.
			},
			donut: {
				width: 40,
				label: {
					show: false
				}
			},
			legend: {
				show: false
			},
			tooltip: {
				show: false
			},
			size: {
		        height: 200
		    },
		});
		d3.select('.legend<?=$cat['id']?>').selectAll('div').data([<?php if(!empty($aChartByVendors)) foreach($aChartByVendors as $vendor){?>'<?=@$vendor['name']?>',<?php }?>])
		.each(function (id) {
			var $this = d3.select(this);
			$this.attr('data-id', function (id) { return id; })
			.select('.color_code').style('background-color', chart.color(id))
		})
		
	</script>
        <?}?>
    </div>
    
    
    <?php }?>
    <div class="mobilechart" id="chartbycategory">
        <div class="chart chartcat"></div>
        <div class="legend legendcat">
            <?php if(!empty($aChartByCategories)) foreach($aChartByCategories as $c){?>
                <div><span class="color_code"></span><span><?=@$c['name']?><br/>$<?=number_format($c['amt'],2,'.',',')?></span></div>
            <?php }?>
        </div>
        <script>
		var chart = c3.generate({
			bindto: d3.select('.chartcat'),
			data: {
				columns: [
                                    <?php if(!empty($aChartByCategories)) foreach($aChartByCategories as $c){?>
                                         ["<?=@$c['name']?>", <?=$c['amt']?>],            
                                    <?php }?>
				],
				colors: {
                                    <?php if(!empty($aVendorsColors)) foreach($aVendorsColors as $k=>$color){?>
					'color<?=$k?>': <?=@$color?>,
                                    <?php }?>
				},
				type : 'donut',
				order: null // set null to disable sort of data. desc is the default.
			},
			donut: {
				width: 40,
				label: {
					show: false
				}
			},
			legend: {
				show: false
			},
			tooltip: {
				show: false
			},
			size: {
		        height: 200
		    },
		});
		d3.select('.legendcat').selectAll('div').data([<?php if(!empty($aChartByCategories)) foreach($aChartByCategories as $c){?>'<?=@$c['name']?>',<?php }?>])
		.each(function (id) {
			var $this = d3.select(this);
			$this.attr('data-id', function (id) { return id; })
			.select('.color_code').style('background-color', chart.color(id))
		})
		
	</script>
    </div>
    <div class="select-block-1 select-title-page" style="margin-top:20px;">
            <div class="select-1">	
                <select class="" style="width:100%;" id="chartSelect">
                    <?php foreach($aCategories as $k=>$cat){?>
                    <option value="#chartblock<?=$cat['id']?>"><?=$cat['name']?></option>        
                    <?}?>
                    <option value="#chartbycategory">All</option>
                </select>
            </div>
    </div>
    
</section>

    <div style="clear:both"></div>
<?}?>
<?php }?>
    
    <?php if(isset($aPettyCashAdmin)){
    $total_payouts = 0;
    $total_gross_sales = 0;
    $total_cash_submitted = 0;
    foreach($aPettyCashAdmin as $reporter){
            $total_pc = 0;
            foreach($reporter as $pc){
                $total_pc+=$pc['amount'];
            }
            $total_payouts+=$total_pc;
            $user_total_gross_sales = (isset($reporter[0]['totalGross']) ? $reporter[0]['totalGross'] : 0) - $total_pc;
            $total_gross_sales+= $user_total_gross_sales;
            $user_total_cash_submitted = ($reporter[0]['realCash'] > 0 ? $reporter[0]['realCash'] : ($aPettyCashAdminTotal[$reporter[0]['user_id']] - $total_pc));
            $total_cash_submitted+= $user_total_cash_submitted;
            $user_difference = ((isset($reporter[0]['totalGross']) ? $reporter[0]['totalGross'] : 0) - ($reporter[0]['realCash'] > 0 ? $reporter[0]['realCash']+ $total_pc : $aPettyCashAdminTotal[$reporter[0]['user_id']]));
           }    
    ?>
    <h3><a href="payouts.php">PAYOUTS</a></h3>
    <?php
    if($total_cash_submitted <= 0){
        $total_cash_submitted = $gross;
	$total_difference = -$total_payouts;
    }else{
	$total_difference = $gross-$total_cash_submitted-$total_payouts;
    }
    ?>
    <div class="table-responsive">
        <div class="table-sl1 pettycash">
            <table width="100%">
                <tr>
                    <td colspan="2"><b>TOTAL PAYOUTS:</b></td>
                    <td><b>$<?=number_format($total_payouts,2,'.',',')?></b></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2"><b>TOTAL GROSS SALES:</b></td>
                    <td><b>$<?=number_format($gross,2,'.',',')?></b></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2"><b>TOTAL CASH SUBMITTED:</b></td>
                    <td><b>$<?=number_format($total_cash_submitted,2,'.',',')?></b></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2"><b>TOTAL DIFFERENCE:</b></td>
                    <td><b<?if($total_difference > 0) echo ' style="color:#f00"';elseif($total_difference < 0) echo ' style="color:#00f"';?>>$<?=number_format($total_difference,2,'.',',')?></b></td>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>
    <?php }?>
</section>
    
    
    
   
</section>

 <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
<div class="dataGrid">
        <a href="reports_remove_data.php" style="float:right;">Remove Data</a><br />
</div>
<?}?>

<?include '_footer_tpl.php'?>