<?include '_header_tpl.php'?>
<style>
.table-2 table tr:last-child td {
    font-weight: normal;
}
.table-2 td span{
    text-transform: uppercase;
}
.table-2 .modal-dialog .form-control{
    display:inline;
}
</style>
<script>
$(document).ready(function(){
    $('.dsc').each(function(){
        var r = $(this);
        r.click(function(){            
            $(this).parent().find('input[type="radio"]').click(); 
        });
    });
    $('.app').each(function(){
        var r = $(this);
        r.click(function(){
            $('.dsc').val('');
        });
    })
});
</script>
<!-- start content title-page -->
<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Employees List</h2>
    </section>
</section>
<!-- stop content title-page -->

<section class="content">
        <button class="button" onclick="parent.location='edit_employee.php'">Add New</button>
        <button class="button" onclick="parent.location='employees_shifts.php'">Shifts</button>
</section>

    <?if(!empty($error)){?>
    <section class="content"><p class="error" style="color:#f00"><?=$error?></p></section>
    <?}?>
    
<section class="content">   

    
    <div class="table-responsive table-2 employees-list">
        <table>
            <tr>
                <th class="th-serial-number"></th>
                <th><font>Name</font></th>
                <th><font>Position</font></th>
                <th><font>Phone</font></th>
                <th><font>Email</font></th>
                <?if($_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1){?>
                <th>&nbsp;</th>
                <?}?>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            <?if(!empty($aUsers)){?>
                <?foreach($aUsers as $k=>$user){?>
            <tr>
                <td class="td-serial-number"><?=$k+1?></td>
                <td class="td-name"><div class="cont-t3"><a href="edit_employee.php?id=<?=$user['id']?>"><span><?=$user['firstname']?> <?=$user['lastname']?></span></a></div></td>
                <td class="td-position"><div class="cont-t3"><span><?=$aUserRoles[$user['role']]?></span></div></td>
                <td class="td-phone">
                        <div class="cont-t3">
                                <span class="for-desktop"><?=$user['phone']?></span>

                                        <!-- start modal for mobile -->
                                        <div class="modal-block for-mobile">
                                                <div class="modal-button" data-toggle="modal" data-target="#phone<?=$user['id']?>">
                                                        <img src="images/icon_pp.png" alt="" />
                                                        VIEW
                                                </div>
                                                <div class="modal fade modal-name-1p" tabindex="-1" role="dialog" id="phone<?=$user['id']?>">
                                                  <div class="modal-dialog">
                                                        <div class="modal-content">
                                                                <div class="pop-close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></div>
                                                                <div class="pop-name"><?=$user['firstname']?> <?=$user['lastname']?></div>
                                                                <div class="pop-content">phone</div>
                                                                <div class="pop-content content-phone"><?=$user['phone']?></div>
                                                        </div>
                                                  </div>
                                                </div>
                                        </div>
                                        <!-- stop modal for mobile -->							

                        </div>
                </td>
                <td class="td-email">
                        <div class="cont-t3">
                                <span class="email-span for-desktop"><?=$user['email']?></span>

                                        <!-- start modal for mobile -->
                                        <div class="modal-block for-mobile">
                                                <div class="modal-button" data-toggle="modal" data-target="#email<?=$user['id']?>">
                                                        <img src="images/icon_pp.png" alt="" />
                                                        VIEW
                                                </div>
                                                <div class="modal fade modal-name-1e" tabindex="-1" role="dialog" id="email<?=$user['id']?>">
                                                  <div class="modal-dialog">
                                                        <div class="modal-content">
                                                                <div class="pop-close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></div>
                                                                <div class="pop-name"><?=$user['firstname']?> <?=$user['lastname']?></div>
                                                                <div class="pop-content">Email</div>
                                                                <div class="pop-content content-email"><?=$user['email']?></div>
                                                        </div>
                                                  </div>
                                                </div>
                                        </div>
                                        <!-- stop modal for mobile -->						

                        </div>
                </td>
                <?if($_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1){?>
                <td>
                    <?php if($user['id'] != 1){?>
                    <div class="cont-t3"><a href="#" data-toggle="modal" data-target="#makePatient<?=$user['id']?>"><span><?if($user['is_patient']) echo 'Update Discount'; else echo 'Employee Discount'?></span></a></div>
                    <div class="modal fade" id="makePatient<?=$user['id']?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="" method="post" class="singleForm">
                                    <input type="hidden" name="employee" value="<?=$user['id']?>"/>        
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="myModalLabel"><?if($user['is_patient']) echo 'Update Discount'; else echo 'Employee Discount'?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <div class="box-input">
                                                <input type="radio" name="apply_purchase_price" value="1" class="app" <?php if($user['apply_purchase_price']) echo 'checked'?> /> <label>Apply purchase price</label></p>
                                            </div>
                                        </div> 
                                        <div class="form-group">
                                            <div class="box-input">
                                                <input type="radio" name="apply_purchase_price" value="0" <?php if(!$user['apply_purchase_price']) echo 'checked'?>/> <label>Discount</label> <input type="text" name="employee_discount" value="<?=!empty($user['employee_discount']) ? $user['employee_discount'] : ''?>" class="form-control dsc" style="width:70px"/> %
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>
                            </div>                            
                        </div>
                    </div>
                    <?php }?>
                </td>
                <?}?>
                <?if($user['id'] > 1){?>
                <td class="td-activ"><div class="cont-t3"><a href="employees.php?id=<?=$user['id']?>&amp;active=<?=$user['active'] ? 0 : 1?>">
                    <?if($user['active']){?>
                    <span>active</span>
                    <?}else{?>
                    <span>inactive</span>
                    <?}?>
                </a></div></td>
                <td class="td-delite"><div class="cont-t3"><a href="employees.php?id=<?=$user['id']?>&amp;delete=1>" title="delete" onclick="return confirm('Are you sure you want to delete employee \'<?=$user['firstname']?> <?=$user['lastname']?>\'?')"><span><font><i class="fa fa-times"></i></font><font>delete</font></span></a></div></td>
                <?}else{?>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <?}?>
            </tr>
                <?}?>
            <?}?>
        </table>
    </div>
</section>

<?include '_footer_tpl.php'?>