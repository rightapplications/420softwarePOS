<?include '_header_tpl.php'?>
<style>
    .form-group{
        width:160px;
        float:left;
        margin-right:10px;
    }
    .jq-selectbox.opened {z-index: 1050!important;}
    .jq-selectbox.opened .dropdown{height:300px!important;}
</style>
<!-- start content title-page -->
<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Set Prices</h2>
    </section>
</section>
<!-- stop content title-page -->

<section class="content">
    <section class="search">
        <div class="search-content">
            
            <div class="search-form">
                <div class="input-submit">
                        <input type="button" class="form-control" value="<< Back" onclick="parent.location='<?=!empty($_SESSION[CLIENT_ID]['return_page']) ? $_SESSION[CLIENT_ID]['return_page'] : 'inventory.php'?>'" style="float:left;width:200px;"/>
                </div>
            </div>
        </div>
    </section>
</section>

<section class="content">
    
    <div class="table-responsive table-3 category-table">
            <table>
                <tr>
                    <th class="th-serial-number"></th>
                    <th class="th-name"><font>8th</font></th>
                    <th><font>Gram</font></th>
                    <th><font>1/2 8th</font></th>
                    <th><font>2 Grams</font></th>
                    <th><font>4 Grams</font></th>
                    <th><font>5 Grams</font></th>
                    <th><font>1/4</font></th>
                    <th><font>1/2</font></th>
                    <th><font>Oz</font></th>
                    <th class="th-delite"></th>
                </tr>
                <?if(!empty($aPrices)){?>
                    <?foreach($aPrices as $k=>$c){?>
                <tr>
                    <td class="td-serial-number"><?=$k+1?></td>
                    <td class="td-name"><div class="cont-t3"><a href="inventory_set_prices.php?id=<?=$c['id']?>" title=""><span class="catn">$<?=number_format($c['eighth'],2,'.',',')?></span></a></div></td>
                    <td>
                        <div class="cont-t3"><span>$<?=number_format($c['gram'],2,'.',',')?></span></div>
                    </td>
                    <td>
                        <div class="cont-t3"><span>$<?=number_format($c['halfeighth'],2,'.',',')?></span></div>
                    </td>
                    <td>
                        <div class="cont-t3"><span>$<?=number_format($c['twograms'],2,'.',',')?></span></div>
                    </td>
                    <td>
                        <div class="cont-t3"><span>$<?=number_format($c['fourgrams'],2,'.',',')?></span></div>
                    </td>
                    <td>
                        <div class="cont-t3"><span>$<?=number_format($c['fivegrams'],2,'.',',')?></span></div>
                    </td>
                    <td>
                        <div class="cont-t3"><span>$<?=number_format($c['fourth'],2,'.',',')?></span></div>
                    </td> 
                    <td>
                        <div class="cont-t3"><span>$<?=number_format($c['half'],2,'.',',')?></span></div>
                    </td>
                    <td>
                        <div class="cont-t3"><span>$<?=number_format($c['oz'],2,'.',',')?></span></div>
                    </td>
                    <td class="td-delite">  
                        <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1 or !empty($_SESSION[CLIENT_ID]['user_superclinic']['set_prices'])){?>
                        <div class="cont-t3">
                            <a href="inventory_set_prices.php?del=<?=$c['id']?>" title="delete" onclick="return confirm('Are you sure you want to delete the set')"><span><font><i class="fa fa-times"></i></font><font>delete</font></span></a>
                        </div>  
                        <?}?>
                    </td>
                </tr>
                    <?}?>
                <?}?>
            </table>
        </div>
    <br />
    <form action="" method="post">
        <input type="hidden" name="prices_sent" value="1" />        	  
            
                <div class="form-group">
                        <label>8th</label>
                        <div class="box-input"><input type="text" class="form-control" name="price[eighth]" value="<?=isset($aPrice['eighth']) ? $aPrice['eighth'] : ''?>"/></div>
                </div>            
            
                <div class="form-group">
                        <label>Gram</label>
                        <div class="box-input"><input type="text" class="form-control" name="price[gram]" value="<?=isset($aPrice['gram']) ? $aPrice['gram'] : ''?>"/></div>
                </div>
        
                <div class="form-group">
                        <label>1/2 8th</label>
                        <div class="box-input"><input type="text" class="form-control" name="price[halfeighth]" value="<?=isset($aPrice['halfeighth']) ? $aPrice['halfeighth'] : ''?>"/></div>
                </div>
        
                <div class="form-group">
                        <label>2 Grams</label>
                        <div class="box-input"><input type="text" class="form-control" name="price[twograms]" value="<?=isset($aPrice['twograms']) ? $aPrice['twograms'] : ''?>"/></div>
                </div>
        
                <div class="form-group">
                        <label>4 Grams (1/8)</label>
                        <div class="box-input"><input type="text" class="form-control" name="price[fourgrams]" value="<?=isset($aPrice['fourgrams']) ? $aPrice['fourgrams'] : ''?>"/></div>
                </div>
        
                <div class="form-group">
                        <label>5 Grams</label>
                        <div class="box-input"><input type="text" class="form-control" name="price[fivegrams]" value="<?=isset($aPrice['fivegrams']) ? $aPrice['fivegrams'] : ''?>"/></div>
                </div>
            
                <div class="form-group">
                        <label>1/4</label>
                        <div class="box-input"><input type="text" class="form-control" name="price[fourth]" value="<?=isset($aPrice['fourth']) ? $aPrice['fourth'] : ''?>"/></div>
                </div>
        
                <div class="form-group">
                        <label>1/2</label>
                        <div class="box-input"><input type="text" class="form-control" name="price[half]" value="<?=isset($aPrice['half']) ? $aPrice['half'] : ''?>"/></div>
                </div>
        
                <div class="form-group">
                        <label>Oz</label>
                        <div class="box-input"><input type="text" class="form-control" name="price[oz]" value="<?=isset($aPrice['oz']) ? $aPrice['oz'] : ''?>"/></div>
                </div>
                
            <div class="clearfix"></div> 
          
            <div class="checkou-button">
                <input type="submit" class="button" value="Save">
            </div>   
        
    </form>
 
    
</section>

<section class="content">
    <section class="content-header title-page">
      <h2>Set max values for rounding</h2>
    </section>
</section>

<section class="content">
    <form action="" method="post">
        <input type="hidden" name="round_sent" value="1" /> 

            <div class="form-group">
                    <label>Gram</label>
                    <div class="box-input"><input type="text" class="form-control" name="round[gram]" value="<?=isset($aRound['gram']) ? $aRound['gram'] : ''?>"/></div>
            </div>
        
            <div class="form-group">
                    <label>1/2 8th</label>
                    <div class="box-input"><input type="text" class="form-control" name="round[halfeighth]" value="<?=isset($aRound['halfeighth']) ? $aRound['halfeighth'] : ''?>"/></div>
            </div>
        
            <div class="form-group">
                    <label>2 Grams</label>
                    <div class="box-input"><input type="text" class="form-control" name="round[twograms]" value="<?=isset($aRound['twograms']) ? $aRound['twograms'] : ''?>"/></div>
            </div>

            <div class="form-group">
                    <label>8th</label>
                    <div class="box-input"><input type="text" class="form-control" name="round[eighth]" value="<?=isset($aRound['eighth']) ? $aRound['eighth'] : ''?>"/></div>
            </div>
        
            <div class="form-group">
                    <label>4 Grams (1/8)</label>
                    <div class="box-input"><input type="text" class="form-control" name="round[fourgrams]" value="<?=isset($aRound['fourgrams']) ? $aRound['fourgrams'] : ''?>"/></div>
            </div>
        
            <div class="form-group">
                    <label>5 Grams</label>
                    <div class="box-input"><input type="text" class="form-control" name="round[fivegrams]" value="<?=isset($aRound['fivegrams']) ? $aRound['fivegrams'] : ''?>"/></div>
            </div>

            <div class="form-group">
                    <label>1/4</label>
                    <div class="box-input"><input type="text" class="form-control" name="round[fourth]" value="<?=isset($aRound['fourth']) ? $aRound['fourth'] : ''?>"/></div>
            </div>

            <div class="form-group">
                    <label>1/2</label>
                    <div class="box-input"><input type="text" class="form-control" name="round[half]" value="<?=isset($aRound['half']) ? $aRound['half'] : ''?>"/></div>
            </div>

            <div class="form-group">
                    <label>Oz</label>
                    <div class="box-input"><input type="text" class="form-control" name="round[oz]" value="<?=isset($aRound['oz']) ? $aRound['oz'] : ''?>"/></div>
            </div>
                
            <div class="form-group">
                    <label>Round Automatically</label>
                    <div class="box-input"><input type="checkbox" class="form-control" name="autoround" value="1" <?=$autoround ? 'checked' : ''?>/></div>
            </div>
            
            <div class="clearfix"></div> 
          
            <div class="checkou-button">
                <input type="submit" class="button" value="Save">
            </div>   
        
    </form>
</section>

<section class="content">
    <section class="content-header title-page">
      <h2>Highlights the product in red if not sold within the selected date</h2>
    </section>
</section>

<section class="content">
    <form action="" method="post">
        <div class="form-group">
                <label>Days</label>
                <div class="box-input">
                    <div class="select-block-1" style="width:100px; z-index:1050">	
                        <select name="inactive_time_frame" >
                            <?for($i=1; $i<=30;$i++){?>
                            <option value="<?=$i?>"<?if($i == $iTimeFrame) echo " selected"?>><?=$i?></option>
                            <?}?>
                        </select>
                    </div>
                </div>
        </div>
        <div class="clearfix"></div> 
          
        <div class="checkou-button">
            <input type="submit" class="button" value="Save">
        </div>
    </form>
</section>


<?include '_footer_tpl.php'?>