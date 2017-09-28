<?include '_header_tpl.php'?>

<?include '_reports_list_tpl.php'?>

<style>
    .form-group{
        width:200px;
        float:left;
        margin-right:10px;
    }
</style>
<!-- start content title-page -->
<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Expenses</h2>
    </section>
</section>
<!-- stop content title-page -->

<section class="content">
    <section class="content-header">
      <h2>TOTAL: $<?=number_format($totalExpense,2,'.',',')?> &nbsp;&nbsp;|&nbsp;&nbsp; Per Day: $<?=number_format($perDay,2,'.',',')?></h2>
    </section>
</section>

<section class="content">
    <div class="table-responsive table-3 category-table">
        <table>
            <tr>
                <th class="th-serial-number"></th>
                <th><font>Name</font></th>                   
                <th><font>Amount</font></th>
                <th><font>Period</font></th>
                <th class="th-delite"></th>
            </tr>
            <?if(!empty($aExpense)){?>
                <?foreach($aExpense as $k=>$c){?>
            <tr>
                <td class="td-serial-number"><?=$k+1?></td>                    
                <td class="td-name"><div class="cont-t3"><a href="reports_expense.php?id=<?=$c['id']?>" title=""><span class="catn"><?=$c['name']?></span></a></div></td>
                <td>
                    <div class="cont-t3"><span>$<?=number_format($c['amount'],2,'.',',')?></span></div>
                </td> 
                <td>
                    <div class="cont-t3"><span>
                        <?php
                            if(isset($c['period'])){
                                if($c['period'] == 1){
                                    echo 'Monthly';
                                }elseif($c['period'] == 4){
                                     echo 'Weekly';
                                }elseif($c['period'] == 30){
                                     echo 'Daily';
                                }
                            }else{
                                echo 'Monthly';
                            }
                        ?>
                    </span></div>
                </td>
                <td class="td-delite">  
                    <div class="cont-t3">
                        <a href="reports_expense.php?del=<?=$c['id']?>" title="delete" onclick="return confirm('Are you sure you want to delete \'<?=$c['name']?>\'')"><span><font><i class="fa fa-times"></i></font><font>delete</font></span></a>
                    </div>  
                </td>
            </tr>
                <?}?>
            <?}?>
        </table>
    </div>
    <br />
    <form action="" method="post">
        <input type="hidden" name="amount_sent" value="1" /> 
        <div class="form-group">
                <label>Name</label>
                <div class="box-input"><input type="text" class="form-control" name="expense[name]" value="<?=isset($aExpenseItem['name']) ? $aExpenseItem['name'] : ''?>"/></div>
        </div>

        <div class="form-group">
                <label>Amount</label>
                <div class="box-input"><input type="text" class="form-control" name="expense[amount]" value="<?=isset($aExpenseItem['amount']) ? $aExpenseItem['amount'] : ''?>"/></div>
        </div>
        
        <div class="form-group" style="width:300px">
                <label>Period</label>
                <div class="box-input">
                    <input type="radio" class="form-control" name="expense[period]" value="30" <?=isset($aExpenseItem['period']) and $aExpenseItem['period'] == '30' ? 'checked' : ''?>/> Daily &nbsp;&nbsp;
                    <input type="radio" class="form-control" name="expense[period]" value="4" <?=isset($aExpenseItem['period']) and $aExpenseItem['period'] == '4' ? 'checked' : ''?>/> Weekly &nbsp;&nbsp;
                    <input type="radio" class="form-control" name="expense[period]" value="1" <?=((isset($aExpenseItem['period']) and $aExpenseItem['period'] == '1') or !isset($aExpenseItem['period'])) ? 'checked' : ''?>/> Monthly
                </div>
        </div>
                
        <div class="clearfix"></div> 

        <div class="checkou-button">
            <input type="submit" class="button" value="Save">
        </div> 
    </form>
</section>

<?include '_footer_tpl.php'?>