<html>
<head>
    <style>
    body{width:240px;}
    .content{width:240px;}
    html, body {
        font-family: Helvetica, sans-serif;        
        margin:0;
        padding:0;
    }
    td{
        font-size:9pt;
        vertical-align:top;
    }
    </style>
    <script>
    function printData(){
        window.print();
        //window.close();
    }
    </script>
</head>
<body onload="printData()">
    <div class="content">
        <table width="100%" cellspasing="0" cellpadding="0">
            <tr><th align="center" colspan="2"><?=SITE_NAME?></th></tr>
            <tr><td align="center" colspan="2"><?=$dt?></td></tr>
            <tr><td align="center" colspan="2"><hr /></td></tr>
            <?php 
            $total = 0;
            $i = 0;
            foreach($_SESSION[CLIENT_ID]['cart'] as $number=>$item){
                $nameNumChar = strlen($item['name']);
                if($item['modifiers']){
                    foreach($item['modifiers'] as  $k => $mod){
                        foreach($mod as $altname=>$alt){
                            if($alt['qty'] == 0){
                                continue;
                            }
                            
                            //qty
                            $qt = '';
                            if(isset($alt['params'])){
                                foreach($alt['params'] as $p=>$param){ 
                                    if($param['qty'] > 0){
                                        $qt.= ($param['qty'].' '.$param['name'].'  ');                                                
                                    }
                                }
                                $qt = trim($qt);
                            }else{
                                $qt.=$alt['qty'].' ';
                                if($altname == 'default') {
                                   $qt.=$alt['name'];
                                }else{
                                    if($altname == 'other'){
                                        $qt.=$alt['name'];
                                    }else{
                                        $qt.=$aAlternativeWeights[$altname]['name'];
                                    }
                                }
                            }
                            $qtNumChar = strlen($qt);
                            //total per row
                            $total_row=$alt['qty']*($alt['price']-$alt['discount_amt']-($alt['comp'] ? $alt['price'] : 0));
                            
                            $sTotalRow = '$'.number_format($total_row, 2, '.', ',');
                            $totalRowNumChar = strlen($sTotalRow);
                            
                            $spaceLength = 37 - $nameNumChar - $qtNumChar - $totalRowNumChar;
                            $space = '';
                            for($i = 0; $i<=$spaceLength; $i++){
                                $space.='.';
                            }
                            
                            $total+=$total_row;
                            
                            
            ?>
            <tr>
                <td align="left"><?=$item['name']?> (<?=$qt?>)</td> 
                <td align="right"><?=$sTotalRow?></td>
            </tr>
            <?$i++;}}}}
            $sTotal = '$'.number_format($total, 2, '.', ',');
            ?>
            
            <?php if(!empty($_SESSION[CLIENT_ID]['order_discount_amt'])){
                $subSpaceLength = 40 - 8 - strlen($sTotal);
                $sub_space = '';
                for($i = 0; $i<=$subSpaceLength; $i++){
                    $sub_space.='.';
                }
                
                $sDiscount = '$'.number_format($_SESSION[CLIENT_ID]['order_discount_amt'], 2, '.', ',');
                $discSpaceLength = 40 - 8 - strlen($sDiscount);
                $disc_space = '';
                for($i = 0; $i<=$discSpaceLength; $i++){
                    $disc_space.='.';
                }
                ?>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr>
                <td align="left"><b>Subtotal</b></td>
                <td align="right"><b><?=$sTotal?></b></td>
            </tr>
            <tr>
                <td align="left"><b>Discount</b></td>
                <td align="right"><b><?=$sDiscount?></b></td>
            </tr>
            <?php }?>
            <tr><td align="center" colspan="2"><hr /></td></tr>
            <?php 
            $totalVal = round($total-@$_SESSION[CLIENT_ID]['order_discount_amt'], 2);
            $sTotalVal = '$'.number_format($totalVal,2,'.',',');
            $totalValSpaceLength = 20 - 6 - strlen($sTotalVal);
            $totalval_space = '';
            for($i = 0; $i<=$totalValSpaceLength; $i++){
                $totalval_space.='.';
            }
            $change = $cash - $totalVal;
            $changeString = '$'.number_format($change,2,'.',',');
            ?>
            <tr>
                <td align="left"><b>TOTAL</b></td>
                <td align="right"><b><?=$sTotalVal?></b></td>
            </tr>
            
            <tr>
                <td align="left">Cash Given</td>
                <td align="right"><?=$cashString?></td>
            </tr>
            <tr>
                <td align="left">Change</td>
                <td align="right"><?=$changeString?></td>
            </tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr>
                <td align="center" colspan="2">Thank You!</td>
            </tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr><td colspan="2" align="center">---</td></tr>
        </table>
    </div>
</body>
</html>